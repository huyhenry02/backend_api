<?php

namespace App\Http\Controllers\Api\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Http\Controllers\Api\ApiController;
use App\Modules\Appointment\Repositories\Interfaces\AppointmentInterface;
use App\Modules\Appointment\Requests\CreateAppointmentRequest;
use App\Modules\Appointment\Requests\DetailAppointmentRequest;
use App\Modules\Appointment\Requests\ListAppointmentRequest;
use App\Modules\Appointment\Requests\UpdateAppointmentStatusRequest;
use App\Modules\Appointment\Transformers\CreateAppointmentTransformer;
use App\Modules\Appointment\Transformers\DetailAppointmentTransformer;
use App\Modules\Appointment\Transformers\ListAppointmentTransformer;
use App\Modules\Appointment\Transformers\UpdateAppointmentTransformer;
use App\Modules\Employee\Repositories\Interfaces\CurriculumVitaeInterface;
use App\Modules\Employee\Repositories\Interfaces\EmployeeInterface;
use App\Modules\Sequence\Repositories\Interfaces\SequenceInterface;
use Exception;
use App\Modules\User\Repositories\Interfaces\UserInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use RuntimeException;

/**
 *
 */
class AppointmentController extends ApiController
{
    use AuthorizesRequests;

    protected AppointmentInterface $appointmentRepo;
    protected EmployeeInterface $employeeRepo;
    protected CurriculumVitaeInterface $curriculumVitaeRepo;
    protected SequenceInterface $sequenceRepo;
    protected UserInterface $userRepo;

    /**
     * @param AppointmentInterface $appointment
     * @param EmployeeInterface $employee
     * @param CurriculumVitaeInterface $curriculumVitae
     * @param SequenceInterface $sequence
     * @param UserInterface $user
     */
    public function __construct(
        AppointmentInterface     $appointment,
        EmployeeInterface        $employee,
        CurriculumVitaeInterface $curriculumVitae,
        SequenceInterface        $sequence,
        UserInterface            $user,
    )
    {
        $this->appointmentRepo = $appointment;
        $this->employeeRepo = $employee;
        $this->curriculumVitaeRepo = $curriculumVitae;
        $this->sequenceRepo = $sequence;
        $this->userRepo = $user;
    }

    /**
     *
     * @OA\Post(
     *      path="/api/appointment/create",
     *      operationId="createAppointment",
     *      tags={"Appointment"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Create Appointment",
     *      description="Create Appointment",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="employee_id",
     *                      type="uuid",
     *                      example="4868aaad-f073-431e-83c3-a3393ab6bc16"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Hydra"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="long.nguyen@nk-software.co"
     *                  ),
     *                   @OA\Property(
     *                       property="start_time",
     *                       type="date",
     *                       example="2023-08-15 11:30"
     *                   ),
     *                   @OA\Property(
     *                       property="end_time",
     *                       type="date",
     *                       example="2023-08-15 12:30"
     *                   ),
     *                  @OA\Property(
     *                      property="identification",
     *                      type="string",
     *                      example="2023232020202"
     *                  ),
     *                  @OA\Property(
     *                      property="reason",
     *                      type="boolean",
     *                      example="OK"
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                      example="0911001136"
     *                  ),
     *                  required={"employee_id", "name", "email", "phone", "identification"}
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
    public function createAppointment(CreateAppointmentRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $postData = $request->validated();
            $postData['status'] = AppointmentStatusEnum::PENDING->value;
            $postData['registerer_id'] = $this->createGuest($postData);
            $appointment = $this->appointmentRepo->create($postData);
            $data = fractal($appointment, new CreateAppointmentTransformer())->toArray();
            handleLogCreateData($data, $appointment, $appointment['registerer_id']);
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
     *      path="/api/appointment/get",
     *      operationId="getAppointments",
     *      tags={"Appointment"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="List Appointment",
     *      description="List Appointment",
     *      @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="item per page",
     *            required=true,
     *            example=2,
     *            @OA\Schema(
     *                type="integer"
     *            )
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
    public function getAppointments(ListAppointmentRequest $request): JsonResponse
    {
        try {
            $employeeId = $request->validated('employee_id', auth()->user()->employee_id);
            $status = $request->validated('status');
            $conditions = [
                'employee_id' => $employeeId,
            ];
            if ($status) {
                $conditions['status'] = $status;
            }
            $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
            $appointment = $this->appointmentRepo->getData(conditions: $conditions, perPage: $perPage);
            $data = fractal($appointment, new ListAppointmentTransformer())->toArray();
            $respond = $this->respondSuccess($data);
        } catch (Exception $exception) {
            $respond = $this->respondError($exception->getMessage());
        }
        return $respond;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/appointment/detail",
     *      operationId="getAppointment",
     *      tags={"Appointment"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get One Appointment",
     *      description="Get One Appointment",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="appointment id",
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
    public function getAppointment(DetailAppointmentRequest $request): JsonResponse
    {
        $id = $request->validated('id');
        $appointment = $this->appointmentRepo->find($id);
        $data = fractal($appointment, new DetailAppointmentTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     * @param array $postData
     *
     * @return string
     */
    private function createGuest(array $postData): string
    {
        $employeeCode = $this->sequenceRepo->generateCode(GUEST_CODE);
        $emp['code'] = $employeeCode;
        $emp['type'] = GUEST_TYPE;
        $employee = $this->employeeRepo->create($emp);
        $postData['code'] = $this->sequenceRepo->generateCode(CURRICULUM_VITAE_CODE);
        $postData['phone_number'] = $postData['phone'];
        $postData['employee_id'] = $employee->id;
        $this->curriculumVitaeRepo->create($postData);
        return $employee->id;
    }

    /**
     * @OA\Put(
     *     path="/api/appointment/update-status",
     *     operationId="updateStatusAppointment",
     *     tags={"Appointment"},
     *     security={{"bearerAuth": {}}},
     *     summary="Update appointment status",
     *     description="Update appointment status",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="string", example="edf8f63c-404e-4b8a-8725-99ca3fe8305d"),
     *                 @OA\Property(property="status", type="string", example="rejected"),
     *                 @OA\Property(property="reject_reason", type="string", example="OK"),
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
    public function updateStatusAppointment(UpdateAppointmentStatusRequest $request): JsonResponse
    {
        $id = $request->validated('id');
        $newStatus = $request->validated('status');
        $reject_reason = $request->validated('reject_reason');
        DB::beginTransaction();
        try {
            $data = ['status' => $newStatus];
            $currentAppointment = $this->appointmentRepo->find($id);
            $originalData = fractal($currentAppointment, new UpdateAppointmentTransformer())->toArray();

            if (!$this->checkAppointmentStatus($currentAppointment->status, $newStatus)) {
                throw new RuntimeException(__('messages.appointment.status_invalid'));
            }
            if ($newStatus === AppointmentStatusEnum::APPROVED->value) {
                $this->createGuestUser($id);
            } else if ($newStatus === AppointmentStatusEnum::REJECTED->value) {
                $data['rejected_by'] = auth()->user()->employee_id;
                $data['reject_reason'] = $reject_reason;
            }
            $appointment = $this->appointmentRepo->update($id, $data);
            $data = fractal($appointment, new UpdateAppointmentTransformer())->toArray();
            handleLogUpdateData($originalData, $data, $currentAppointment);
            $response = $this->respondSuccess($data);
            DB::commit();
        } catch (Exception $e) {
            $response = $this->respondError($e->getMessage());
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @param string $appointmentId
     * @return void
     */
    private function createGuestUser(string $appointmentId): void
    {
        $appointment = $this->appointmentRepo->find($appointmentId);
        $employee = $this->employeeRepo->find($appointment->registerer_id);
        $this->userRepo->create([
            'name' => $appointment->name,
            'email' => $employee->code . GUEST_MAIL,
            'username' => $employee->code,
            'password' => bcrypt($employee->code),
            'employee_id' => $employee->id,
        ]);
    }

    private function checkAppointmentStatus(string $status, string $newStatus): bool
    {
        return match ($status) {
            AppointmentStatusEnum::PENDING->value => $newStatus === AppointmentStatusEnum::APPROVED->value
                || $newStatus === AppointmentStatusEnum::REJECTED->value,
            AppointmentStatusEnum::APPROVED->value => $newStatus === AppointmentStatusEnum::REJECTED->value
                || $newStatus === AppointmentStatusEnum::PROCESSING->value,
            AppointmentStatusEnum::PROCESSING->value => $newStatus === AppointmentStatusEnum::COMPLETED->value,
            default => false,
        };
    }
}
