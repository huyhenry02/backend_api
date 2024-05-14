<?php

namespace App\Http\Controllers\Api\Hierarchy;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Hierarchy\Repositories\Interfaces\HierarchyInterface;
use App\Modules\Hierarchy\Requests\CreateUnitRequest;
use App\Modules\Hierarchy\Requests\DeleteUnitRequest;
use App\Modules\Hierarchy\Requests\GetUnitByCodeRequest;
use App\Modules\Hierarchy\Requests\GetUnitByLevelRequest;
use App\Modules\Hierarchy\Requests\GetUnitDetailRequest;
use App\Modules\Hierarchy\Requests\UpdateUnitRequest;
use App\Modules\Hierarchy\Transformers\ListUnitTransformer;
use App\Modules\Hierarchy\Transformers\UnitDataTransformer;
use App\Modules\Hierarchy\Transformers\UnitDetailTransformer;
use App\Modules\MasterData\UnitLevel\Models\UnitLevel;
use App\Modules\MasterData\UnitLevel\Repositories\UnitLevelRepository;
use App\Modules\Sequence\Repositories\SequenceRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use OpenApi\Annotations as OA;
use RuntimeException;
use Spatie\Fractalistic\ArraySerializer;

class HierarchyController extends ApiController
{
    private HierarchyInterface $hierarchyRepo;
    private SequenceRepository $sequenceRepo;
    private UnitLevelRepository $unitLevelRepo;

    public function __construct(
        HierarchyInterface  $hierarchyRepo,
        SequenceRepository  $sequenceRepo,
        UnitLevelRepository $unitLevelRepo,
    )
    {
        $this->hierarchyRepo = $hierarchyRepo;
        $this->sequenceRepo = $sequenceRepo;
        $this->unitLevelRepo = $unitLevelRepo;
    }

    /**
     * @OA\Get(
     *      path="/api/hierarchy/index",
     *     tags={"Hierarchy"},
     *     summary="get list units in company",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="get list successfully",
     *       )
     * )
     */
    public function getListUnits(): JsonResponse
    {
        $selectColumns = [
            'id',
            'name'
        ];
        $listUnits = $this->hierarchyRepo->getListUnits($selectColumns);
        return $this->respondSuccess(fractal($listUnits, new ListUnitTransformer())->toArray());
    }

    /**
     * @OA\Get(
     *      path="/api/hierarchy/detail",
     *     tags={"Hierarchy"},
     *     summary="get detail information of unit",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="unit id",
     *         required=false,
     *         example="b8a4ff19-6723-4ae4-a07d-9be9ca3b5027",
     *         @OA\Schema(
     *             type="uuid"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="get detail successfully",
     *       )
     * )
     */
    public function getUnitDetail(GetUnitDetailRequest $request): JsonResponse
    {
        $unitDetail = $this->hierarchyRepo->getUnitDetail($request->input('id'));
        $data = fractal($unitDetail, new UnitDetailTransformer())
            ->parseIncludes('child_units');
        return $this->respondSuccess($data->toArray());
    }

    /**
     * @OA\Post(
     *     path="/api/hierarchy/create",
     *     tags={"Hierarchy"},
     *     summary="create new unit",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="establishment_date",
     *                     type="date",
     *                 ),
     *                 @OA\Property(
     *                     property="level_id",
     *                     type="uuid",
     *                     description="Attribute when create unit"
     *                 ),
     *                 @OA\Property(
     *                     property="parent_id",
     *                     type="uuid",
     *                     description="Attribute when create unit"
     *                 ),
     *                 @OA\Property(
     *                     property="mandates",
     *                     type="string",
     *                     description="Attribute when create unit"
     *                 ),
     *                 @OA\Property(
     *                     property="tax_code",
     *                     type="string",
     *                     description="Attribute when create company"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="Attribute when create company"
     *                 ),
     *                 @OA\Property(
     *                     property="registration_number",
     *                     type="string",
     *                     description="Attribute when create company"
     *                 ),
     *                 @OA\Property(
     *                     property="date_of_issue",
     *                     type="date",
     *                     description="Attribute when create company"
     *                 ),
     *                 @OA\Property(
     *                     property="place_of_issue",
     *                     type="string",
     *                     description="Attribute when create company"
     *                 ),
     *                 @OA\Property(
     *                     property="representative",
     *                     type="string",
     *                     description="Attribute when create company"
     *                 ),
     *                 @OA\Property(
     *                     property="position",
     *                     type="string",
     *                     description="Attribute when create company"
     *                 ),
     *                 @OA\Property(
     *                     property="is_company",
     *                     type="boolean",
     *                     description="Require Attribute to define creating unit is company"
     *                 ),
     *                 example={"name": "Công ty ABC", "tax_code" : "N1022", "address" : "Vũng Tàu, Việt Nam",
     *                  "establishment_date" : "2000/12/10", "registration_number" : "MH8292840", "date_of_issue" : "2000/12/10",
     *                  "place_of_issue" : "2000/12/10", "representative" : "Nguyễn Văn A", "posititon" : "Giám đốc",
     *                  "level_id" : "3a37916d-7a93-42f8-bea1-90033e23afcc", "parent_id" : "9eced07f-fdb8-418b-9778-1529ebd3bb75",
     *                  "mandates" : "Đảm bảo ANCL", "is_company" : "1",
     *                 },
     *                 required={"name"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="create successfully"
     *     ),
     *      @OA\Response(
     *          response=400,
     *          description="create failed",
     *      ),
     * )
     */
    public function createUnit(CreateUnitRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $unitData = $request->only('name', 'establishment_date');
            if ($request->input('is_company')) {
                $unitData = array_merge($unitData, $request->only(['tax_code', 'address', 'registration_number', 'date_of_issue',
                    'place_of_issue', 'representative', 'position', 'parent_id']));
                $companyLevel = $this->unitLevelRepo->getFirstRow(['code' => UnitLevel::COMPANY_TYPE], ['id']);
                $unitData['level_id'] = $companyLevel->id;
            } else {
                $unitData = array_merge($unitData, $request->only(['level_id', 'parent_id', 'mandates']));
            }

            $unitData['unit_code'] = $this->sequenceRepo->generateCode(UNIT_CODE);
            $createdUnitData = $this->hierarchyRepo->create($unitData);
            DB::commit();
            $respData = [
                'data' => fractal($createdUnitData, new UnitDataTransformer())->toArray(),
                'msg' => __('messages.hierarchy.create_successfully')
            ];
            handleLogCreateData($respData['data'], $createdUnitData);
            $response = $this->respondCreated($respData);
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->respondError(__('messages.hierarchy.create_fail'));

        }
        return $response;
    }

    private function checkValidCompany($id): bool
    {
        $company = $this->hierarchyRepo->getUnitDetailWithoutChildUnits($id, ['id', 'level_id']);
        if ($company->unitLevel->code == UnitLevel::COMPANY_TYPE) {
            return true;
        }
        return false;
    }

    /**
     * @OA\Put(
     *     path="/api/hierarchy/update",
     *     tags={"Hierarchy"},
     *     summary="update unit",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="uuid",
     *                 ),
     *                 @OA\Property(
     *                     property="establishment_date",
     *                     type="date",
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     description="Receive 2 values: active|inactive"
     *                 ),
     *                 @OA\Property(
     *                     property="is_company",
     *                     type="boolean",
     *                     description="Required attribute to define updating unit is company"
     *                 ),
     *                 @OA\Property(
     *                     property="parent_id",
     *                     type="uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="tax_code",
     *                     type="string",
     *                     description="Attribute when update company"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="Attribute when update company"
     *                 ),
     *                 @OA\Property(
     *                     property="registration_number",
     *                     type="string",
     *                     description="Attribute when update company"
     *                 ),
     *                 @OA\Property(
     *                     property="date_of_issue",
     *                     type="date",
     *                     description="Attribute when update company"
     *                 ),
     *                 @OA\Property(
     *                     property="place_of_issue",
     *                     type="string",
     *                     description="Attribute when update company"
     *                 ),
     *                 @OA\Property(
     *                     property="representative",
     *                     type="string",
     *                     description="Attribute when update company"
     *                 ),
     *                 @OA\Property(
     *                     property="position",
     *                     type="string",
     *                     description="Attribute when update company"
     *                 ),
     *                 @OA\Property(
     *                     property="level_id",
     *                     type="uuid",
     *                     description="Attribute when update unit"
     *                 ),
     *                 @OA\Property(
     *                     property="mandates",
     *                     type="string",
     *                     description="Attribute when update unit"
     *                 ),
     *                 example={"name": "Công ty ABC", "tax_code" : "N1022", "address" : "Vũng Tàu, Việt Nam",
     *                  "establishment_date" : "2000/12/10", "registration_number" : "MH8292840", "date_of_issue" : "2000/12/10",
     *                  "place_of_issue" : "2000/12/10", "representative" : "Nguyễn Văn A", "posititon" : "Giám đốc",
     *                  "level_id" : "3a37916d-7a93-42f8-bea1-90033e23afcc", "parent_id" : "9eced07f-fdb8-418b-9778-1529ebd3bb75",
     *                  "mandates" : "Đảm bảo ANCL", "status": "active", "is_company" : "1",
     *                 },
     *                 required={"id", "status"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update successfully"
     *     ),
     *      @OA\Response(
     *          response=400,
     *          description="update failed",
     *      ),
     * )
     */
    public function updateUnit(UpdateUnitRequest $request): JsonResponse
    {
        $unitData = $request->only('name', 'establishment_date', 'status');
        $originalData = $this->hierarchyRepo->find($request->input('id'));
        $originalData = fractal($originalData, new UnitDataTransformer(), new ArraySerializer())->toArray();
        try {
            if ($request->input('is_company')) {
                if (!$this->checkValidCompany($request->input('id'))) {
                    throw new InvalidArgumentException();
                }

                $unitData = array_merge($unitData, $request->only(['tax_code', 'address', 'registration_number', 'date_of_issue',
                    'place_of_issue', 'representative', 'position', 'parent_id']));
            } else {
                $unitData = array_merge($unitData, $request->only(['level_id', 'parent_id', 'mandates']));
            }
            $updatedUnitData = $this->hierarchyRepo->update($request->input('id'), $unitData);

            $respData = [
                'data' => fractal($updatedUnitData, new UnitDataTransformer(), new ArraySerializer())->toArray(),
                'msg' => __('messages.hierarchy.update_successfully')
            ];
            handleLogUpdateData($originalData, $respData['data'], $originalData);
            $response = $this->respondSuccess($respData);
        } catch (InvalidArgumentException $e) {
            $response = $this->respondError(__('messages.hierarchy.not_valid_company'));
        } catch (Exception $e) {
            $response = $this->respondError(__('messages.hierarchy.update_fail'));
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/hierarchy/delete",
     *     tags={"Hierarchy"},
     *     summary="delete unit",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="uuid",
     *                     description="unit id"
     *                 ),
     *                 example={"id": "c613c9ec-d604-4783-b090-d33fa1a11fae"},
     *                 required={"id"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="delete successfully"
     *     ),
     *      @OA\Response(
     *          response=400,
     *          description="delete failed",
     *      ),
     * )
     */
    public function deleteUnit(DeleteUnitRequest $request): JsonResponse
    {
        $unitId = $request->input('id');

        try {
            $hierarchy = $this->hierarchyRepo->find($unitId);
            if (
                $hierarchy->children->count() > 0
                || $hierarchy->contract->count() > 0
                || $hierarchy->fromDepartment->count() > 0
                || $hierarchy->toDepartment->count() > 0
                || $hierarchy->subsidiary->count() > 0
            ) {
                throw new RuntimeException(__('messages.hierarchy.can_not_delete'));
            }
            if (!$hierarchy->delete()) {
                throw new RuntimeException(__('messages.hierarchy.delete_fail'));
            }
            handleLogDeleteData($hierarchy);
            $respData = [
                "message" => __('messages.hierarchy.delete_successfully')
            ];
            $response = $this->respondSuccess($respData);
        } catch (Exception $ex) {
            $response = $this->respondError($ex->getMessage());
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/hierarchy/get-by-level",
     *     tags={"Hierarchy"},
     *     summary="get by level",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="level_id",
     *         in="query",
     *         description="level id",
     *         required=false,
     *         example="b8a4ff19-6723-4ae4-a07d-9be9ca3b5027",
     *         @OA\Schema(
     *             type="uuid"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="get by level successfully",
     *       )
     * )
     */
    public function getListUnitsByLevel(GetUnitByLevelRequest $request): JsonResponse
    {
        $params = [
            'level_id' => $request->level_id,
        ];
        $listUnits = $this->hierarchyRepo->getData(conditions: $params);
        return $this->respondSuccess(fractal($listUnits, new ListUnitTransformer())->toArray());
    }

    /**
     * @OA\Get(
     *     path="/api/hierarchy/get-by-code",
     *     tags={"Hierarchy"},
     *     summary="get by code",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="code",
     *         required=false,
     *         example="company",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="get by level successfully",
     *       )
     * )
     */
    public function getListUnitsByCode(GetUnitByCodeRequest $request): JsonResponse
    {
        $params = [
            'code' => $request->code,
        ];
        $unitLevel = $this->unitLevelRepo->getFirstRow(conditions: $params);
        $listUnits = $this->hierarchyRepo->getData(conditions: ['level_id' => $unitLevel->id]);
        return $this->respondSuccess(fractal($listUnits, new ListUnitTransformer())->toArray());
    }
}
