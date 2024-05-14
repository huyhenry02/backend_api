<?php

namespace Database\Seeders\MasterData;

use App\Models\MasterModels\ContractType;
use Database\Seeders\CsvSeeder;
use Illuminate\Support\Facades\DB;

class ContractTypesSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileLocation = base_path("database/seeders/SeedFiles/contract_types.csv");
        $records = $this->readCSV($fileLocation, array('delimiter' => ','));
        $records = array_filter($records);
        array_shift($records);

        try {
            DB::beginTransaction();
            foreach ($records as $row) {
                if (!array_filter($row)) {
                    continue;
                }

                ContractType::updateOrCreate([
                    'code' => $row[self::code]
                ], [
                    'name' => $row[self::name],
                    'code' => $row[self::code],
                    'status' => $row[self::status],
                ]);
            }
            DB::commit();
            echo "Seed contract types successfully \n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Seed employments fail \n";
            echo $e->getMessage() . "\n";
        }
    }
}
