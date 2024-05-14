<?php

namespace App\Modules\Asset\Repositories;

use App\Modules\Asset\Models\AssetDeliveryHistory;
use App\Modules\Asset\Repositories\Interfaces\AssetDeliveryInterface;
use App\Repositories\BaseRepository;

class AssetDeliveryRepository extends BaseRepository implements AssetDeliveryInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return AssetDeliveryHistory::class;
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
