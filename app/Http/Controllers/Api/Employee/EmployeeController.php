<?php

namespace App\Http\Controllers\Api\Employee;

use App\Enums\EmployeeRecordTypeEnum;
use App\Http\Controllers\Api\ApiController;
use App\Modules\Employee\Repositories\Interfaces\ContractAllowanceInterface;
use App\Modules\Employee\Repositories\Interfaces\ContractInsuranceProcessedHistoryInterface;
use App\Modules\Employee\Repositories\Interfaces\ContractInterface;
use App\Modules\Employee\Repositories\Interfaces\ContractWorkingHistoryInterface;
use App\Modules\Employee\Repositories\Interfaces\CurriculumVitaeInterface;
use App\Modules\Employee\Repositories\Interfaces\EmployeeInterface;
use App\Modules\Employee\Repositories\Interfaces\EmployeeLogInterface;
use App\Modules\Employee\Repositories\Interfaces\HealthInterface;
use App\Modules\Employee\Repositories\Interfaces\WorkingHistoryInterface;
use App\Modules\Employee\Requests\CreateElectronicRecordRequest;
use App\Modules\Employee\Requests\CreateEmployeeRequest;
use App\Modules\Employee\Requests\CreateUserRequest;
use App\Modules\Employee\Requests\ListEmployeeRequest;
use App\Modules\Employee\Requests\SearchEmployeeLogByEmployeeRequest;
use App\Modules\Employee\Requests\SearchEmployeeLogRequest;
use App\Modules\Employee\Transformers\DataCompareTransformer;
use App\Modules\Employee\Transformers\EmployeeTransformer;
use App\Modules\Employee\Transformers\UserTransformer;
use App\Modules\Employee\Transformers\UuidTransformer;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Modules\Sequence\Repositories\Interfaces\SequenceInterface;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\Interfaces\UserInterface;
use App\Traits\MediaTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class EmployeeController extends ApiController
{
    protected UserInterface $userRepo;
    protected EmployeeInterface $employeeRepo;
    protected EmployeeLogInterface $employeeLogRepo;
    protected CurriculumVitaeInterface $curriculumVitaeRepo;
    protected WorkingHistoryInterface $workingHistoryRepo;
    protected RoleInterface $roleRepo;
    protected SequenceInterface $sequenceRepo;
    protected HealthInterface $healthRepo;
    protected ContractInterface $contractRepo;
    protected ContractInsuranceProcessedHistoryInterface $contractInsuranceProcessedHistoryRepo;
    protected ContractAllowanceInterface $contractAllowanceRepo;
    protected ContractWorkingHistoryInterface $contractWorkingHistoryRepo;
    protected RawMediaUploadInterface $rawMediaUploadRepo;

    use MediaTrait;

    /**
     * @param UserInterface $user
     * @param EmployeeInterface $employee
     * @param EmployeeLogInterface $employeeLog
     * @param CurriculumVitaeInterface $curriculumVitae
     * @param WorkingHistoryInterface $workingHistory
     * @param RoleInterface $role
     * @param SequenceInterface $sequence
     * @param HealthInterface $health
     * @param ContractInterface $contract
     * @param ContractInsuranceProcessedHistoryInterface $contractInsuranceProcessedHistory
     * @param ContractAllowanceInterface $contractAllowance
     * @param ContractWorkingHistoryInterface $contractWorkingHistory
     * @param RawMediaUploadInterface $rawMediaUpload
     */
    public function __construct(
        UserInterface                              $user,
        EmployeeInterface                          $employee,
        EmployeeLogInterface                       $employeeLog,
        CurriculumVitaeInterface                   $curriculumVitae,
        WorkingHistoryInterface                    $workingHistory,
        RoleInterface                              $role,
        SequenceInterface                          $sequence,
        HealthInterface                            $health,
        ContractInterface                          $contract,
        ContractInsuranceProcessedHistoryInterface $contractInsuranceProcessedHistory,
        ContractAllowanceInterface                 $contractAllowance,
        ContractWorkingHistoryInterface            $contractWorkingHistory,
        RawMediaUploadInterface                    $rawMediaUpload,
    )
    {
        $this->userRepo = $user;
        $this->employeeRepo = $employee;
        $this->employeeLogRepo = $employeeLog;
        $this->curriculumVitaeRepo = $curriculumVitae;
        $this->workingHistoryRepo = $workingHistory;
        $this->roleRepo = $role;
        $this->sequenceRepo = $sequence;
        $this->healthRepo = $health;
        $this->contractRepo = $contract;
        $this->contractInsuranceProcessedHistoryRepo = $contractInsuranceProcessedHistory;
        $this->contractAllowanceRepo = $contractAllowance;
        $this->contractWorkingHistoryRepo = $contractWorkingHistory;
        $this->rawMediaUploadRepo = $rawMediaUpload;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/employee/list",
     *      operationId="getList",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list of employees",
     *      description="Returns list of employees",
     *      @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="per_page",
     *           example="10",
     *           @OA\Schema(
     *               type="integer",
     *           )
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="page",
     *           example="1",
     *           @OA\Schema(
     *               type="integer",
     *           )
     *       ),
     *       @OA\Parameter(
     *           name="type",
     *           in="query",
     *           description="type of employee is employee or guest",
     *           example="employee",
     *           @OA\Schema(
     *               type="string",
     *           )
     *       ),
     *      @OA\Parameter(
     *           name="status",
     *           in="query",
     *           description="status - active or inactive",
     *           example="active",
     *           @OA\Schema(
     *               type="string",
     *           )
     *       ),
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
    public function getList(ListEmployeeRequest $request): JsonResponse
    {
        $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
        $conditions = [];
        if ($status = $request->validated('status')) {
            $conditions = ['status' => $status];
        }
        $conditions['type'] = EMPLOYEE_TYPE;
        if ($type = $request->validated('type')) {
            $conditions['type'] = $type;
        }
        $employees = $this->employeeRepo->getData(conditions: $conditions, perPage: $perPage);
        $data = fractal($employees, new EmployeeTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     * @OA\Put(
     *     path="/api/employee/update-status",
     *     operationId="updateStatus",
     *     tags={"Employee"},
     *     security={{"bearerAuth": {}}},
     *     summary="Update employee status",
     *     description="Update employee status",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="string", example="4868aaad-f073-431e-83c3-a3393ab6bc16"),
     *                 @OA\Property(property="status", type="string", example="inactive"),
     *                 required={"id", "status"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */


    public function updateStatus(CreateEmployeeRequest $request): JsonResponse
    {
        $id = $request->validated('id');
        $status = $request->validated('status');
        DB::beginTransaction();
        try {
            $originalData = $this->employeeRepo->find($id);
            $employee = $this->employeeRepo->update($id, ['status' => $status]);
            $data = fractal($employee, new EmployeeTransformer())->toArray();
            handleLogUpdateData($originalData->toArray(), $data, $originalData);
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
     *      path="/api/employee/history",
     *      operationId="getHistory",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get curriculum vitae history",
     *      description="Get curriculum vitae history, default is curriculum vitae history",
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
     *          name="type",
     *          in="query",
     *          description="employee log type",
     *          example="curriculum_record",
     *          @OA\Schema(
     *              type="string"
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
    public function getHistory(SearchEmployeeLogByEmployeeRequest $request): JsonResponse
    {
        $employeeId = $request->validated('employee_id');
        $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
        $employeeLogs = $this->employeeLogRepo->getAllLogs($employeeId, perPage: $perPage);
        $data = fractal($employeeLogs, new UuidTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     *
     * @OA\Get(
     *      path="/api/employee/get-log-compare",
     *      operationId="getLogCompare",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get curriculum vitae history compare",
     *      description="Get curriculum vitae history compare",
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="employee log id",
     *         required=true,
     *         example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
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
     * @throws \JsonException
     */
    public function getLogCompare(SearchEmployeeLogRequest $request): JsonResponse
    {
        $employeeLogs = $this->employeeLogRepo->find($request->validated('id'));
        $logData = json_decode($employeeLogs->data, true, 512, JSON_THROW_ON_ERROR);
        $data = [];
        switch ($employeeLogs->type) {
            case EmployeeRecordTypeEnum::CONTRACT:
//                $dataChanges = $this->contractRecordRepo->getChanges($employeeLogs);
                break;
            case EmployeeRecordTypeEnum::HEALTH:
//                $dataChanges = $this->healthRecordRepo->getChanges($employeeLogs);
                break;
            default:
                $dataChanges = $this->curriculumVitaeRepo->getChanges($employeeLogs->employee_id, $logData);
                $dataChanges['working_history_changes'] = $this
                    ->getWorkingChanges($dataChanges->id, $logData['working_histories'] ?? []);
                $data = fractal($dataChanges, new DataCompareTransformer())->toArray();
        }
        return $this->respondSuccess($data);
    }

    /**
     * @param string $curriculumVitaeId
     * @param array $logData
     *
     * @return array
     */
    private function getWorkingChanges(string $curriculumVitaeId, array $logData): array
    {
        $logs = $this->workingHistoryRepo->getByParams(['curriculum-vitae_id' => $curriculumVitaeId])->toArray();
        $changes = [];
        foreach ($logData as $logDatum) {
            $logs = array_filter($logs, static function ($log) use ($logDatum) {
                return $log['id'] !== $logDatum['id'];
            });
            $log = $this->workingHistoryRepo->getFirstRow(['id' => $logDatum['id']])->fill($logDatum);
            if ($log->isDirty()) {
                $data = $log->getDirty();
                $data['id'] = $log->id;
                $data['all'] = false;
                $changes[] = $data;
            }
        }
        $logs = array_map(static function ($log) {
            $log['all'] = true;
            return $log;
        }, $logs);
        return [...$changes, ...$logs];

    }


    /**
     *
     * @OA\Post(
     *      path="/api/employee/user/create",
     *      operationId="createUser",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Create User",
     *      description="Create User",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="employee_id",
     *                      type="uuid",
     *                      example="c613c9ec-d604-4783-b090-d33fa1a11fae"
     *                  ),
     *                  @OA\Property(
     *                      property="username",
     *                      type="string",
     *                      example="NKSOFT"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      example="abcd1234a"
     *                  ),
     *                  @OA\Property(
     *                      property="role_id",
     *                      type="uuid",
     *                      example="c613c9ec-d604-4783-b090-d33fa1a11fae"
     *                  ),
     *                  required={"employee_id", "username", "password", "role_id"},
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
    public function createUser(CreateUserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $postData = $request->validated();
            $postData['password'] = bcrypt($postData['password']);
            if ($employee = $this->userRepo->getFirstRow(['employee_id' => $postData['employee_id']], withTrashed: true)) {
                $employee->restore();
                $employee->update($postData);
            } else {
                $employee = $this->curriculumVitaeRepo->getFirstRow(['employee_id' => $postData['employee_id']]);
                if (isset($employee->employee->code)) {
                    $postData['username'] = $employee->employee->code;
                }
                $postData['email'] = $employee->email;
                $postData['name'] = $employee->name;
                $user = $this->userRepo->create($postData);
                $user->role()->sync([$postData['role_id'] => ['model_type' => User::class]]);
            }
            $data = fractal($user, new UserTransformer())->toArray();
            handleLogCreateData($data, $user);
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
     * @OA\Post(
     *      path="/api/employee/electronic-record/create",
     *      operationId="createElectronicRecord",
     *      tags={"Employee"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Create Electronic Record",
     *      description="Create Electronic Record",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *              required={"curriculum_vitae", "contract", "health"},
     *                  @OA\Property(
     *                      property="curriculum_vitae",
     *                      required={"name", "email", "phone_number", "identification"},
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Hydra",
     *                      ),
     *                      @OA\Property(
     *                          property="nationality_id",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string",
     *                          example="long.nguyen@nk-software.co"
     *                      ),
     *                      @OA\Property(
     *                          property="phone_number",
     *                          type="string",
     *                          example="0352158989"
     *                      ),
     *                      @OA\Property(
     *                          property="dob",
     *                          type="date",
     *                          example="2000-09-02"
     *                      ),
     *                      @OA\Property(
     *                          property="gender",
     *                          type="string",
     *                          example="male"
     *                      ),
     *                      @OA\Property(
     *                          property="country",
     *                          type="string",
     *                          example="VN"
     *                      ),
     *                      @OA\Property(
     *                          property="marital",
     *                          type="boolean",
     *                          example="0"
     *                      ),
     *                      @OA\Property(
     *                          property="ethnic",
     *                          type="string",
     *                          example="Kinh"
     *                      ),
     *                      @OA\Property(
     *                          property="region_id",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="identification",
     *                          type="string",
     *                          example="2023232020202"
     *                      ),
     *                      @OA\Property(
     *                          property="place_of_issue",
     *                          type="string",
     *                          example="VN"
     *                      ),
     *                      @OA\Property(
     *                          property="date_of_issue",
     *                          type="date",
     *                          example="2000-01-01"
     *                      ),
     *                      @OA\Property(
     *                          property="tax_code",
     *                          type="string",
     *                          example="2023123"
     *                      ),
     *                      @OA\Property(
     *                          property="onboard_date",
     *                          type="date",
     *                          example="2023-01-01"
     *                      ),
     *                      @OA\Property(
     *                          property="leader_id",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="subsidiary_id",
     *                          type="uuid",
     *                          example=""
     *                      ),
     *                      @OA\Property(
     *                          property="position_id",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          type="string",
     *                          example="VN"
     *                      ),
     *                      @OA\Property(
     *                          property="bank_account_number",
     *                          type="string",
     *                          example="1312312"
     *                      ),
     *                      @OA\Property(
     *                          property="bank_account_name",
     *                          type="string",
     *                          example="NVLONG"
     *                      ),
     *                      @OA\Property(
     *                          property="bank_name",
     *                          type="string",
     *                          example="TPBANK"
     *                      ),
     *                      @OA\Property(
     *                          property="bank_branch",
     *                          type="string",
     *                          example="VN"
     *                      ),
     *                      @OA\Property(
     *                          property="identification_front",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="identification_back",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="face_image",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="fingerprint",
     *                          type="uuid",
     *                          example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                      ),
     *                      @OA\Property(
     *                          property="working_histories",
     *                          type="array",
     *                          @OA\Items(
     *                              @OA\Property(
     *                                  property="start_date",
     *                                  type="date",
     *                                  example="2000-10-10"
     *                              ),
     *                              @OA\Property(
     *                                  property="end_date",
     *                                  type="date",
     *                                  example="2000-10-10"
     *                              ),
     *                              @OA\Property(
     *                                  property="posisition_id",
     *                                  type="uuid",
     *                                  example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                              ),
     *                              @OA\Property(
     *                                  property="company",
     *                                  type="string",
     *                                  example="Công ty A"
     *                              ),
     *                          )
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="health",
     *                      required={"note", "health_records"},
     *                      @OA\Property(
     *                          property="blood_pressure",
     *                          type="string",
     *                          example="80"
     *                      ),
     *                      @OA\Property(
     *                          property="heartbeat",
     *                          type="integer",
     *                          example="80"
     *                      ),
     *                      @OA\Property(
     *                          property="height",
     *                          type="integer",
     *                          example="150"
     *                      ),
     *                      @OA\Property(
     *                          property="weight",
     *                          type="integer",
     *                          example="50"
     *                      ),
     *                      @OA\Property(
     *                          property="blood_group",
     *                          type="string",
     *                          example="A+"
     *                      ),
     *                      @OA\Property(
     *                          property="note",
     *                          type="string",
     *                          example="note example"
     *                      ),
     *                      @OA\Property(
     *                          property="health_records",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="contract",
     *                      required={"contract_type_id", "contract_files", "contract_health_records"},
     *                      @OA\Property(
     *                          property="contract_type_id",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                      @OA\Property(
     *                          property="contract_files",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                      @OA\Property(
     *                          property="department_id",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                      @OA\Property(
     *                          property="position_id",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                      @OA\Property(
     *                          property="function",
     *                          type="string",
     *                          example="funcion example"
     *                      ),
     *                      @OA\Property(
     *                          property="rank",
     *                          type="string",
     *                          example="rank example"
     *                      ),
     *                      @OA\Property(
     *                          property="skill_coefficient",
     *                          type="number",
     *                          example="1.2"
     *                      ),
     *                      @OA\Property(
     *                          property="workplace",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                      @OA\Property(
     *                          property="employment_type_id",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                      @OA\Property(
     *                          property="effective_date",
     *                          type="date",
     *                          example="2000-10-12"
     *                      ),
     *                      @OA\Property(
     *                          property="signed_date",
     *                          type="date",
     *                          example="2000-10-12"
     *                      ),
     *                      @OA\Property(
     *                          property="signer",
     *                          type="string",
     *                          example="Nguyễn Văn A"
     *                      ),
     *                      @OA\Property(
     *                          property="digital_signature",
     *                          type="string",
     *                          example="active"
     *                      ),
     *                      @OA\Property(
     *                          property="apply_from_date",
     *                          type="date",
     *                          example="2000-10-12"
     *                      ),
     *                      @OA\Property(
     *                          property="note",
     *                          type="string",
     *                          example="note example"
     *                      ),
     *                      @OA\Property(
     *                          property="payment_type",
     *                          type="string",
     *                          example="payment type example"
     *                      ),
     *                      @OA\Property(
     *                          property="salary",
     *                          type="numeric",
     *                          example="1000000"
     *                      ),
     *                      @OA\Property(
     *                          property="insurance_book_number",
     *                          type="string",
     *                          example="125478AC"
     *                      ),
     *                      @OA\Property(
     *                          property="insurance_book_status",
     *                          type="string",
     *                          example="active"
     *                      ),
     *                      @OA\Property(
     *                          property="insurers",
     *                          type="string",
     *                          example="Nguyễn Văn B"
     *                      ),
     *                      @OA\Property(
     *                          property="insurance_card_number",
     *                          type="string",
     *                          example="125478AC"
     *                      ),
     *                      @OA\Property(
     *                          property="insurance_city_code",
     *                          type="string",
     *                          example="Hà Nội"
     *                      ),
     *                      @OA\Property(
     *                          property="medical_examination_place",
     *                          type="string",
     *                          example="Bệnh viện A"
     *                      ),
     *                      @OA\Property(
     *                          property="card_received_date",
     *                          type="date",
     *                          example="2000-10-12"
     *                      ),
     *                      @OA\Property(
     *                          property="card_returned_date",
     *                          type="date",
     *                          example="2000-10-12"
     *                      ),
     *                      @OA\Property(
     *                          property="contract_health_records",
     *                          type="uuid",
     *                          example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                      ),
     *                      @OA\Property(
     *                          property="contract_working_histories",
     *                          type="array",
     *                          @OA\Items(
     *                              @OA\Property(
     *                                  property="worked_from_date",
     *                                  type="date",
     *                                  example="2000-10-12"
     *                              ),
     *                              @OA\Property(
     *                                  property="worked_to_date",
     *                                  type="date",
     *                                  example="2000-10-13"
     *                              ),
     *                             @OA\Property(
     *                                  property="from_department",
     *                                  type="uuid",
     *                                  example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                              ),
     *                             @OA\Property(
     *                                  property="to_department",
     *                                  type="uuid",
     *                                  example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                              ),
     *                             @OA\Property(
     *                                  property="reason",
     *                                  type="string",
     *                                  example="reason example"
     *                              ),
     *                             @OA\Property(
     *                                  property="job_transfer_proofs",
     *                                  type="uuid",
     *                                  example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                              ),
     *                          ),
     *                      ),
     *                      @OA\Property(
     *                          property="contract_allowances",
     *                          type="array",
     *                          @OA\Items(
     *                              @OA\Property(
     *                                  property="allowance_id",
     *                                  type="uuid",
     *                                  example="824b1d21-50d2-44d6-9245-ae98ce29013a"
     *                              ),
     *                              @OA\Property(
     *                                  property="benefit",
     *                                  type="numeric",
     *                                  example="1000000"
     *                              ),
     *                          ),
     *                      ),
     *                      @OA\Property(
     *                      property="contract_insurance_processed_histories",
     *                      type="array",
     *                       @OA\Items(
     *                              @OA\Property(
     *                                   property="insurance_policy_id",
     *                                   type="uuid",
     *                                   example="85ad7cc0-d3ae-419e-ab12-f6735050cac8"
     *                               ),
     *                             @OA\Property(
     *                                   property="refund_amount",
     *                                   type="float",
     *                                   example="190.999"
     *                               ),
     *                               @OA\Property(
     *                                    property="completed_date",
     *                                    type="date",
     *                                    example="2020-10-12"
     *                                ),
     *                                @OA\Property(
     *                                     property="received_date",
     *                                     type="date",
     *                                     example="2020-10-12"
     *                                 ),
     *                                 @OA\Property(
     *                                      property="refunded_date",
     *                                      type="date",
     *                                      example="2020-10-12"
     *                                  ),
     *                           ),
     *                       ),
     *                  ),
     *              ),
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
    public function createElectronicRecord(CreateElectronicRecordRequest $request): JsonResponse
    {

        try {
            DB::beginTransaction();
            $employeeId = $this->createEmployee();

            if (
                !$this->createCurriculumVitae($employeeId, $request->input('curriculum_vitae')) ||
                !$this->createContract($employeeId, $request->input('contract')) ||
                !$this->createHealth($employeeId, $request->input('health'))
            ) {
                throw new \Exception();
            }
            $respData = [
                'data' => [
                    'employee_id' => $employeeId,
                    'msg' => __('messages.employee.electronic_records.create_successfully')
                ]
            ];
            $response = $this->respondCreated($respData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response = $this->respondError(__('messages.employee.electronic_records.create_fail'));
        }
        return $response;
    }

    /**
     * @return string
     */
    private function createEmployee(): string
    {
        $employeeCode = $this->sequenceRepo->generateCode();
        $employee = $this->employeeRepo->create([
            'code' => $employeeCode,
        ]);
        handleLogCreateData($employee, $employee);
        return $employee->id;
    }

    private function createHealth($employeeId, $healthData): bool
    {
        try {
            $healthData['employee_id'] = $employeeId;
            $healthData['code'] = $this->sequenceRepo->generateCode(HEALTH_CODE);
            $healthRecord = $this->healthRepo->create($healthData);

            if (!$this->moveRawMediaToMedia($healthData['health_records'], $healthRecord)) {
                throw new \Exception();
            }
            handleLogCreateData($healthRecord, $healthRecord);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createCurriculumVitae($employeeId, $curriculumVitaeData): bool
    {
        try {
            $curriculumVitaeData['employee_id'] = $employeeId;
            $curriculumVitaeData['code'] = $this->sequenceRepo->generateCode(CURRICULUM_VITAE_CODE);
            $curriculumVitae = $this->curriculumVitaeRepo->create($curriculumVitaeData);

            if (!empty($curriculumVitaeData['working_histories'])) {
                $this->createWorkingHistory($curriculumVitaeData['working_histories'], $curriculumVitae->id);
            }

            $mediaFields = [
                'identification_front',
                'identification_back',
                'face_image',
                'fingerprint'
            ];
            foreach ($mediaFields as $mediaField) {
                if (!empty($curriculumVitaeData[$mediaField])) {
                    if (!$this->moveRawMediaToMedia($curriculumVitaeData[$mediaField], $curriculumVitae)) {
                        throw new \Exception();
                    }
                }
            }
            handleLogCreateData($curriculumVitae, $curriculumVitae);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function moveRawMediaToMedia($rawMediaId, $model): bool
    {
        try {
            $rawMedia = $this->rawMediaUploadRepo->find($rawMediaId);
            if (!$rawMedia) {
                throw new \Exception();
            }
            $this->moveMediaToNewCollection($model, $rawMedia);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $workingHistories
     * @param string $curriculumVitaeId
     *
     * @return void
     */
    private function createWorkingHistory(mixed $workingHistories, string $curriculumVitaeId): void
    {
        foreach ($workingHistories as $workingHistory) {
            $workingHistory['curriculum_vitae_id'] = $curriculumVitaeId;
            $this->workingHistoryRepo->create($workingHistory);
        }
    }

    private function createContract($employeeId, $contractData): bool
    {
        try {
            $contractData['employee_id'] = $employeeId;
            $contractData['code'] = $this->sequenceRepo->generateCode(CONTRACT_CODE);

            $excludedAttributes = [
                'contract_working_histories',
                'contract_allowances',
                'contract_files',
                'contract_health_records',
                'contract_insurance_processed_histories',
            ];
            $contractRecord = $this->contractRepo->create(Arr::except($contractData, $excludedAttributes));
            $contractId = $contractRecord->id;

            if (!empty(array_filter($contractData['contract_allowances']))) {
                if (!$this->createContractAllowances($contractId, $contractData['contract_allowances'])) {
                    throw new \Exception();
                }
            }

            if (!empty(array_filter($contractData['contract_insurance_processed_histories']))) {
                if (!$this->createContractInsuranceProcessedHistories($contractId, $contractData['contract_insurance_processed_histories'])) {
                    throw new \Exception();
                }
            }

            if (!empty(array_filter($contractData['contract_working_histories']))) {
                if (!$this->createContractWorkingHistories($contractId, $contractData['contract_working_histories'])) {
                    throw new \Exception();
                }
            }
            $mediaFields = [
                'contract_files',
                'contract_health_records',
            ];

            foreach ($mediaFields as $mediaField) {
                if (!empty($contractData[$mediaField])) {
                    if (!$this->moveRawMediaToMedia($contractData[$mediaField], $contractRecord)) {
                        throw new \Exception();
                    }
                }
            }
            handleLogCreateData($contractRecord, $contractRecord);
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    private function createContractWorkingHistories($contractId, $contractWorkingHistoriesData): bool
    {
        try {
            foreach ($contractWorkingHistoriesData as $contractWorkingHistoryData) {
                $contractWorkingHistoryData['contract_id'] = $contractId;
                $contractWorkingHistoryRecord = $this->contractWorkingHistoryRepo->create(Arr::except($contractWorkingHistoryData, ['job_transfer_proofs']));
                if (!$this->moveRawMediaToMedia($contractWorkingHistoryData['job_transfer_proofs'], $contractWorkingHistoryRecord)) {
                    throw new \Exception();
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createContractInsuranceProcessedHistories($contractId, $contractInsuranceProcessedHistoriesData): bool
    {
        try {

            foreach ($contractInsuranceProcessedHistoriesData as &$contractInsuranceProcessedHistoryData) {
                $contractInsuranceProcessedHistoryData['id'] = Str::uuid()->toString();
                $contractInsuranceProcessedHistoryData['contract_id'] = $contractId;
            }
            return $this->contractInsuranceProcessedHistoryRepo->bulkInsert($contractInsuranceProcessedHistoriesData);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createContractAllowances($contractId, $contractAllowancesData)
    {
        try {
            foreach ($contractAllowancesData as &$contractAllowanceData) {
                $contractAllowanceData['id'] = Str::uuid()->toString();
                $contractAllowanceData['contract_id'] = $contractId;
            }
            return $this->contractAllowanceRepo->bulkInsert($contractAllowancesData);
        } catch (\Exception $e) {
            return false;
        }
    }
}
