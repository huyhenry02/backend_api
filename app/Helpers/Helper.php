<?php

use App\Enums\ActionLogTypeEnum;
use App\Enums\CommonStatusEnum;
use App\Modules\Log\Repositories\LogRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\Fractal\Fractal;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

if (!function_exists('checkAccess')) {
    function checkAccess($permissionName)
    {
        $user = Auth::user();
        try {
            $roles = $user->roles;
            $role = $roles[0] ?? false;
            if (($role['status'] ?? CommonStatusEnum::INACTIVE->value) !== CommonStatusEnum::ACTIVE->value) {
                return false;
            }
            return $user->hasPermissionTo($permissionName, 'api');
        } catch (PermissionDoesNotExist|Exception $ex) {
            return false;
        }
    }
}

if (!function_exists('transformPaginate')) {
    function transformPaginate($data, $ruleTransform): Fractal
    {
        return fractal()
            ->collection($data)
            ->transformWith($ruleTransform)
            ->paginateWith(new IlluminatePaginatorAdapter($data));
    }
}

if (!function_exists('checkChangeFileRequired')) {
    function checkChangeFileRequired(Model $model, array $media, array $collectionNames): void
    {
        if (!empty($media['delete'])) {
            foreach ($collectionNames as $collectionName) {
                if (
                    empty($media['new'][$collectionName]) &&
                    !empty($media['delete'][$collectionName]) &&
                    (count($model->getMedia($collectionName)) === count($media['delete'][$collectionName]))
                ) {
                    throw new RuntimeException(__('messages.collections.' . $collectionName) . ' - ' . __('messages.media_required'));
                }
            }
        }
    }
}

function array_diff_assoc_recursive($array1, $array2)
{
    foreach ($array1 as $key => $value) {
        if (array_key_exists($key, $array2)) {
            if (is_array($value) && is_array($array2[$key])) {
                $diff = array_diff_assoc_recursive($value, $array2[$key]);
                if (empty($diff)) {
                    unset($array1[$key]);
                } else {
                    $array1[$key] = $diff;
                }
            } elseif (is_object($value) && is_object($array2[$key])) {
                if ($value != $array2[$key]) {
                    $array1[$key] = $value;
                } else {
                    unset($array1[$key]);
                }
            } elseif ($value == $array2[$key]) {
                unset($array1[$key]);
            }
        }
    }
    return $array1;
}


if (!function_exists('handleLogUpdateData')) {
    function handleLogUpdateData(array $originalData, array $newData, $model): void
    {
        $employeeId = auth()->user()->employee_id;
        $changedData = [];
        $oldData = [];

            $subArrays = ['contract_working_histories', '`contract_allowances`', 'contract_insurance_processed_histories', 'working_histories'];
        $mainFields = array_diff_key($newData, array_flip($subArrays));
        $changedMainData = array_diff_assoc_recursive($mainFields, $originalData);
        if (!empty($changedMainData)) {
            $oldData = array_intersect_key($originalData, $changedMainData);
            $changedData = $changedMainData;
        }

        foreach ($subArrays as $subArray) {
            if (isset($originalData[$subArray]) && isset($newData[$subArray])) {
                $originalSubArray = $originalData[$subArray];
                $newSubArray = $newData[$subArray];

                $originalSubArrayById = array_column($originalSubArray, null, 'id');
                $newSubArrayById = array_column($newSubArray, null, 'id');

                $removedIds = array_diff(array_keys($originalSubArrayById), array_keys($newSubArrayById));
                foreach ($removedIds as $removedId) {
                    $oldData[$subArray][$removedId] = $originalSubArrayById[$removedId];
                    $changedData[$subArray][$removedId] = null;
                }

                foreach ($newSubArray as $item) {
                    $id = $item['id'] ?? null;
                    if ($id && isset($originalSubArrayById[$id])) {
                        $originalItem = $originalSubArrayById[$id];
                        $changedItemData = array_diff_assoc_recursive($item, $originalItem);
                        if (!empty($changedItemData)) {
                            $oldData[$subArray][$id] = array_intersect_key($originalItem, $changedItemData);
                            $changedData[$subArray][$id] = $changedItemData;
                        }
                    } else {
                        if (!empty($item['id']) && !isset($originalSubArrayById[$item['id']])) {
                            $changedData[$subArray][$id] = $item;
                        }
                    }
                }
            }
        }

        if (!empty($changedData)) {
            $model_type = get_class($model);
            $logChangedData = json_encode($changedData);
            $logOldData = json_encode($oldData);
            LogRepository::createLog($newData['id'], $model_type, ActionLogTypeEnum::UPDATE->value, $employeeId, $logChangedData, $logOldData);
        }
    }
}


if (!function_exists('handleLogDeleteData')) {
    function handleLogDeleteData($data): void
    {
        $model_type = get_class($data);
        $employeeId = auth()->user()->employee_id;
        $logData = json_encode($data->toArray());
        LogRepository::createLog($data->id, $model_type, ActionLogTypeEnum::DELETE->value, $employeeId, '{}', $logData);
    }
}
if (!function_exists('handleLogCreateData')) {
    function handleLogCreateData($data, $model, $employeeId = null): void
    {
        $employeeId = $employeeId ?? auth()->user()->employee_id;
        $model_type = get_class($model);
        $logData = json_encode($data);
        LogRepository::createLog($model['id'], $model_type, ActionLogTypeEnum::CREATE->value, $employeeId, $logData, '{}');
    }
}
