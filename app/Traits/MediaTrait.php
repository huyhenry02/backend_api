<?php

namespace App\Traits;

use App\Modules\RawMediaUpload\Models\Media;
use App\Modules\RawMediaUpload\Models\RawMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

trait MediaTrait
{
    use InteractsWithMedia;

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function addMediaToCollection(HasMedia $newModel, HasMedia $oldModel): void
    {
        $mediaCollections = $oldModel->getRegisteredMediaCollections();
        foreach ($mediaCollections as $collection) {
            $media = $oldModel->getMedia($collection->name);
            if (!$media->isEmpty()) {
                foreach ($media as $medium) {
                    $newModel->addMedia($medium->getPath())
                        ->toMediaCollection($collection->name);
                }
            }

        }
        $oldModel->delete();
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function moveMediaToNewCollection(HasMedia $newModel, HasMedia $oldModel): void
    {
        $mediaCollections = $oldModel->getRegisteredMediaCollections();
        foreach ($mediaCollections as $collection) {
            $media = $oldModel->getMedia($collection->name);
            if (!$media->isEmpty()) {
                foreach ($media as $medium) {
                    $newModel->copyMedia($medium->getPath())
                        ->toMediaCollection($collection->name);
                }
            }

        }
        $oldModel->delete();
    }

    public function deleteFile(array|string $mediaIds, string $collectionName, $modelId = null): bool
    {
        if (empty($mediaIds) || empty($collectionName)) {
            return false;
        }

        if (is_array($mediaIds)) {
            $result = $this->deleteMultipleFiles($mediaIds, $collectionName, $modelId);
        } else {
            $result = $this->deleteOneFile($mediaIds, $collectionName);
        }

        return $result;
    }

    private function deleteMultipleFiles($mediaIds, $collectionName, $modelId): bool
    {
        DB::beginTransaction();
        try {
            $query = Media::whereIn('uuid', $mediaIds)
                ->where([
                    ['collection_name', '=', $collectionName],
                    ['model_id', '=', $modelId]
                ]);

            $listDeleteMedias = $query->get();
            if (count($listDeleteMedias) != count($mediaIds)) {
                throw new \Exception();
            }
            $query->delete();
            DB::commit();

            foreach ($listDeleteMedias as $deleteMedia) {
                $mediaPath = Config::get('media-library.prefix') . '/' . $deleteMedia->id;
                if (Storage::disk('public')->directoryExists($mediaPath)) {
                    Storage::disk('public')->deleteDirectory($mediaPath);
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function deleteOneFile($mediaId, $collectionName): bool
    {
        try {
            $media = Media::where([
                [ 'uuid', '=', $mediaId ],
                [ 'collection_name', '=', $collectionName ],
            ]);
            if (!$media) {
                throw new \Exception();
            }
            $modelType = $media->model_type;

            $model = $modelType::find($media->model_id);
            $model->deleteMedia($media->id);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    private function updateMedia($model, array $media, array $collectionNames): void
    {
        $new = $media['new'] ?? [];
        $delete = $media['delete'] ?? [];
        foreach ($collectionNames as $collectionName) {
            if (!empty($delete[$collectionName])) {
                $model->clearMediaCollection($collectionName);
            }
            if (!empty($new[$collectionName])) {
                $rawMedia = RawMedia::find($new[$collectionName]);
                $this->moveMediaToNewCollection($model, $rawMedia);
            }
        }
    }
}
