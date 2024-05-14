<?php

namespace App\Modules\MasterData;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class MasterDataTransformer extends TransformerAbstract
{
    public function transform(Model $model)
    {
        return [
            'id'       => $model->id,
            'name'     => $model->name,
            'code'     => $model->code,
            'status'   => $model->status ?? '',
        ];
    }
}
