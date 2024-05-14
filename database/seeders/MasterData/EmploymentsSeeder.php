<?php

namespace Database\Seeders\MasterData;

use App\Modules\MasterData\Employment\Models\Employment;
use Carbon\Carbon;
use Database\Seeders\CsvSeeder;
use Exception;
use Illuminate\Support\Facades\DB;

class EmploymentsSeeder extends CsvSeeder
{
    public function run(): void
    {
        $fileLocation = base_path("database/seeders/SeedFiles/employments.csv");
        $records = $this->readCSV($fileLocation, array('delimiter' => ','));
        $records = array_filter($records);
        array_shift($records);
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            foreach ($records as $row) {
                Employment::updateOrCreate([
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
            echo "Seed employments successfully \n";
        } catch (Exception $e) {
            DB::rollBack();
            echo "Seed employments fail \n";
            echo $e->getMessage() . "\n";
        }
    }
}
