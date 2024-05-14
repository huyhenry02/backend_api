<?php

namespace Database\Seeders\RolePermission;

use App\Modules\Employee\Models\CurriculumVitae;
use App\Modules\Employee\Repositories\Interfaces\CurriculumVitaeInterface;
use App\Modules\Employee\Repositories\Interfaces\EmployeeInterface;
use App\Modules\RolePermission\Models\ModelHasRole;
use App\Modules\RolePermission\Models\Permission;
use App\Modules\RolePermission\Models\RoleHasPermission;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\Interfaces\UserInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    private string $employeeCode = 'NS00000';
    private string $curriculumVitaeCode = 'HSNS00000';
    private UserInterface $userRepo;
    private RoleInterface $roleRepo;
    private EmployeeInterface $employeeRepo;
    private CurriculumVitaeInterface $curriculumVitaeRepo;
    public function __construct(
        UserInterface $userRepo,
        RoleInterface $roleRepo,
        EmployeeInterface $employeeRepo,
        CurriculumVitaeInterface $curriculumVitaeRepo
    )
    {
        $this->userRepo = $userRepo;
        $this->roleRepo =  $roleRepo;
        $this->employeeRepo =  $employeeRepo;
        $this->curriculumVitaeRepo =  $curriculumVitaeRepo;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $employee = $this->employeeRepo->getFirstRow(['code', $this->employeeCode]);
            if (!$employee) {
                $employee = $this->employeeRepo->create([
                    'code' => $this->employeeCode,
                ]);
            }
            $cv = $this->curriculumVitaeRepo->getFirstRow(['employee_id', $employee->id]);
            if (!$cv) {
                $cv = $this->curriculumVitaeRepo->create([
                    'employee_id' => $employee->id,
                    'code' => $this->curriculumVitaeCode,
                    'name' => 'Super Admin',
                    'email' => 'superadmin@wishcare.com',
                    'phone_number' => '0999999999',
                    'identification' => '000000000000',
                ]);
            }
            echo 'check curriculum vitae successfully' . "\n";
            //Create super admin
            $user = [
                'name' => 'superadmin',
                'employee_id' => $employee->id,
                'email' => 'superadmin@wishcare.com',
                'username' => $this->employeeCode,
                'password' => bcrypt('Superadmin1@'),
            ];
            $response = User::firstWhere([
                'email' => $user['email']
            ]);
            if (!$response) {
                $response = $this->userRepo->create($user);
            }
            $role = $this->roleRepo->getByParams(['name' => 'admin'], 'id', 'ASC', true)->first();

            if (!$role) {
                $role = $this->roleRepo->create([
                    'name' => 'admin',
                    'description' => 'Admin',
                    'guard_name' => 'api',
                ]);
            }
            $permissions = Permission::get()->pluck('id');
            foreach ($permissions as $permission){
                RoleHasPermission::updateOrInsert(['permission_id' => $permission, 'role_id' => $role->id], ['permission_id' => $permission, 'role_id' => $role->id]);
            }
            ModelHasRole::updateOrInsert(
                ['role_id' => $role->id, 'model_type' => 'App\Modules\User\Models\User', 'model_id' => $response->id]
                , ['role_id' => $role->id, 'model_type' => 'App\Modules\User\Models\User', 'model_id' => $response->id]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage() . "\n";
        }
    }
}
