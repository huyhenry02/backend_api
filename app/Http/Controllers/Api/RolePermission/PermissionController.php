<?php

namespace App\Http\Controllers\Api\RolePermission;

use App\Http\Controllers\Api\ApiController;
use App\Modules\RolePermission\Repositories\Interfaces\PermissionInterface;
use App\Modules\RolePermission\Transformers\PermissionTransformer;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PermissionController extends ApiController
{
    private PermissionInterface $permissionRepo;
    public function __construct(PermissionInterface $permissionRepo)
    {
        $this->permissionRepo = $permissionRepo;
    }

    /**
     * @OA\Get(
     *      path="/api/permission/index",
     *     tags={"Role-Permissions"},
     *     summary="get list permissions",
     *      security={
     *      {"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="records_per_page",
     *         in="query",
     *         description="records per page",
     *         required=false,
     *         example=-1,
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
    public function getListPermissions(Request $request)
    {
        $recordsPerPage = $request->query('records_per_page') ?? DEFAULT_RECORDS_PER_PAGE;
        $permissions =  $this->permissionRepo->getAllPermissions($recordsPerPage);
        if ($recordsPerPage != GET_ALL_ITEMS) {
            $permissions = transformPaginate($permissions, new PermissionTransformer());
        } else {
            $permissions = fractal($permissions, new PermissionTransformer());
        }
        return $this->respondSuccess($permissions->toArray());
    }
}
