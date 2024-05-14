<?php

namespace Database\Seeders\RolePermission;

use App\Modules\RolePermission\Models\Module;
use App\Modules\RolePermission\Models\Permission;
use Database\Seeders\CsvSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends CsvSeeder
{
    const MODULE_NAME = 0;
    const PERMISSION_NAME = 1;
    const PERMISSION_DESCRIPTION = 2;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileLocation = base_path("database/seeders/SeedFiles/permissions.csv");
        $records = $this->readCSV($fileLocation, array('delimiter' => ','));
        $records = array_filter($records);
        array_shift($records);

        try {
            DB::beginTransaction();
            foreach ($records as $record) {
                if (!array_filter($record)) {
                    continue;
                }

                $module = $this->upsertModule($record[self::MODULE_NAME]);
                if (!$module) {
                    throw new \Exception();
                }

                $permission = Permission::firstWhere([
                    'name' => $record[self::PERMISSION_NAME]
                ]);
                if (!$permission) {
                    Permission::create([
                        'name' => $record[self::PERMISSION_NAME],
                        'module_id' => $module->id,
                        'guard_name' => 'api',
                        'description' => $record[self::PERMISSION_DESCRIPTION]
                    ]);
                }
            }
            DB::commit();
            echo "Seed permissions successfully \n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Seed permissions fail \n";
            echo $e->getMessage() . "\n";
        }
    }

    private function upsertModule($moduleDescription)
    {
        $moduleName = Str::slug($moduleDescription);
        $module = Module::firstWhere([
            'name' => $moduleName
        ]);
        if ($module) {
            $module->update([
              'description' => $moduleDescription
            ]);
            return $module;
        }

        return Module::create([
                'name' => $moduleName,
                'description' => $moduleDescription
            ]);

    }

}
