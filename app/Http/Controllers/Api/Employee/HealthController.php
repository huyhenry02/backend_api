<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Employee\Repositories\Interfaces\EmployeeInterface;
use App\Modules\Employee\Repositories\Interfaces\EmployeeLogInterface;
use App\Modules\Employee\Repositories\Interfaces\HealthInterface;
use App\Modules\Employee\Requests\SearchHealthRecordRequest;
use App\Modules\Employee\Requests\UpdateHealthRequest;
use App\Modules\Employee\Transformers\HealthTransformer;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Traits\MediaTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends ApiController
{
    use MediaTrait;

    protected HealthInterface $healthRepo;
    protected EmployeeLogInterface $employeeLogRepo;
    protected EmployeeInterface $employeeRepo;
    protected RawMediaUploadInterface $rawMediaUploadRepo;

    const HEALTH_COLLECTION_MEDIA = [
        RawMediaUploadCollectionConstants::HEALTH_RECORDS
    ];

    const HEALTH_REQUIRE_MEDIA = [
        RawMediaUploadCollectionConstants::HEALTH_RECORDS
    ];
    const HEALTH_ATTRIBUTES = [
        'blood_pressure',
        'heartbeat',
        'height',
        'weight',
        'blood_group',
        'note'
    ];

    public function __construct(
        HealthInterface         $health,
        EmployeeInterface       $employee,
        EmployeeLogInterface    $employeeLog,
        RawMediaUploadInterface $rawMediaUpload,
    )
    {
        $this->healthRepo = $health;
        $this->employeeRepo = $employee;
        $this->employeeLogRepo = $employeeLog;
        $this->rawMediaUploadRepo = $rawMediaUpload;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/employee/health/get",
     *      operationId="getHelth",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get one Health",
     *      description="Get Health",
     *
     *      @OA\Parameter(
     *         name="employee_id",
     *         in="query",
     *         description="employee id",
     *         required=true,
     *         example="b8a4ff19-6723-4ae4-a07d-9be9ca3b5027",
     *         @OA\Schema(
     *             type="uuid"
     *         )
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
    public function getHealth(SearchHealthRecordRequest $request): JsonResponse
    {
        $healthRecord = $this->healthRepo->getFirstRow(['employee_id' => $request->input('employee_id')]);
        $data = fractal($healthRecord, new HealthTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     * @OA\PUT(
     *      path="/api/employee/health/update",
     *      operationId="updateHealth",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Update Health",
     *      description="Update Health",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="id",
     *                      type="uuid",
     *                      example="6a96cb6c-0358-49b5-bee4-db895dd0a394"
     *                  ),
     *                  @OA\Property(
     *                      property="blood_pressure",
     *                      type="string",
     *                      example="80"
     *                  ),
     *                  @OA\Property(
     *                      property="heartbeat",
     *                      type="integer",
     *                      example="82"
     *                  ),
     *                  @OA\Property(
     *                      property="height",
     *                      type="integer",
     *                      example="80"
     *                  ),
     *                  @OA\Property(
     *                      property="weight",
     *                      type="integer",
     *                      example="180"
     *                  ),
     *                  @OA\Property(
     *                      property="blood_group",
     *                      type="string",
     *                      example="A+"
     *                  ),
     *                  @OA\Property(
     *                      property="note",
     *                      type="string",
     *                      example="test note"
     *                  ),
     *                  @OA\Property(
     *                      property="media",
     *                      type="array",
     *                      @OA\Items(
     *                         @OA\Property(
     *                             property="new",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(
     *                                     property="health_records",
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
     *                                     property="health_records",
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
    public function updateHealth(UpdateHealthRequest $request): JsonResponse
    {
        $healthRecord = $this->healthRepo->find($request->input('id'));
        $originalData = fractal($healthRecord, new HealthTransformer())->toArray();
        $postMediaData = $request->validated('media');

        if (!$this->checkRequireMedia($healthRecord, $postMediaData)) {
            return $this->respondError(__('messages.employee.electronic_records.create_fail'));
        }

        if (!empty($postMediaData['delete'])) {
            if (!$this->deleteHealthMedia($postMediaData['delete'], $healthRecord->id)) {
                return $this->respondError(__('messages.employee.electronic_records.create_fail'));
            }
        }
        if (!empty($postMediaData['new'])) {
            if (!$this->updateHealthMedia($healthRecord, $postMediaData['new'])) {
                return $this->respondError(__('messages.employee.electronic_records.create_fail'));
            }
        }
        DB::beginTransaction();
        try {
            $healthRecord = $this->healthRepo->update($request->validated('id'), $request->only(self::HEALTH_ATTRIBUTES));

            if (!$healthRecord) {
                throw new Exception(__('messages.employee.electronic_records.create_fail'));
            }
            DB::commit();

            foreach (self::HEALTH_COLLECTION_MEDIA as $collectionName) {
                $healthRecord[$collectionName] = $healthRecord->getMedia($collectionName);
            }
            $health = $this->healthRepo->find($healthRecord->id);
            $newData = fractal($health, new HealthTransformer())->toArray();
            $response = $this->respondSuccess($newData);
            handleLogUpdateData($originalData, $newData, $health);
        } catch (Exception $e) {
            $response = $this->respondError(__('messages.employee.electronic_records.create_fail'));
            DB::rollBack();
        }

        return $response;
    }

    private function checkRequireMedia($healthRecord, $media)
    {
        if (empty($media['delete'])) {
            return true;
        }

        foreach (self::HEALTH_REQUIRE_MEDIA as $collectionName) {
            if ((count($healthRecord->getMedia($collectionName)) == count($media['delete'][$collectionName]))
                && empty($media['new'][$collectionName])) {
                return false;
            }
        }
        return true;
    }

    private function deleteHealthMedia($deleteMedia, $healthRecordId)
    {
        foreach (self::HEALTH_COLLECTION_MEDIA as $collectionName) {
            if (!empty($deleteMedia[$collectionName])) {
                if (!$this->deleteFile($deleteMedia[$collectionName], $collectionName, $healthRecordId)) {
                    return false;
                }
            }
        }
        return true;
    }

    private function updateHealthMedia($healthRecord, $newMediaId)
    {
        try {
            foreach (self::HEALTH_COLLECTION_MEDIA as $collectionName) {
                if (!empty($newMediaId[$collectionName])) {
                    $rawMedia = $this->rawMediaUploadRepo->find($newMediaId[$collectionName]);
                    $this->moveMediaToNewCollection($healthRecord, $rawMedia);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
