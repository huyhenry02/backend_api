<?php

namespace App\Modules\RawMediaUpload\Constants;

class RawMediaUploadCollectionConstants
{
    const IDENTIFICATION_FRONT = 'identification_front';
    const IDENTIFICATION_BACK = 'identification_back';
    const FACE_IMAGE = 'face_image';
    const FINGER_PRINT = 'fingerprint';
    const HEALTH_RECORDS = 'health_records';
    const CONTRACT_FILES = 'contract_files';
    const CONTRACT_HEALTH_RECORDS = 'contract_health_records';
    const JOB_TRANSFER_PROOFS = 'job_transfer_proofs';
    const ASSET_IMAGE = 'asset_images';
    const EXAMPLE = 'example';

    public static function getAllValues(): array
    {
        return [
            self::IDENTIFICATION_FRONT,
            self::IDENTIFICATION_BACK,
            self::FACE_IMAGE,
            self::FINGER_PRINT,
            self::HEALTH_RECORDS,
            self::CONTRACT_FILES,
            self::CONTRACT_HEALTH_RECORDS,
            self::JOB_TRANSFER_PROOFS,
            self::ASSET_IMAGE,
            self::EXAMPLE,
        ];
    }
}
