<?php

namespace App\Http\Controllers\Api\Asset;

use App\Enums\CommonStatusEnum;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PaginationRequest;
use App\Modules\Asset\Repositories\Interfaces\AssetDeliveryInterface;
use App\Modules\Asset\Repositories\Interfaces\AssetInterface;
use App\Modules\Asset\Repositories\Interfaces\AssetMaintenanceInterface;
use App\Modules\Asset\Requests\CreateAssetDeliveryRequest;
use App\Modules\Asset\Requests\CreateAssetMaintenanceRequest;
use App\Modules\Asset\Requests\CreateAssetRequest;
use App\Modules\Asset\Requests\DeleteAssetRequest;
use App\Modules\Asset\Requests\DetailAssetDeliveryRequest;
use App\Modules\Asset\Requests\DetailAssetMaintenanceRequest;
use App\Modules\Asset\Requests\DetailAssetRequest;
use App\Modules\Asset\Requests\ListAssetDeliveryRequest;
use App\Modules\Asset\Requests\ListAssetMaintenanceRequest;
use App\Modules\Asset\Requests\UpdateAssetRequest;
use App\Modules\Asset\Transformers\DetailAssetDeliveryHistoryTransformer;
use App\Modules\Asset\Transformers\DetailAssetMaintenanceTransformer;
use App\Modules\Asset\Transformers\DetailAssetTransformer;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Modules\Sequence\Repositories\Interfaces\SequenceInterface;
use App\Traits\MediaTrait;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;


/**
 *
 */
class AssetController extends ApiController
{
    use AuthorizesRequests;
    use MediaTrait;

    protected AssetInterface $assetRepo;
    protected AssetMaintenanceInterface $assetMaintenanceRepo;
    protected AssetDeliveryInterface $assetDeliveryRepo;
    protected SequenceInterface $sequenceRepo;
    protected RawMediaUploadInterface $rawMediaUploadRepo;
    const ASSET_COLLECTION_MEDIA = [
        RawMediaUploadCollectionConstants::ASSET_IMAGE
    ];

    use MediaTrait;

    /**
     * @param AssetInterface $asset
     * @param AssetDeliveryInterface $assetDelivery
     * @param AssetMaintenanceInterface $assetMaintenance
     * @param SequenceInterface $sequence
     * @param RawMediaUploadInterface $rawMediaUpload
     */
    public function __construct(
        AssetInterface            $asset,
        AssetDeliveryInterface    $assetDelivery,
        AssetMaintenanceInterface $assetMaintenance,
        SequenceInterface         $sequence,
        RawMediaUploadInterface   $rawMediaUpload,
    )
    {
        $this->assetRepo = $asset;
        $this->assetMaintenanceRepo = $assetMaintenance;
        $this->assetDeliveryRepo = $assetDelivery;
        $this->rawMediaUploadRepo = $rawMediaUpload;
        $this->sequenceRepo = $sequence;
    }

    /**
     *
     * @OA\Post(
     *      path="/api/asset/create",
     *      operationId="createAsset",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Create Asset ",
     *      description="Create Asset ",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Máy khắc laser UV YLP-UV-3C-S 3W"
     *                  ),
     *                   @OA\Property(
     *                       property="management_code",
     *                       type="string",
     *                       example="KA/KOF/201"
     *                   ),
     *                   @OA\Property(
     *                       property="management_unit",
     *                       type="string",
     *                       example="TÀU CCA GS"
     *                   ),
     *                  @OA\Property(
     *                      property="original_price",
     *                      type="float",
     *                      example="2450000000"
     *                  ),
     *                  @OA\Property(
     *                      property="residual_price",
     *                      type="float",
     *                      example="2400000000"
     *                  ),
     *                  @OA\Property(
     *                      property="insurance_contract",
     *                      type="string",
     *                      example="C15/BHKT/15/06/2022"
     *                  ),
     *                  @OA\Property(
     *                      property="asset_images",
     *                      type="uuid",
     *                      example="b175cdc1-230e-4663-812a-759a0edf3414"
     *                   ),
     *                  required={"name", "management_code", "management_unit", "original_price","residual_price","insurance_contract","asset_images"}
     *              )
     *
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function createAsset(CreateAssetRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $postData = $request->validated();
            $postData['status'] = CommonStatusEnum::ACTIVE;
            $postData['code'] = $this->sequenceRepo->generateCode(ASSET_CODE);
            $asset = $this->assetRepo->create($postData);
            if (!$this->moveRawMediaToMedia($postData['asset_images'], $asset)) {
                throw new Exception('Cannot move asset image to media');
            }
            $data = fractal($asset, new DetailAssetTransformer())->toArray();
            handleLogCreateData($data, $asset);
            $response = $this->respondSuccess($data);
            DB::commit();
        } catch (Exception $e) {
            $response = $this->respondError($e->getMessage());
            DB::rollBack();
        }
        return $response;
    }

    private function moveRawMediaToMedia($rawMediaId, $model): bool
    {
        try {
            $rawMedia = $this->rawMediaUploadRepo->find($rawMediaId);
            if (!$rawMedia) {
                throw new Exception('Raw media not found');
            }
            $this->moveMediaToNewCollection($model, $rawMedia);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/asset/update",
     *      operationId="updateAsset",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Update Asset",
     *      description="Update Asset",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                   @OA\Property(
     *                       property="asset_id",
     *                       type="string",
     *                       format="uuid",
     *                       example="c613c9ec-d604-4783-b090-d33fa1a11fae"
     *                   ),
     *                   @OA\Property(
     *                        property="name",
     *                        type="string",
     *                        example="Máy khắc laser UV YLP-UV-3C-S 3W"
     *                    ),
     *                    @OA\Property(
     *                        property="management_code",
     *                        type="string",
     *                        example="KA/KOF/201"
     *                    ),
     *                    @OA\Property(
     *                        property="management_unit",
     *                        type="string",
     *                        example="TÀU CCA GS"
     *                    ),
     *                   @OA\Property(
     *                       property="original_price",
     *                       type="float",
     *                       example="2450000000"
     *                   ),
     *                   @OA\Property(
     *                       property="residual_price",
     *                       type="float",
     *                       example="2400000000"
     *                   ),
     *                   @OA\Property(
     *                       property="insurance_contract",
     *                       type="string",
     *                       example="C15/BHKT/15/06/2022"
     *                   ),
     *                   @OA\Property(
     *                       property="status",
     *                       type="string",
     *                       example="active"
     *                    ),
     *                   @OA\Property(
     *                       property="asset_images",
     *                       type="uuid",
     *                       example="b175cdc1-230e-4663-812a-759a0edf3414"
     *                    ),
     *                   @OA\Property(
     *                       property="media",
     *                       type="array",
     *                      @OA\Items(
     *                         @OA\Property(
     *                             property="new",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(
     *                                     property="asset_images",
     *                                     type="uuid",
     *                                     example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                     description="raw_media_id"
     *                                 ),
     *                             ),
     *                         ),
     *                         @OA\Property(
     *                             property="delete",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(
     *                                     property="asset_images",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="uuid",
     *                                         example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                         description="list delete media_ids"
     *                                     )
     *                                 ),
     *                             ),
     *                         ),
     *                      ),
     *                  )
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function updateAsset(UpdateAssetRequest $request): JsonResponse
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $assetRecord = $this->assetRepo->find($data['asset_id']);
            $originalData = fractal($assetRecord, new DetailAssetTransformer())->toArray();
            if (!empty($data['media'])) {
                checkChangeFileRequired($assetRecord, $data['media'], self::ASSET_COLLECTION_MEDIA);
            }
            if (!empty($data['media'])) {
                $this->updateMedia($assetRecord, $data['media'], self::ASSET_COLLECTION_MEDIA);
            }
            $assetRecord->fill($data);
            $assetRecord->save();
            $respContractData = fractal($assetRecord, new DetailAssetTransformer())->toArray();
            handleLogUpdateData($originalData, $respContractData, $assetRecord);
            $response = $this->respondSuccess($respContractData);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->respondError($e->getMessage());
            dd($e);
        }
        return $response;
    }

    /**
     *
     * @OA\Delete(
     *      path="/api/asset/delete",
     *      operationId="deleteAsset",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Delete Asset",
     *      description="Delete Asset",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function deleteAsset(DeleteAssetRequest $request): JsonResponse
    {
        $data = $this->assetRepo->find($request->validated('asset_id'));
        if ($data->delete()) {
            handleLogDeleteData($data);
            $response = $this->respondSuccessWithoutData(__('messages.delete_successfully'));
        } else {
            $response = $this->respondError(__('messages.delete_fail'));
        }
        return $response;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/asset/detail",
     *      operationId="detail",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get One Asset",
     *      description="Get One Asset",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="asset id",
     *          required=true,
     *          example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function detail(DetailAssetRequest $request): JsonResponse
    {
        $id = $request->validated('id');
        $appointment = $this->assetRepo->find($id);
        $data = fractal($appointment, new DetailAssetTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     *
     * @OA\Get(
     *      path="/api/asset/list",
     *      operationId="getListAsset",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list Asset ",
     *      description="Get list Asset ",
     *      @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="item per page",
     *           required=true,
     *           example=2,
     *           @OA\Schema(
     *               type="integer"
     *           )
     *      ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="page",
     *           example=1,
     *           @OA\Schema(
     *               type="integer"
     *           )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function getListAsset(PaginationRequest $request): JsonResponse
    {
        try {
            $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
            $status = $request->validated('status');
            $conditions = [];
            if ($status) {
                $conditions['status'] = $status;
            }
            $asset = $this->assetRepo->getData(conditions: $conditions, orderBy: ['code' => 'desc'], perPage: $perPage);
            $data = fractal($asset, new DetailAssetTransformer())->toArray();
            $respond = $this->respondSuccess($data);
        } catch (Exception $exception) {
            $respond = $this->respondError($exception->getMessage());
        }
        return $respond;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/asset/maintenance/list",
     *      operationId="getAssetMaintenances",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list Asset Maintenances",
     *      description="Get list Asset Maintenances",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="asset id",
     *          required=true,
     *          example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="status",
     *          required=true,
     *          example="active",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function getAssetMaintenances(ListAssetMaintenanceRequest $request): JsonResponse
    {
        try {
            $assetId = $request->validated('asset_id');
            $status = $request->validated('status');
            $conditions = [
                'asset_id' => $assetId,
            ];
            if ($status) {
                $conditions['status'] = $status;
            }
            $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
            $assetMaintenances = $this->assetMaintenanceRepo->getData(conditions: $conditions, perPage: $perPage);
            $data = fractal($assetMaintenances, new DetailAssetMaintenanceTransformer())->toArray();
            $respond = $this->respondSuccess($data);
        } catch (Exception $exception) {
            $respond = $this->respondError($exception->getMessage());
        }
        return $respond;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/asset/maintenance/detail",
     *      operationId="getAssetMaintenance",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get One Asset Maintenance",
     *      description="Get One Asset Maintenance",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="asset maintenance id",
     *          required=true,
     *          example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function getAssetMaintenance(DetailAssetMaintenanceRequest $request): JsonResponse
    {
        try {
            $id = $request->validated('id');
            $assetMaintenances = $this->assetMaintenanceRepo->find($id);
            $data = fractal($assetMaintenances, new DetailAssetMaintenanceTransformer())->toArray();
            $respond = $this->respondSuccess($data);
        } catch (Exception $exception) {
            $respond = $this->respondError($exception->getMessage());
        }
        return $respond;
    }

    /**
     *
     * @OA\Post(
     *      path="/api/asset/maintenance/create",
     *      operationId="createAssetMaintenance",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Create Asset Maintenance",
     *      description="Create Asset Maintenance",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="asset_id",
     *                      type="uuid",
     *                      example="a0888c4c-d378-4d12-a74d-41128c3b8bb4"
     *                  ),
     *                  @OA\Property(
     *                      property="created_date",
     *                      type="date",
     *                      example="2023-11-11 12:12:12"
     *                  ),
     *                  @OA\Property(
     *                      property="created_by",
     *                      type="string",
     *                      example="created_by"
     *                  ),
     *                   @OA\Property(
     *                       property="reason",
     *                       type="string",
     *                       example="reason"
     *                   ),
     *                   @OA\Property(
     *                       property="description",
     *                       type="string",
     *                       example="description"
     *                   ),
     *                  @OA\Property(
     *                      property="proposal",
     *                      type="string",
     *                      example="proposal"
     *                  ),
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                      example="code"
     *                  ),
     *                  @OA\Property(
     *                      property="causal",
     *                      type="string",
     *                      example="causal"
     *                  ),
     *                  required={"asset_id", "created_date", "created_by", "reason", "description","proposal","code","causal"}
     *              )
     *
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function createAssetMaintenance(CreateAssetMaintenanceRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $postData = $request->validated();
            $postData['status'] = CommonStatusEnum::ACTIVE;
            $assetMaintenance = $this->assetMaintenanceRepo->create($postData);
            $data = fractal($assetMaintenance, new DetailAssetMaintenanceTransformer())->toArray();
            handleLogCreateData($data, $assetMaintenance);
            $response = $this->respondSuccess($data);
            DB::commit();
        } catch (Exception $e) {
            $response = $this->respondError($e->getMessage());
            DB::rollBack();
        }
        return $response;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/asset/delivery-history/list",
     *      operationId="getAssetDeliveries",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list Asset Delivery",
     *      description="Get list Asset Delivery",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="asset id",
     *          required=true,
     *          example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="status",
     *          required=true,
     *          example="active",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function getAssetDeliveries(ListAssetDeliveryRequest $request): JsonResponse
    {
        try {
            $assetId = $request->validated('asset_id');
            $status = $request->validated('status');
            $conditions = [
                'asset_id' => $assetId,
            ];
            if ($status) {
                $conditions['status'] = $status;
            }
            $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
            $assetAssetDeliveries = $this->assetDeliveryRepo->getData(conditions: $conditions, perPage: $perPage);
            $data = fractal($assetAssetDeliveries, new DetailAssetDeliveryHistoryTransformer())->toArray();
            $respond = $this->respondSuccess($data);
        } catch (Exception $exception) {
            $respond = $this->respondError($exception->getMessage());
        }
        return $respond;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/asset/delivery-history/detail",
     *      operationId="getAssetDelivery",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get One Asset Delivery",
     *      description="Get One Asset Delivery",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="asset delivery history id",
     *          required=true,
     *          example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function getAssetDelivery(DetailAssetDeliveryRequest $request): JsonResponse
    {
        try {
            $id = $request->validated('id');
            $assetAssetDeliveries = $this->assetDeliveryRepo->find($id);
            $data = fractal($assetAssetDeliveries, new DetailAssetDeliveryHistoryTransformer())->toArray();
            $respond = $this->respondSuccess($data);
        } catch (Exception $exception) {
            $respond = $this->respondError($exception->getMessage());
        }
        return $respond;
    }

    /**
     *
     * @OA\Post(
     *      path="/api/asset/delivery-history/create",
     *      operationId="createAssetDelivery",
     *      tags={"Asset"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Create Asset Delivery",
     *      description="Create Asset Delivery",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="asset_id",
     *                      type="uuid",
     *                      example="a0888c4c-d378-4d12-a74d-41128c3b8bb4"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="name"
     *                  ),
     *                  @OA\Property(
     *                      property="created_date",
     *                      type="date",
     *                      example="2023-11-11 12:12:12"
     *                  ),
     *                   @OA\Property(
     *                       property="receiver",
     *                       type="string",
     *                       example="receiver"
     *                   ),
     *                   @OA\Property(
     *                       property="deliver",
     *                       type="string",
     *                       example="deliver"
     *                   ),
     *                  @OA\Property(
     *                      property="reason",
     *                      type="string",
     *                      example="reason"
     *                  ),
     *                  @OA\Property(
     *                      property="place_of_use",
     *                      type="string",
     *                      example="place_of_use"
     *                  ),
     *                  @OA\Property(
     *                      property="attachments",
     *                      type="string",
     *                      example="attachments"
     *                  ),
     *                  @OA\Property(
     *                       property="code",
     *                       type="string",
     *                       example="code"
     *                   ),
     *                  required={"asset_id", "name", "created_date", "receiver", "deliver","reason","place_of_use","attachments","code"}
     *              )
     *
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     */
    public function createAssetDelivery(CreateAssetDeliveryRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $postData = $request->validated();
            $postData['status'] = CommonStatusEnum::ACTIVE;
            $assetDelivery = $this->assetDeliveryRepo->create($postData);
            $data = fractal($assetDelivery, new DetailAssetDeliveryHistoryTransformer())->toArray();
            handleLogCreateData($data, $assetDelivery);
            $response = $this->respondSuccess($data);
            DB::commit();
        } catch (Exception $e) {
            $response = $this->respondError($e->getMessage());
            DB::rollBack();
        }
        return $response;
    }

}
