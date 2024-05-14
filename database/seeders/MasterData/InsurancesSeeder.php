<?php

namespace Database\Seeders\MasterData;

use App\Modules\MasterData\InsurancePolicy\Models\InsurancePolicy;
use Carbon\Carbon;
use Database\Seeders\CsvSeeder;
use Exception;
use Illuminate\Support\Facades\DB;

class InsurancesSeeder extends CsvSeeder
{
    public function run(): void
    {
        $fileLocation = base_path("database/seeders/SeedFiles/insurance_policies.csv");
        $records = $this->readCSV($fileLocation, array('delimiter' => ','));
        $records = array_filter($records);
        array_shift($records);
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            foreach ($records as $row) {
                InsurancePolicy::updateOrCreate([
                    'code' => $row[self::code]
                ], [
                    'name' => $row[self::name],
                    'code' => $row[self::code],
                    'status' => $row[self::status],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            DB::commit();
            echo "Seed insurance policies successfully \n";
        } catch (Exception $e) {
            DB::rollBack();
            echo "Seed insurance policies fail \n";
            echo $e->getMessage() . "\n";
        }
    }
}
