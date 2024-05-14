<?php

namespace App\Modules\Asset\Repositories;

use App\Modules\Asset\Models\Asset;
use App\Modules\Asset\Repositories\Interfaces\AssetInterface;
use App\Repositories\BaseRepository;

class AssetRepository extends BaseRepository implements AssetInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Asset::class;
    }
    public function update($id, array $attributes): mixed
    {
        $response = $this->find($id);
        if ($response) {
            $response->update($attributes);
            return $response;
        }
        return false;
    }
}
