<?php

namespace App\Modules\Log\Repositories;

use App\Modules\Appointment\Models\Appointment;
use App\Modules\Asset\Models\Asset;
use App\Modules\Asset\Models\AssetDeliveryHistory;
use App\Modules\Asset\Models\AssetMaintenance;
use App\Modules\Employee\Models\Contract;
use App\Modules\Employee\Models\CurriculumVitae;
use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\Health;
use App\Modules\Hierarchy\Models\Hierarchy;
use App\Modules\Log\Model\Log;
use App\Modules\Log\Repositories\Interfaces\LogInterface;
use App\Modules\RolePermission\Models\Role;
use App\Modules\User\Models\User;
use App\Repositories\BaseRepository;

class LogRepository extends BaseRepository implements LogInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return Log::class;
    }

    protected array $modelNamespaceMapping = [
        'asset' => Asset::class,
        'appointment' => Appointment::class,
        'employee' => Employee::class,
        'health' => Health::class,
        'curriculum_vitae' => CurriculumVitae::class,
        'contract' => Contract::class,
        'role' => Role::class,
        'asset_maintenance' => AssetMaintenance::class,
        'asset_delivery_history' => AssetDeliveryHistory::class,
        'hierarchy' => Hierarchy::class,
        'user' => User::class,
    ];

    public static function createLog(string $id, string $model_type, string $action, string $employeeId, $newData = '{}', $oldData = '{}'): bool
    {
        $log = new Log([
            'model_id' => $id,
            'model_type' => $model_type,
            'employee_id' => $employeeId,
            'action' => $action,
            'new_data' => $newData,
            'old_data' => $oldData,
        ]);
        return $log->save();
    }

    public function getLogData(string $model_type, string $id)
    {
        $modelNamespace = $this->modelNamespaceMapping[$model_type] ?? null;
        return $this->_model->where([
            ['model_type', $modelNamespace],
            ['model_id', $id]
        ])->orderBy('created_at', 'desc')
          ->get();
    }
}
