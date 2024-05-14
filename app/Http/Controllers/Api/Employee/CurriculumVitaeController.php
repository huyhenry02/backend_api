<?php

namespace App\Http\Controllers\Api\Employee;

use App\Enums\EmployeeRecordTypeEnum;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PaginationRequest;
use App\Modules\Employee\Repositories\Interfaces\CurriculumVitaeInterface;
use App\Modules\Employee\Repositories\Interfaces\EmployeeInterface;
use App\Modules\Employee\Repositories\Interfaces\EmployeeLogInterface;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Modules\Sequence\Repositories\Interfaces\SequenceInterface;
use App\Modules\Employee\Repositories\Interfaces\WorkingHistoryInterface;
use App\Modules\Employee\Requests\SearchCurriculumVitaeRequest;
use App\Modules\Employee\Requests\UpdateCurriculumVitaeRequest;
use App\Modules\Employee\Transformers\CurriculumVitaeTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use OpenApi\Annotations as OA;
use \App\Traits\MediaTrait;
use RuntimeException;

class CurriculumVitaeController extends ApiController
{
    protected CurriculumVitaeInterface $curriculumVitaeRepo;
    protected EmployeeLogInterface $employeeLogRepo;
    protected EmployeeInterface $employeeRepo;
    protected SequenceInterface $sequenceRepo;
    protected WorkingHistoryInterface $workingHistoryRepo;
    protected RawMediaUploadInterface $rawMediaUploadRepo;
    protected array $include = ['employee', 'workingHistories'];

    use MediaTrait;

    /**
     * @param CurriculumVitaeInterface $curriculumVitae
     * @param EmployeeLogInterface $employeeLog
     * @param EmployeeInterface $employee
     * @param SequenceInterface $sequence
     * @param WorkingHistoryInterface $workingHistory
     * @param RawMediaUploadInterface $rawMediaUpload
     */
    public function __construct(
        CurriculumVitaeInterface $curriculumVitae,
        EmployeeLogInterface     $employeeLog,
        EmployeeInterface        $employee,
        SequenceInterface        $sequence,
        WorkingHistoryInterface  $workingHistory,
        RawMediaUploadInterface  $rawMediaUpload,
    )
    {
        $this->curriculumVitaeRepo = $curriculumVitae;
        $this->employeeLogRepo = $employeeLog;
        $this->employeeRepo = $employee;
        $this->sequenceRepo = $sequence;
        $this->workingHistoryRepo = $workingHistory;
        $this->rawMediaUploadRepo = $rawMediaUpload;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/employee/curriculum-vitae/list",
     *      operationId="getCurriculumVitaeList",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list of curriculum vitaes",
     *      description="Returns list of curriculum vitaes",
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="item per page",
     *          example=2,
     *          @OA\Schema(
     *              type="integer"
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
    public function getCurriculumVitaeList(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
        $curriculumVitaes = $this->curriculumVitaeRepo->getData(perPage: $perPage);
        $data = transformPaginate($curriculumVitaes, new CurriculumVitaeTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     *
     * @OA\Get(
     *      path="/api/employee/curriculum-vitae/detail",
     *      operationId="getOneCurriculumVitae",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get one Curriculum VItae",
     *      description="Get one Curriculum VItae",
     *      @OA\Parameter(
     *          name="employee_id",
     *          in="query",
     *          description="employee id",
     *          required=true,
     *          example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="include",
     *          in="query",
     *          description="include",
     *          example="working_histories,employee,leader,position,subsidiary,nationality",
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
    public function getOneCurriculumVitae(SearchCurriculumVitaeRequest $request): JsonResponse
    {
        $employeeId = $request->validated('employee_id');
        $include = $request->validated('include');
        $curriculum_vitae = $this->curriculumVitaeRepo->getFirstRow(['employee_id' => $employeeId]);

        $data = fractal($curriculum_vitae, new CurriculumVitaeTransformer());
        if (!empty($include)) {
            $include = explode(',', $include);

            $data->parseIncludes(array_map(static function ($item) {
                return $item;
            }, $include));
        }
        return $this->respondSuccess($data->toArray());
    }

    /**
     *
     * @OA\Put(
     *      path="/api/employee/curriculum-vitae/update",
     *      operationId="updateCurriculumVitae",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Update Curriculum Vitae",
     *      description="Update Curriculum Vitae",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="id",
     *                      type="uuid",
     *                      example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Hydra",
     *                  ),
     *                  @OA\Property(
     *                      property="nationality_id",
     *                      type="uuid",
     *                      example="4c74d56d-0315-4c43-9c6d-435578af1dd8 id of nationalities table",
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="long.nguyen@nk-software.co",
     *                  ),
     *                  @OA\Property(
     *                      property="phone_number",
     *                      type="string",
     *                      example="0352158989",
     *                  ),
     *                  @OA\Property(
     *                      property="dob",
     *                      type="date",
     *                      example="2000-09-02",
     *                  ),
     *                  @OA\Property(
     *                      property="gender",
     *                      type="string",
     *                      example="male/female",
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      type="string",
     *                      example="Hà nội, Việt Nam",
     *                  ),
     *                  @OA\Property(
     *                      property="marital",
     *                      type="boolean",
     *                      example="true/false",
     *                  ),
     *                  @OA\Property(
     *                      property="ethnic",
     *                      type="string",
     *                      example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                  ),
     *                  @OA\Property(
     *                      property="region",
     *                      type="string",
     *                      example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                  ),
     *                  @OA\Property(
     *                      property="identification",
     *                      type="string",
     *                      example="0010000008",
     *                  ),
     *                  @OA\Property(
     *                      property="place_of_issue",
     *                      type="string",
     *                      example="Công an Hà Nội",
     *                  ),
     *                  @OA\Property(
     *                      property="date_of_issue",
     *                      type="date",
     *                      example="2022-02-02",
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
     *                                     property="identification_front",
     *                                     type="uuid",
     *                                     example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                     description="raw_media_id"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="identification_back",
     *                                     type="uuid",
     *                                     example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                     description="raw_media_id"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="face_image",
     *                                     type="uuid",
     *                                     example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                     description="raw_media_id"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="fingerprint",
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
     *                                     property="identification_front",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="uuid",
     *                                         example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                                         description="list delete media_ids"
     *                                     )
     *                                 ),
     *                                  @OA\Property(
     *                                     property="face_image",
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
     *                  ),
     *                  @OA\Property(
     *                      property="tax_code",
     *                      type="string",
     *                      example="4454353453",
     *                  ),
     *                  @OA\Property(
     *                      property="onboard_date",
     *                      type="date",
     *                      example="2022-02-02",
     *                  ),
     *                  @OA\Property(
     *                      property="leader_id",
     *                      type="uuid",
     *                      example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                      description="id of employees in hie"
     *                  ),
     *                  @OA\Property(
     *                      property="subsidiary_id",
     *                      type="uuid",
     *                      example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                      description="id of hierarchy only id of level company"
     *                  ),
     *                  @OA\Property(
     *                      property="position_id",
     *                      type="uuid",
     *                      example="4c74d56d-0315-4c43-9c6d-435578af1dd8",
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      type="string",
     *                      example="Ha noi, viet nam",
     *                  ),
     *                  @OA\Property(
     *                      property="bank_account_number",
     *                      type="string",
     *                      example="0123097324",
     *                  ),
     *                  @OA\Property(
     *                      property="bank_account_name",
     *                      type="string",
     *                      example="Nguyễn Văn A",
     *                  ),
     *                  @OA\Property(
     *                      property="bank_name",
     *                      type="string",
     *                      example="Ngân hàng VIETCOMBank",
     *                  ),
     *                  @OA\Property(
     *                      property="bank_branch",
     *                      type="string",
     *                      example="Chi nhanh Thăng Long",
     *                  ),
     *                  @OA\Property(
     *                       property="working_histories",
     *                       type="array",
     *                       @OA\Items(
     *                           @OA\Property(
     *                               property="id",
     *                               type="uuid",
     *                               example="85ad7cc0-d3ae-419e-ab12-f6735050cac8"
     *                           ),
     *                           @OA\Property(
     *                               property="start_date",
     *                               type="date",
     *                               example="2022-02-02"
     *                           ),
     *                           @OA\Property(
     *                                property="end_date",
     *                                type="date",
     *                                example="2020-10-12"
     *                            ),
     *                            @OA\Property(
     *                                property="position",
     *                                type="string",
     *                                example="Nhan vien phòng phát triển"
     *                            ),
     *                            @OA\Property(
     *                                property="company",
     *                                type="string",
     *                                example="Cong ty vận tải ABC"
     *                            ),
     *                            @OA\Property(
     *                                property="is_deleted",
     *                                type="boolean",
     *                                example="true/false"
     *                            ),
     *                           description="update"
     *                       ),
     *                  ),
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

    public function updateCurriculumVitae(UpdateCurriculumVitaeRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $postData = $request->validated();
            $cv_detail = $this->curriculumVitaeRepo->find($postData['id']);
            $originalData = fractal($cv_detail, new CurriculumVitaeTransformer())
                ->parseIncludes([
                    'working_histories',
                    'employee',
                    'leader',
                    'position',
                    'subsidiary',
                ])->toArray();
            $this->checkWorkingHistory($postData, $postData['id']);
            $curriculum_vitae = $this->updateCV($postData['id'], $postData);
            $data = fractal($curriculum_vitae, new CurriculumVitaeTransformer())
                ->parseIncludes([
                    'working_histories',
                    'employee',
                    'leader',
                    'position',
                    'subsidiary',
                ]);
            handleLogUpdateData($originalData, $data->toArray(), $cv_detail);
            $response = $this->respondSuccess(['data' => $data]);
            DB::commit();
        } catch (Exception $e) {
            $response = $this->respondError($e->getMessage());
            DB::rollBack();
            dd($e);
        }
        return $response;
    }

    /**
     * @param array $data
     * @param string $curriculumVitaeId
     */
    private function checkWorkingHistory(array $data, string $curriculumVitaeId): void
    {
        if (!empty($data['working_histories'])) {
            foreach ($data['working_histories'] as $workingHistory) {
                $workingHistory['curriculum_vitae_id'] = $curriculumVitaeId;
                if (!empty($workingHistory['id'])) {
                    if ($workingHistory['is_deleted']) {
                        $this->workingHistoryRepo->delete($workingHistory['id']);
                    } else {
                        $this->workingHistoryRepo->updateWorkingHistories($workingHistory['id'], $workingHistory);
                    }
                } else {
                    $this->workingHistoryRepo->create($workingHistory);
                }
            }
        }
    }


    /**
     * @param string $id
     * @param array $attributes
     *
     * @return mixed
     */
    private function updateCV(string $id, array $attributes): mixed
    {
        $curriculum_vitae = $this->curriculumVitaeRepo->find($id);
        $mediaFields = [
            'identification_front',
            'identification_front',
            'face_image',
            'fingerprint'
        ];
        if ($attributes['media']['new'] ?? false) {
            foreach ($mediaFields as $mediaField) {
                if ($attributes['media']['new'][$mediaField] ?? false) {
                    $this->moveRawMediaToMedia($attributes['media']['new'][$mediaField], $curriculum_vitae);
                }
            }
        }
        if ($attributes['media']['delete'] ?? false) {
            foreach ($mediaFields as $mediaField) {
                if ($attributes['media']['delete'][$mediaField] ?? false) {
                    $this->deleteMultipleFiles($attributes['media']['delete'][$mediaField], $mediaField, $curriculum_vitae->id);
                }
            }
        }
        if (isset($attributes['marital'])) {
            $attributes['marital'] = (bool)$attributes['marital'];
        }
        $curriculum_vitae->fill($attributes);
        $curriculum_vitae->save();
        return $curriculum_vitae;
    }

    private function moveRawMediaToMedia(string $rawMediaId, $model): bool
    {
        try {
            $rawMedia = $this->rawMediaUploadRepo->find($rawMediaId);
            if (!$rawMedia) {
                throw new RuntimeException();
            }
            $this->moveMediaToNewCollection($model, $rawMedia);

            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}
