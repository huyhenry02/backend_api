<?php

namespace Database\Seeders\Custom;

use App\Enums\CommonStatusEnum;
use App\Modules\Asset\Models\Asset;
use App\Modules\MasterData\Employment\Models\Employment;
use Carbon\Carbon;
use Database\Seeders\CsvSeeder;
use Exception;
use Illuminate\Support\Facades\DB;

class AssetsSeeder extends CsvSeeder
{
    public const NAME = 0;
    public const CODE = 1;
    public const MANAGEMENT_CODE = 2;
    public const MANAGEMENT_UNIT = 3;
    public const ORIGINAL_PRICE = 4;
    public const RESIDUAL_PRICE = 5;
    public const INSURANCE_CONTRACT = 6;

    public function run(): void
    {
        $fileLocation = base_path("database/seeders/SeedFiles/assets.csv");
        $records = $this->readCSV($fileLocation, array('delimiter' => ','));
        $records = array_filter($records);
        array_shift($records);
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            foreach ($records as $row) {
                Asset::updateOrCreate([
                    'code' => $row[self::CODE]
                ], [
                    'name' => $row[self::NAME],
                    'code' => $row[self::CODE],
                    'management_code' => $row[self::MANAGEMENT_CODE],
                    'management_unit' => $row[self::MANAGEMENT_UNIT],
                    'original_price' => $row[self::ORIGINAL_PRICE],
                    'residual_price' => $row[self::RESIDUAL_PRICE],
                    'insurance_contract' => $row[self::INSURANCE_CONTRACT],
                    'status' => CommonStatusEnum::ACTIVE->value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            DB::commit();
            echo "Seed assets successfully \n";
        } catch (Exception $e) {
            DB::rollBack();
            echo "Seed assets fail \n";
            echo $e->getMessage() . "\n";
        }
    }
}
