<?php

namespace App\Modules\Config\Helper\Fractal;

use League\Fractal\Serializer\ArraySerializer;

class ResponseSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data): array
    {
        if ($resourceKey === false || is_null($resourceKey)) {
            return $data;
        }
        return [$resourceKey ?: 'data' => $data];
    }

    public function item($resourceKey, $data): array
    {
        if ($resourceKey === false || is_null($resourceKey)) {
            return $data;
        }
        return [$resourceKey ?: 'data' => $data];
    }

    public function null(): ?array
    {
        return ['data' => []];
    }
}
