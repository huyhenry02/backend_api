<?php

namespace App\Modules\Example\Transformmers;

use App\Modules\Example\Models\Example;
use League\Fractal\TransformerAbstract;

class ExampleTransformer extends TransformerAbstract
{
    public function transform(Example $example)
    {
        return [
            'id' => $example->id,
            'name' => $example->name,
            'des' => $example->des,
            'files' => $example->getMedia('example')
        ];
    }
}
