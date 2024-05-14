<?php

namespace App\Providers;

//use App\Module\User\Repositories\Interfaces\UserInterface;
//use App\Module\User\Repositories\UserRepository;
//use App\Modules\MasterData\Repositories\Interfaces\MasterDataInterface;
//use App\Modules\MasterData\Repositories\MasterDataRepository;
use App\Modules\RolePermission\Repositories\Interfaces\ModuleInterface;
use App\Modules\RolePermission\Repositories\Interfaces\RoleInterface;
use App\Modules\RolePermission\Repositories\ModuleRepository;
use App\Modules\RolePermission\Repositories\RoleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoriesProvider extends ServiceProvider
{

    public function register(): void
    {
        $repositories = [
            'Example',
            'User',
            'Role',
            'Employee',
            'Sequence',
            'RawMediaUpload',
            'Hierarchy',
            'Appointment',
            'Asset',
            'Log'
        ];
        foreach ($repositories as $repository) {
            $this->app->bind('App\Modules\\' . $repository . '\Repositories\Interfaces\\' . $repository . 'Interface',
                'App\Modules\\' . $repository . '\Repositories\\' . $repository . 'Repository'
            );
        }
        $this->app->bind(RoleInterface::class,
            RoleRepository::class
        );
        $this->app->bind(ModuleInterface::class,
            ModuleRepository::class
        );

        $subRepositories = [
            'Employee.CurriculumVitae',
            'Employee.EmployeeLog',
            'Employee.WorkingHistory',
            'Employee.Health',
            'Employee.Contract',
            'Employee.ContractAllowance',
            'Employee.ContractInsuranceProcessedHistory',
            'Employee.ContractWorkingHistory',
            'RolePermission.Permission',
            'Asset.AssetDelivery',
            'Asset.AssetMaintenance',
        ];
        foreach ($subRepositories as $subRepository) {
            [$parentRepository, $subRepository] = explode('.', $subRepository);
            $this->app->bind('App\Modules\\' . $parentRepository . '\Repositories\Interfaces\\' . $subRepository . 'Interface',
                'App\Modules\\' . $parentRepository . '\Repositories\\' . $subRepository . 'Repository'
            );
        }

        $masterData = [
            'MasterData.Allowance',
            'MasterData.City',
            'MasterData.Employment',
            'MasterData.InsurancePolicy',
            'MasterData.Nationality',
            'MasterData.Position',
            'MasterData.UnitLevel',
            'MasterData.Title',
            'MasterData.Religion',
            'MasterData.ContractType',
            'MasterData.SalaryType',
        ];
        foreach ($masterData as $masterDatum) {
            [$parentRepository, $subRepository] = explode('.', $masterDatum);
            $this->app->bind('App\Modules\\' . $parentRepository . '\\' . $subRepository . '\Repositories\Interfaces\\' . $subRepository . 'Interface',
                'App\Modules\\' . $parentRepository . '\\' . $subRepository . '\Repositories\\' . $subRepository . 'Repository'
            );
        }
//        $this->app->bind(UserInterface::class,
//            UserRepository::class
//        );

    }
}
