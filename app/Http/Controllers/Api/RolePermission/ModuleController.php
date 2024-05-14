<?php

namespace App\Http\Controllers\Api\RolePermission;

use App\Http\Controllers\Api\ApiController;
use App\Modules\RolePermission\Models\Role;
use App\Modules\RolePermission\Repositories\Interfaces\ModuleInterface;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Modules\RolePermission\Requests\GetListModulesRequest;
use App\Modules\RolePermission\Transformers\ModuleTransformer;
use OpenApi\Annotations as OA;

class ModuleController extends ApiController
{
    private ModuleInterface $moduleRepo;
    private RoleInterface $roleRepo;

    public function __construct(ModuleInterface $moduleRepo, RoleInterface $roleRepo)
    {
        $this->moduleRepo = $moduleRepo;
        $this->roleRepo = $roleRepo;
    }

    /**
     * @OA\Get(
     *      path="/api/module/index",
     *     tags={"Role-Permissions"},
     *     summary="get list modules with permisisons by role",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="role_id",
     *         in="query",
     *         description="role id",
     *         required=false,
     *         example="c613c9ec-d604-4783-b090-d33fa1a11fae",
     *         @OA\Schema(
     *             type="uuid"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="get list modules successfully",
     *       )
     * )
     */
    public function getListModules(GetListModulesRequest $request)
    {
        $roleId = $request->query('role_id');
        if (!$roleId) {
            $role = new Role();
        } else {
            $role = $this->roleRepo->find($roleId);
        }
        $listModulesWithPermissions = $this->moduleRepo->getAllModules($roleId);
//        $listModulesWithPermissions = $this->handleListModules($listModulesWithPermissions);
        foreach ($listModulesWithPermissions as $module) {
            $module->permissions->map(function ($permission) use ($role) {
                $permission->inRole = $role->hasPermissionTo($permission);
                return $permission;
            });
        }
        return $this->respondSuccess(fractal($listModulesWithPermissions, new ModuleTransformer())->toArray());
    }

    private function handleListModules($listModules)
    {
        foreach ($listModules as &$module) {
            foreach ($module['permissions'] as &$permission) {
                $keyPermissionLang = 'permissions.' . $module['name'] . '.' . $permission['name'];
                $permission['description'] = __($keyPermissionLang);
            }
        }
        unset($module, $permission);
        return $listModules;
    }
}
