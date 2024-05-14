<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PaginationRequest;
use App\Jobs\SendMailJob;
use App\Mail\CommonMail;
use App\Modules\Employee\Transformers\UserByUserIdTransformer;
use App\Modules\Employee\Transformers\UserInfoTransformer;
use App\Modules\Log\Repositories\Interfaces\LogInterface;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\Interfaces\UserInterface;
use App\Modules\User\Requests\ChangePassRequest;
use App\Modules\User\Requests\DeleteUserRequest;
use App\Modules\User\Requests\GetUserByUserIdRequest;
use App\Modules\User\Requests\ResetPassRequest;
use App\Modules\User\Requests\SendEmailRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use RuntimeException;

class UserController extends ApiController
{
    protected UserInterface $userRepo;
    protected RoleInterface $roleRepo;
    protected LogInterface $logRepo;

    /**
     * @param UserInterface $user
     * @param RoleInterface $role
     * @param LogInterface $logRepo
     */
    public function __construct(
        UserInterface $user,
        RoleInterface $role,
        LogInterface  $logRepo
    )
    {
        $this->userRepo = $user;
        $this->roleRepo = $role;
        $this->logRepo = $logRepo;

    }

    /**
     *
     * @OA\Put(
     *      path="/api/auth/change-pass",
     *      operationId="changePassUser",
     *      tags={"AUTH"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Change Pass User",
     *      description="Change Pass User",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="old_password",
     *                      type="string",
     *                      example="duchuy1234"
     *                  ),
     *                  @OA\Property(
     *                      property="new_password",
     *                      type="string",
     *                      example="duchuy1234@"
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirm",
     *                      type="string",
     *                      example="duchuy1234@"
     *                  ),
     *                  required={"old_password", "new_password", "password_confirm"},
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
    public function changePassUser(ChangePassRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validated('old_password');
            $user = auth()->user();

            if (!$user || !password_verify($request->validated('old_password'), $user->password)) {
                throw new Exception(__('messages.change_fail'));
            }
            $this->userRepo->changePassword($user, bcrypt($request->validated('new_password')));
            $resp = $this->respondSuccessWithoutData(__('messages.change_successfully'));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $resp = $this->respondError($e->getMessage());
        }
        return $resp;
    }

    /**
     *
     * @OA\Put(
     *      path="/api/auth/update",
     *      operationId="updateUser",
     *      tags={"AUTH"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Update User",
     *      description="Update User",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="user_id",
     *                      type="string",
     *                      example="45e10e7f-7891-4ecc-bcfc-757b09afb56c"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      example="duchuy1234@"
     *                  ),
     *                  @OA\Property(
     *                      property="role_id",
     *                      type="string",
     *                      example="a48006aa-2509-4b75-839c-e0ebab1d778b"
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="active"
     *                  ),
     *                  required={"user_id", "new_password"},
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
    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepo->find($request->validated('user_id'));
            $originalData = $user->toArray();
            if (!$user) {
                throw new RuntimeException(__('messages.not_found'));
            }
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            $user->fill($data);
            $user->save();
            if (!empty($data['role_id'])) {
                $user->role()->sync([$data['role_id'] => ['model_type' => User::class]]);
            }
            $newData = $this->userRepo->find($request->validated('user_id'))->toArray();
            handleLogUpdateData($originalData, $newData, $user);
            $resp = $this->respondSuccessWithoutData(__('messages.update_successfully'));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $resp = $this->respondError($e->getMessage());
        }
        return $resp;
    }

    /**
     *
     * @OA\Get(
     *      path="/api/auth/get-user-info",
     *      operationId="getUserInfo",
     *      tags={"Account"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get User Info",
     *      description="Returns User Info",
     *      @OA\Parameter(
     *           name="employee_id",
     *           in="query",
     *           description="employee_id",
     *           required=true,
     *           example="27251564-ba4c-4dff-bd93-5b18625135a9",
     *           @OA\Schema(
     *               type="string",
     *               format="uuid"
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
    public function getUserInfo(): JsonResponse
    {
        $data = $this->userRepo->find(Auth::user()->id);
        $response = fractal($data, new UserInfoTransformer())->toArray();
        return $this->respondSuccess($response);
    }

    /**
     *
     * @OA\Get(
     *      path="/api/auth/get-user",
     *      operationId="getUserByUserId",
     *      tags={"Account"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get User Info",
     *      description="Returns User Info",
     *      @OA\Parameter(
     *           name="user_id",
     *           in="query",
     *           description="user_id",
     *           required=true,
     *           example="27251564-ba4c-4dff-bd93-5b18625135a9",
     *           @OA\Schema(
     *               type="string",
     *               format="uuid"
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
    public function getUserByUserId(GetUserByUserIdRequest $request): JsonResponse
    {
        $userId = $request->validated('user_id');
        $data = $this->userRepo->getFirstRow(['id' => $userId]);
        $response = fractal($data, new UserByUserIdTransformer())->toArray();
        return $this->respondSuccess($response);
    }

    /**
     *
     * @OA\Delete(
     *      path="/api/auth/delete",
     *      operationId="delete",
     *      tags={"Account"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Delete User",
     *      description="Delete User",
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
    public function delete(DeleteUserRequest $request): JsonResponse
    {
        $data = $this->userRepo->find($request->validated('user_id'));
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
     *      path="/api/user/list",
     *      operationId="getListUser",
     *      tags={"Account"},
     *      security={
     *      {"bearerAuth": {}}},
     *      summary="Get list of account",
     *      description="Returns list of account",
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
    public function getList(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated('per_page', DEFAULT_RECORDS_PER_PAGE);
        $conditions = [];
        if ($status = $request->validated('status')) {
            $conditions = ['status' => $status];
        }
        $employees = $this->userRepo->getData(conditions: $conditions, perPage: $perPage);
        $data = fractal($employees, new UserInfoTransformer())->toArray();
        return $this->respondSuccess($data);
    }

    /**
     *
     * @OA\Post(
     *      path="/api/auth/send-reset-password-email",
     *      operationId="sendResetPasswordEmail",
     *      tags={"AUTH"},
     *      summary="Send Email",
     *      description="Send Email",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="superadmin@wishcare.com",
     *                  ),
     *                  required={"email"},
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
    public function sendResetPasswordEmail(SendEmailRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $email = $request->validated('email');
            $result = $this->userRepo->findUserAndSendMail($email);
            $resetLink = env('FRONTEND_PATH') . '/reset-password?token=' . $result['token'];
            $data = [
                'resetLink' => $resetLink,
            ];
            $mailable = new CommonMail(data: $data, subject: 'Reset Password', view: 'mails.resetPassword');
            $email = [$result['user']->email];
            $cc = [];
            $bcc = [];
            dispatch(new SendMailJob(mailable: $mailable, email: $email, cc: $cc, bcc: $bcc));
            DB::commit();
            $respData = [
                "data" => [
                    "message" => 'Send Email successfully',
                    'resetLink' => $resetLink,
                ]
            ];
            $resp = $this->respondSuccess($respData);
        } catch (Exception $e) {
            DB::rollBack();
            $resp = $this->respondError($e->getMessage());
        }
        return $resp;
    }

    /**
     *
     * @OA\Put(
     *      path="/api/auth/reset-password",
     *      operationId="resetPassword",
     *      tags={"AUTH"},
     *      summary="Reset Password User",
     *      description="Reset Password User",
     *      @OA\Parameter(
     *            name="token",
     *            in="query",
     *            description="token",
     *            required=true,
     *            example="8201d2e11a7f5d00218fc9eaee19a0f8d9741cf2bd666d8cc92042665baf0e6c",
     *            @OA\Schema(
     *                type="string"
     *            )
     *       ),
     *       @OA\Parameter(
     *            name="email",
     *            in="query",
     *            description="email",
     *            required=true,
     *            example="superadmin@wishcare.com",
     *            @OA\Schema(
     *                type="string",
     *               format="uuid"
     *            )
     *       ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="new_password",
     *                      type="string",
     *                      example="SSuperadmin123@"
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirm",
     *                      type="string",
     *                      example="SSuperadmin123@"
     *                  ),
     *                  required={"new_password", "password_confirm"},
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
    public function resetPassword(ResetPassRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $postData = $request->validated();
            $result = $this->userRepo->resetPasswordWithToken($postData['token'], $postData['new_password']);
            $result['resetToken']->delete();
            $resp = $this->respondSuccessWithoutData(__('messages.reset_successfully'));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $resp = $this->respondError($e->getMessage());
        }
        return $resp;
    }
}
