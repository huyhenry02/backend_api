<?php

namespace App\Http\Controllers\Api\GlobalSearch;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Asset\Models\Asset;
use App\Modules\Asset\Models\AssetDeliveryHistory;
use App\Modules\Asset\Models\AssetMaintenance;
use App\Modules\Employee\Models\Employee;
use App\Modules\GlobalSearch\Requests\GlobalSearchRequest;
use App\Modules\GlobalSearch\Transformers\GlobalSearchTransformer;
use App\Modules\RolePermission\Models\Role;
use Illuminate\Http\JsonResponse;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;

class SearchController extends ApiController
{
    protected string $statusOption = 'status';
    protected string $deleteOption = 'delete';

    public function __construct()
    {
    }

    public function search(GlobalSearchRequest $request): JsonResponse
    {
        $keyword = $request->input('keyword');
        $options = explode(',', $request->input('options', ''));
        $page = $request->input('page', DEFAULT_PAGE);
        $perPage = $request->input('perPage', DEFAULT_RECORDS_PER_PAGE);

        $statusCondition = in_array($this->statusOption, $options) ? ['active', 'inactive'] : ['active'];
        $deleteCondition = in_array($this->deleteOption, $options);
        $module = $request->input('module');
        $models = $this->getSearchableModels($statusCondition, $deleteCondition, $module);

        $globalSearch = new Search();

        foreach ($models as $model) {
            $globalSearch->registerModel($model['module'], $model['column']);
        }

        $total = $globalSearch->search($keyword)->count();
        $searchResults = $globalSearch->search($keyword)
            ->forPage($page, $perPage)
            ->toArray();

        $responseData = $this->formatSearchResults($searchResults);

        $respData = [
            'data'  => $responseData,
            'meta' => [
                'pagination' => [
                    'total' => $total,
                    'totalPage' => ceil((int)$total / (int)$perPage),
                    'perPage' => (int)$perPage,
                    'currentPage' => (int)$page,
                ]
            ]
        ];

        return $this->respondSuccess($respData);
    }

    private function getSearchableModels(array $statusCondition, bool $deleteCondition, $module = false, $parent = ''): array
    {
        $models = [
            'employee' => [
                'module' => Employee::class,
                'column' => function (ModelSearchAspect $modelSearchAspect) use ($statusCondition, $deleteCondition) {
                    $modelSearchAspect
                        ->addSearchableAttribute('type')
                        ->with('curriculumVitae')
                        ->whereIn('status', $statusCondition);

                    if (!$deleteCondition) {
                        $modelSearchAspect->whereNull('deleted_at');
                    }
                },
            ],
            'role' => [
                'module' => Role::class,
                'column' => function (ModelSearchAspect $modelSearchAspect) use ($statusCondition, $deleteCondition) {
                    $modelSearchAspect
                        ->addSearchableAttribute('name')
                        ->addSearchableAttribute('description')
                        ->whereIn('status', $statusCondition);

                    if (!$deleteCondition) {
                        $modelSearchAspect->whereNull('deleted_at');
                    }
                },
            ],
            'asset' => [
                'module' => Asset::class,
                'column' => function (ModelSearchAspect $modelSearchAspect) use ($statusCondition, $deleteCondition) {
                    $modelSearchAspect
                        ->addSearchableAttribute('name')
                        ->addSearchableAttribute('code')
                        ->whereIn('status', $statusCondition);
                },
            ],
            'asset_delivery' => [
                'module' => AssetDeliveryHistory::class,
                'column' => function (ModelSearchAspect $modelSearchAspect) use ($statusCondition, $deleteCondition, $parent, $module) {
                    $modelSearchAspect
                        ->addSearchableAttribute('code')
                        ->whereIn('status', $statusCondition);
                    if ($parent && $module) {
                        $modelSearchAspect->where('asset_id', $parent);
                    }
                },
            ],
            'asset_maintenance' => [
                'module' => AssetMaintenance::class,
                'column' => function (ModelSearchAspect $modelSearchAspect) use ($statusCondition, $deleteCondition, $parent, $module) {
                    $modelSearchAspect
                        ->addSearchableAttribute('code')
                        ->whereIn('status', $statusCondition);
                    if ($parent && $module) {
                        $modelSearchAspect->where('asset_id', $parent);
                    }
                },
            ],
        ];
        $result = [];
        if ($module) {
            $result[] = $models[$module];
        } else {
            foreach ($models as $key => $value) {
                $result[] = $value;
            }
        }
        return $result;
    }

    private function formatSearchResults(array $searchResults): array
    {
        $responseData = [];
        foreach ($searchResults as $searchData) {
            $dataCovert = fractal($searchData, new GlobalSearchTransformer())->toArray();
            $responseData[$searchData->type][] = $dataCovert;
        }

        return $responseData;
    }
}
