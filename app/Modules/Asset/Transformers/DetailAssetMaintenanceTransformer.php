<?php

namespace App\Modules\Asset\Transformers;

use App\Modules\Asset\Models\AssetMaintenance;
use League\Fractal\TransformerAbstract;

class DetailAssetMaintenanceTransformer extends TransformerAbstract
{

    /**
     *
     * @param AssetMaintenance $assetMaintenance
     *
     * @return array
     */
    public function transform(AssetMaintenance $assetMaintenance): array
    {
        return [
            'id' => $assetMaintenance['id'],
            'asset_id' => $assetMaintenance['asset_id'],
            'created_date' => $assetMaintenance['created_date'],
            'created_by' => $assetMaintenance['created_by'],
            'reason' => $assetMaintenance['reason'],
            'description' => $assetMaintenance['description'],
            'proposal' => $assetMaintenance['proposal'],
            'code' => $assetMaintenance['code'],
            'causal' => $assetMaintenance['causal'],
        ];
    }

}
