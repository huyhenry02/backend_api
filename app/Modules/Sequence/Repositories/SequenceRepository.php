<?php

namespace App\Modules\Sequence\Repositories;

use App\Modules\Asset\Models\Asset;
use App\Modules\Employee\Models\Contract;
use App\Modules\Employee\Models\CurriculumVitae;
use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\Health;
use App\Modules\MasterData\UnitLevel\Models\UnitLevel;
use App\Modules\Sequence\Models\Sequence;
use App\Modules\Sequence\Repositories\Interfaces\SequenceInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class SequenceRepository extends BaseRepository implements SequenceInterface
{
    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Sequence::class;
    }

    /**
     * @param string $type
     * @param int    $length
     *
     * @return string
     */
    public function generateCode(string $type = EMPLOYEE_CODE, int $length = SEQUENCE_DEFAULT_LENGTH): string
    {
        $sequence = $this->_model->where('type', $type)->orderBy('data', 'DESC')->first();
        $number = $sequence ? $sequence->data + 1 : 1;
        $this->create([
            'type' => $type,
            'data' => $number
        ]);
        while ($this->checkCode($type, $type . str_pad($number, $length, '0', STR_PAD_LEFT))){
            $number = $number + 1;
            $this->create([
                'type' => $type,
                'data' => $number
            ]);
        }
        return $type . str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    private function checkCode($type, $code){
        $arrModelType = [
            'NS' => Employee::class,
            'HSNS' => CurriculumVitae::class,
            'HDNS' => Contract::class,
            'N' => UnitLevel::class,
            'GS' => Employee::class,
            'HSSK' => Health::class,
            'TS' => Asset::class,
        ];
        $data = DB::table(app($arrModelType[$type])->getTable())->where('code', $code )->get()->toArray();
        $result = false;
        if($data) {
            $result = true;
        }
        return $result;
    }
}
