<?php

namespace Database\Seeders\MasterData;

use App\Modules\MasterData\Title\Models\Title;
use App\Modules\MasterData\UnitLevel\Models\UnitLevel;
use Carbon\Carbon;
use Database\Seeders\CsvSeeder;
use Illuminate\Support\Facades\DB;

class TitlesSeeder extends CsvSeeder
{
    public function run(): void
    {
        $fileLocation = base_path("database/seeders/SeedFiles/titles.csv");
        $records = $this->readCSV($fileLocation, array('delimiter' => ','));
        $records = array_filter($records);
        array_shift($records);
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            foreach ($records as $row) {
                if (!array_filter($row)) {
                    continue;
                }
                Title::updateOrCreate([
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
            echo "Seed unit levels successfully \n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Seed unit levels fail \n";
            echo $e->getMessage() . "\n";
        }
    }
}
