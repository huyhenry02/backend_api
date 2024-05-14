<?php

namespace App\Http\Controllers\Api\Account;

use App\Enums\CommonStatusEnum;
use App\Http\Controllers\Api\ApiController;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Modules\RolePermission\Transformers\RoleTransformer;
use App\Modules\User\Repositories\Interfaces\UserInterface;
use App\Modules\User\Requests\LoginRequest;
use App\Modules\User\Requests\SignupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends ApiController
{
    protected UserInterface $userRepo;
    protected RoleInterface $roleRepo;

    public function __construct(
        UserInterface $userRepo,
        RoleInterface $roleRepo
    )
    {
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/signup",
     *      tags={"AUTH"},
     *     summary="Adds a new user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     oneOf={
     *                     	   @OA\Schema(type="string"),
     *                     	   @OA\Schema(type="email"),
     *                     }
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                      property="role_name",
     *                      type="string"
     *                  ),
     *                 example={"name": "linh", "email": "test@wishcare.com", "password": "12345678", "role_name": "giam-doc"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="signup successfully"
     *     ),
     *      @OA\Response(
     *          response=422,
     *          description="validate failed",
     *      ),
     * )
     */
    public function signup(SignupRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ];
            $user = $this->userRepo->create($user);
            $role = Role::findByName($request->input('role_name'), 'api');
            $user->assignRole($role);
            DB::commit();
            $respData = [
                "message" => 'Tạo tài khoản thành công',
            ];
            $resp = $this->respondCreated($respData);
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = $this->respondError($e->getMessage(), 400);
        }

        return $resp;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *      tags={"AUTH"},
     *     summary="user login",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                     @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"username": "superadmin@wishcare.com", "password": "Superadmin1@"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successfully"
     *     ),
     *      @OA\Response(
     *          response=422,
     *          description="login failed",
     *      ),
     * )
     */
    public function login(LoginRequest $request)
    {
        try {
            $loginWithEmail = [
                'email' => $request->username,
                'password' => $request->password,
                'status'=>CommonStatusEnum::ACTIVE
            ];
            $loginWithUsername = [
                'username' => $request->username,
                'password' => $request->password,
                'status'=>CommonStatusEnum::ACTIVE
            ];
            if (Auth::attempt($loginWithEmail)
            || Auth::attempt($loginWithUsername)) {
                $user = Auth::user();
                $respData = [
                    "data" => [
                        "message" => 'Login successfully',
                        'access_token' => $user->createToken('Wishcare')->accessToken
                    ]
                ];
                $permissions = fractal($user->role, new RoleTransformer())->toArray() ?? [];
                if ($permissions) {
                    $respData['data']['permissions'] = $permissions[0]['permissions'] ?? [];
                }
                $resp = $this->respondSuccess($respData);
            } else {
                $resp = $this->respondFailedLogin();
            }
        } catch (\Exception $e) {
            $resp = $this->respondError($e->getMessage(), $e->getCode());
        }
        return $resp;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"AUTH"},
     *     summary="user logout",
     *     security={{"bearerAuth": {}}},
     *     description="user logout",
     *     @OA\RequestBody(
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logout successfully"
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Logout failed",
     *      ),
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            $response =  $this->respondSuccessWithoutData();
        } catch (\Exception $e) {
            $response = $this->respondError($e->getMessage());
        }
        return $response;
    }
}
