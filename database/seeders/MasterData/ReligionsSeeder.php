<?php

namespace Database\Seeders\MasterData;

use App\Modules\MasterData\Religion\Models\Religion;
use App\Modules\MasterData\Title\Models\Title;
use Carbon\Carbon;
use Database\Seeders\CsvSeeder;
use Illuminate\Support\Facades\DB;

class ReligionsSeeder extends CsvSeeder
{
    public function run(): void
    {
        $fileLocation = base_path("database/seeders/SeedFiles/religions.csv");
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
                Religion::updateOrCreate([
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
