<?php

namespace App\Modules\Asset\Transformers;

use App\Modules\Asset\Models\Asset;
use App\Modules\RawMediaUpload\Constants\RawMediaUploadCollectionConstants;
use League\Fractal\TransformerAbstract;

class DetailAssetTransformer extends TransformerAbstract
{
    /**
     *
     * @param Asset $asset
     *
     * @return array
     */
    public function transform(Asset $asset): array
    {
        $assetImages = $asset->getMedia('asset_images');
        $assetImagesArray = count($assetImages) > 0 ? $assetImages : null;
        return [
            'id' => $asset['id'],
            'name' => $asset['name'],
            'code' => $asset['code'],
            'management_code' => $asset['management_code'],
            'management_unit' => $asset['management_unit'],
            'original_price' => $asset['original_price'],
            'residual_price' => $asset['residual_price'],
            'insurance_contract' => $asset['insurance_contract'],
            'status' => $asset['status'],
            'asset_images' => $assetImagesArray,
        ];
    }
}
