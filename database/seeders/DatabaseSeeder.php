<?php

namespace Database\Seeders;


use Database\Seeders\Custom\AssetsSeeder;
use Database\Seeders\MasterData\AllowancesSeeder;
use Database\Seeders\MasterData\CitiesSeeder;
use Database\Seeders\MasterData\ContractTypesSeeder;
use Database\Seeders\MasterData\EmploymentsSeeder;
use Database\Seeders\MasterData\InsurancesSeeder;
use Database\Seeders\MasterData\NationalitiesSeeder;
use Database\Seeders\MasterData\PositionsSeeder;
use Database\Seeders\MasterData\ReligionsSeeder;
use Database\Seeders\MasterData\SalaryTypesSeeder;
use Database\Seeders\MasterData\TitlesSeeder;
use Database\Seeders\MasterData\UnitLevelsSeeder;
use Database\Seeders\RolePermission\PermissionSeeder;
use Database\Seeders\RolePermission\SuperAdminSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            AllowancesSeeder::class,
            CitiesSeeder::class,
            EmploymentsSeeder::class,
            InsurancesSeeder::class,
            InsurancesSeeder::class,
            NationalitiesSeeder::class,
            PositionsSeeder::class,
            UnitLevelsSeeder::class,
            TitlesSeeder::class,
            ReligionsSeeder::class,
            ContractTypesSeeder::class,
            SuperAdminSeeder::class,
            SalaryTypesSeeder::class,
            AssetsSeeder::class,
        ]);
    }
}
