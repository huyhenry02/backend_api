<?php

namespace App\Http\Controllers\Api\FileUpload;

use App\Http\Controllers\Api\ApiController;
use App\Modules\RawMediaUpload\Repositories\Interfaces\RawMediaUploadInterface;
use App\Modules\RawMediaUpload\Requests\RawMediaUploadRequest;
use App\Traits\MediaTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class FileController extends ApiController
{
    use MediaTrait;

    protected RawMediaUploadInterface $rawMediaUploadRepository;

    public function __construct(RawMediaUploadInterface $rawMediaUpload)
    {
        $this->rawMediaUploadRepository = $rawMediaUpload;

    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function upload(RawMediaUploadRequest $request)
    {
        $files = $request->file('files');
        $collection = $request->input('collection');
        $rawMediaId = $request->input('raw_media_id');

        if (!is_null($rawMediaId)) {
            $rawMedia = $this->rawMediaUploadRepository->find($rawMediaId);
        } else {
            $rawMedia = $this->rawMediaUploadRepository->create([]);
        }
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $rawMedia->addMedia($file)->toMediaCollection($collection);
            }
        }
        $respData = [
            'data' => $rawMedia->id
        ];
        return $this->respondSuccess($respData);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function test(Request $request)
    {
        $rawMediaId = $request->input('raw_media_id');
        $rawMedia = $this->rawMediaUploadRepository->find($rawMediaId);
        $newRawMedia = $this->rawMediaUploadRepository->create([]);
        $this->addMediaToCollection($newRawMedia, $rawMedia);

    }
}
