<?php

namespace App\Modules\Asset\Repositories;

use App\Modules\Asset\Models\AssetMaintenance;
use App\Modules\Asset\Repositories\Interfaces\AssetMaintenanceInterface;
use App\Repositories\BaseRepository;

class AssetMaintenanceRepository extends BaseRepository implements AssetMaintenanceInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return AssetMaintenance::class;
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
