<?php

namespace App\Http\Controllers\Api\RolePermission;

use App\Enums\CommonStatusEnum;
use App\Http\Controllers\Api\ApiController;
use App\Modules\Log\Repositories\Interfaces\LogInterface;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Modules\RolePermission\Requests\ChangeRoleStatusRequest;
use App\Modules\RolePermission\Requests\CreateRoleRequest;
use App\Modules\RolePermission\Requests\DeleteRoleRequest;
use App\Modules\RolePermission\Requests\GetRoleRequest;
use App\Modules\RolePermission\Requests\GivePermissionsToRoleRequest;
use App\Modules\RolePermission\Requests\UpdateRoleRequest;
use App\Modules\RolePermission\Transformers\RoleTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use RuntimeException;

class RoleController extends ApiController
{
    protected RoleInterface $roleRepo;

    public function __construct(RoleInterface $roleRepo, LogInterface $logRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    /**
     * @OA\Get(
     *      path="/api/role/index",
     *     tags={"Role-Permissions"},
     *     summary="get list roles",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="records_per_page",
     *         in="query",
     *         description="records per page",
     *         required=false,
     *         example=15,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="get list successfully",
     *       )
     * )
     */
    public function getListRoles(Request $request): JsonResponse
    {
        $recordsPerPage = $request->query('per_page') ?? DEFAULT_RECORDS_PER_PAGE;
        $roles = $this->roleRepo->getAllRoles($recordsPerPage);
        if ($recordsPerPage != GET_ALL_ITEMS) {
            $roles = transformPaginate($roles, new RoleTransformer());
        } else {
            $roles = fractal($roles, new RoleTransformer());
        }
        return $this->respondSuccess($roles->toArray());
    }

    /**
     * @OA\Post(
     *     path="/api/role/create",
     *     tags={"Role-Permissions"},
     *     summary="create new role",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="permissions",
     *                     type="array",
     *                     description="list permission's name",
     *                     @OA\Items(type="string", description="permisison name"),
     *                 ),
     *                 example={"description": "president", "permissions" : {"create-role","update-role"}},
     *                 required={"description"},
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
    public function createRole(CreateRoleRequest $request): JsonResponse
    {
        $roleDescription = $request->input('description');
        $permissions = $request->input('permissions');
        $status = $request->input('status');
        $roleName = Str::slug($roleDescription);
        DB::beginTransaction();
        try {
            $role = $this->roleRepo->getByParams(['name' => $roleName], 'id', 'ASC', true)->toArray();

            if (!empty($role)) {
                throw new \InvalidArgumentException();
            }

            $data = $this->roleRepo->create([
                'name' => Str::slug($roleDescription),
                'description' => $roleDescription,
                'status' => $status,
            ]);
            $data->syncPermissions($permissions);

            $fractalData = fractal($data, new RoleTransformer())->toArray();
            handleLogCreateData($fractalData, $data);
            $respData = [
                'message' => __('messages.role.create_successfully'),
                'data' => $fractalData
            ];
            $response = $this->respondCreated($respData);
            DB::commit();
        } catch (\InvalidArgumentException $ex) {
            DB::rollBack();
            $response = $this->respondError(__('messages.role.role_existed'));
        } catch (\Exception $ex) {
            DB::rollBack();
            $response = $this->respondError(__('messages.role.create_fail'));
        }

        return $response;
    }

    /**
     * @OA\Patch(
     *     path="/api/role/update",
     *     tags={"Role-Permissions"},
     *     summary="update role",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="uuid",
     *                     description="role id"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="role description"
     *                 ),
     *                  @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     description="status in: active, inactive"
     *                 ),
     *                 @OA\Property(
     *                     property="permissions",
     *                     type="array",
     *                     description="list permission's name",
     *                     @OA\Items(type="string", description="permisison name"),
     *                 ),
     *                 example={"id": "c613c9ec-d604-4783-b090-d33fa1a11fae", "description": "president", "status" :
     *     "active", "permissions" : {"create-role","update-role"}}, required={"id"},
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
    public function updateRole(UpdateRoleRequest $request): JsonResponse
    {
        $roleId = $request->input('id');
        $roleDescription = $request->input('description');
        $roleStatus = $request->input('status');
        $permissions = $request->input('permissions');
        $roleName = Str::slug($roleDescription);
        try {
            $role = $this->roleRepo->findByName($roleName, true);
            $originalRole = $this->roleRepo->find($roleId);
            $originalRoleArray = fractal($originalRole, new RoleTransformer())->toArray();
            if ($role) {
                if ($role->id != $roleId) {
                    throw new \InvalidArgumentException();
                }

                if ($role->status == CommonStatusEnum::INACTIVE->value) {
                    throw new ModelNotFoundException();
                }
            }
            $roleRequest = [];
            if ($roleDescription) {
                $roleRequest['description'] = $roleDescription;
            }
            if ($roleStatus) {
                if ($roleStatus == CommonStatusEnum::INACTIVE->value) {
                    if (!empty($role->user) && count($role->user)) {
                        throw new RuntimeException(__('messages.role.delete_has_data_depend'));
                    }
                }
                $roleRequest['status'] = $roleStatus;
            }
            if (!$roleRequest) {
                throw new \Exception(__('messages.no_data'));
            }
            $roleUpdate = $this->roleRepo->update($roleId, $roleRequest);
            $roleUpdate->syncPermissions($permissions);
            $roleUpdate = fractal($roleUpdate, new RoleTransformer())->toArray();
            handleLogUpdateData($originalRoleArray, $roleUpdate, $originalRole);
            $respData = [
                "message" => __('messages.role.update_successfully'),
                "data" => $roleUpdate
            ];
            $response = $this->respondUpdated($respData);
        } catch (\InvalidArgumentException $ex) {
            $response = $this->respondError(__('messages.role.role_existed'));
        } catch (ModelNotFoundException) {
            $response = $this->respondError(__('messages.role.role_not_found'));
        } catch (\Exception $ex) {
            if ($ex->getMessage() == __('messages.role.delete_has_data_depend')) {
                $response = $this->respondError($ex->getMessage());
            } else {
                $response = $this->respondError(__('messages.role.update_fail'));
            }
        }

        return $response;
    }

    /**
     * @OA\Delete(
     *     path="/api/role/delete",
     *     tags={"Role-Permissions"},
     *     summary="delete role",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="uuid",
     *                     description="role id"
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
    public function deleteRole(DeleteRoleRequest $request): JsonResponse
    {
        $roleId = $request->input('id');
        try {
            $role = $this->roleRepo->find($roleId);
            if ($role) {
                if (!empty($role->user) && count($role->user)) {
                    throw new RuntimeException(__('messages.role.delete_has_data_depend'));
                }
            }

            if (!$role->delete()) {
                throw new RuntimeException();
            }
            handleLogDeleteData($role);
            $respData = [
                "message" => __('messages.role.delete_successfully')
            ];
            $response = $this->respondSuccess($respData);
        } catch (\Exception $ex) {
            if ($ex->getMessage()) {
                $response = $this->respondError($ex->getMessage());
            } else {
                $response = $this->respondError(__('messages.role.delete_fail'));
            }
        }
        return $response;
    }

    /**
     * @OA\Patch(
     *     path="/api/role/change-status",
     *     tags={"Role-Permissions"},
     *     summary="change role status",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="uuid",
     *                     description="role id"
     *                 ),
     *                 @OA\Property(
     *                     property="action",
     *                     type="integer",
     *                     enum={0, 1},
     *                     description="0: deactivate, 1: activate"
     *                 ),
     *                 example={"id": "c613c9ec-d604-4783-b090-d33fa1a11fae", "action" : "0"},
     *                 required={"id", "action"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="change role status successfully"
     *     ),
     *      @OA\Response(
     *          response=400,
     *          description="change role status failed",
     *      ),
     * )
     */
    public function changeRoleStatus(ChangeRoleStatusRequest $request): JsonResponse
    {
        $roleId = $request->input('id');
        $action = $request->input('action');
        $status = CommonStatusEnum::ACTIVE;
        if ($action == ACTION_DEACTIVATE) {
            $status = CommonStatusEnum::INACTIVE;
        }

        try {
            $this->roleRepo->update($roleId, [
                'status' => $status
            ]);

            $responseMessage = $action == ACTION_ACTIVATE ?
                __('messages.role.restore_successfully') : __('messages.role.delete_successfully');
            $respData = [
                "message" => $responseMessage
            ];

            $response = $this->respondSuccess($respData);
        } catch (\Exception $ex) {
            $responseFailMessage = $status == ACTION_ACTIVATE ?
                __('messages.role.restore_fail') : __('messages.role.delete_fail');
            $response = $this->respondError($responseFailMessage);
        }

        return $response;
    }

    /**
     * @OA\Post(
     *     path="/api/role/give-permissions",
     *     tags={"Role-Permissions"},
     *     summary="give permissions to role",
     *      security={
     *      {"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="role_id",
     *                     type="uuid",
     *                     description="role id"
     *                 ),
     *                 @OA\Property(
     *                     property="permissions",
     *                     type="array",
     *                     description="list permission's name",
     *                     @OA\Items(type="string", description="permisison name"),
     *                 ),
     *                 example={"id": "c613c9ec-d604-4783-b090-d33fa1a11fae", "permissions" :
     *     {"create-role","update-role"}}, required={"id", "permissions"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="give permissions to role successfully"
     *     ),
     *      @OA\Response(
     *          response=400,
     *          description="give permissions to role failed",
     *      ),
     * )
     */
    public function givePermissionsToRole(GivePermissionsToRoleRequest $request)
    {
        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions');

        try {
            $role = $this->roleRepo->find($roleId);
            if (!isset($role)) {
                throw new ModelNotFoundException();
            }

            $role->syncPermissions($permissions);

            $response = $this->respondSuccessWithoutData(__('messages.role.give_permissions_successfully'));
        } catch (ModelNotFoundException $ex) {
            $response = $this->respondError(__("messages.role.role_not_found"));
        } catch (\Exception $ex) {
            $response = $this->respondError(__('messages.role.give_permissions_fail'));
        }

        return $response;
    }

    /**
     * @OA\Get(
     *      path="/api/role/get",
     *     tags={"Role-Permissions"},
     *     summary="get a role",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="role_id",
     *         in="query",
     *         description="role id",
     *         required=true,
     *         example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="get list successfully",
     *       )
     * )
     */
    public function getRole(GetRoleRequest $request)
    {
        $roleId = $request->input('role_id');
        $role = $this->roleRepo->find($roleId);
        $role = fractal($role, new RoleTransformer());
        return $this->respondSuccess($role->toArray());
    }
}
