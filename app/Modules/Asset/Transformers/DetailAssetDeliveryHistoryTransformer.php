<?php

namespace App\Modules\Asset\Transformers;

use App\Modules\Asset\Models\AssetDeliveryHistory;
use App\Modules\Asset\Models\AssetMaintenance;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class DetailAssetDeliveryHistoryTransformer extends TransformerAbstract
{

    /**
     *
     * @param AssetDeliveryHistory $assetDeliveryHistory
     *
     * @return array
     */
    public function transform(AssetDeliveryHistory $assetDeliveryHistory): array
    {
        return [
            'id' => $assetDeliveryHistory['id'],
            'asset_id' => $assetDeliveryHistory['asset_id'],
            'created_date' => $assetDeliveryHistory['created_date'],
            'receiver' => $assetDeliveryHistory['receiver'],
            'deliver' => $assetDeliveryHistory['deliver'],
            'reason' => $assetDeliveryHistory['reason'],
            'place_of_use' => $assetDeliveryHistory['place_of_use'],
            'attachments' => $assetDeliveryHistory['attachments'],
            'code' => $assetDeliveryHistory['code'],
        ];
    }
}
