<?php

namespace App\Modules\Employee\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class UuidTransformer extends TransformerAbstract
{
    /**
     * @param Model $data
     * @return array
     */
    public function transform(Model $data): array
    {
        return [
            'id' => $data->id,
        ];
    }
}
