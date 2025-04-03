<?php

namespace App\Services\Repository;

use App\Models\EmployeeTask;
use App\Models\Task;
use App\Services\Interfaces\ITaskRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TaskRepository extends BaseRepository implements ITaskRepository
{
    private const MODEL = Task::class;
    public function __construct()
    {
        parent::__construct(self::MODEL);
    }
    public function findAllWithProjectPaging($amount, $projectId)
    {
        try {
            return (self::MODEL)::where('project_id', $projectId)
                ->paginate($amount);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function findAllWithEmployeePaging($amount, $employeeId)
    {
        try {
            return (self::MODEL)::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($employeeId) {
                    $query
                        ->from('employee_task')
                        ->join('m_employees', 'employee_task.employee_id', '=', 'm_employees.id')
                        ->whereColumn('employee_task.task_id', 'tasks.id')
                        ->where('m_employees.id', $employeeId)
                        ->where('m_employees.del_flag', IS_NOT_DELETED)
                        ->where('employee_task.del_flag', IS_NOT_DELETED);
                })
                ->paginate($amount);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function searchWithProject($amount, $projectId, array $requestData, $sort = null, $direction = 'asc')
    {
        try {
            $filters = array_filter(
                $requestData,
                fn($value) => $value !== null && $value !== ''
            );
            $columns = Schema::getColumnListing((new (self::MODEL))->getTable()); // Take column list
            $query = (self::MODEL)::query();
            $query->where('project_id', $projectId);
            foreach ($filters as $key => $value) {
                // if (in_array(strtolower($key), $columns)) {
                if ($key === 'name') {
                    $query->where($key, 'like', '%' . $value . '%');
                }
                if ($key === 'task_status') {
                    $query->where($key, $value);
                }
                // }
            }

            if ($sort && in_array(strtolower($sort), $columns)) {
                $query->orderBy($sort, strtolower($direction));
            }

            return $query->paginate($amount);

        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }

    }
    public function searchWithEmployee($amount, $employeeId, array $requestData, $sort = null, $direction = 'asc')
    {
        try {
            $filters = array_filter(
                $requestData,
                fn($value) => $value !== null && $value !== ''
            );
            $columns = Schema::getColumnListing((new (self::MODEL))->getTable()); // Take column list
            $query = (self::MODEL)::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($employeeId) {
                    $query
                        ->from('employee_task')
                        ->join('m_employees', 'employee_task.employee_id', '=', 'm_employees.id')
                        ->whereColumn('employee_task.task_id', 'tasks.id')
                        ->where('m_employees.id', $employeeId)
                        ->where('m_employees.del_flag', IS_NOT_DELETED)
                        ->where('employee_task.del_flag', IS_NOT_DELETED);
                });
            foreach ($filters as $key => $value) {
                // if (in_array(strtolower($key), $columns)) {
                if ($key === 'name') {
                    $query->where($key, 'like', '%' . $value . '%');
                }
                if ($key === 'task_status') {
                    $query->where($key, $value);
                }
                // }
            }

            if ($sort && in_array(strtolower($sort), $columns)) {
                $query->orderBy($sort, strtolower($direction));
            }

            return $query->paginate($amount);

        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }

    }
    public function search($amount, array $requestData, $sort = null, $direction = 'asc')
    {
        try {
            $filters = array_filter(
                $requestData,
                fn($value) => $value !== null && $value !== ''
            );
            $columns = Schema::getColumnListing((new (self::MODEL))->getTable()); // Take column list
            $query = (self::MODEL)::query();
            foreach ($filters as $key => $value) {
                // if (in_array(strtolower($key), $columns)) {
                if ($key === 'name') {
                    $query->where($key, 'like', '%' . $value . '%');
                }
                if ($key === 'project_id') {
                    $query->where($key, $value);
                }
                if ($key === 'task_status') {
                    $query->where($key, $value);
                }
                // }
            }

            if ($sort && in_array(strtolower($sort), $columns)) {
                $query->orderBy($sort, strtolower($direction));
            }

            return $query->paginate($amount);

        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }

    public function createRelationWithEmployee($taskId, $employeeId)
    {
        try {
            $result = EmployeeTask::create([
                'task_id' => $taskId,
                'employee_id' => $employeeId
            ]);
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function deleteRelationWithEmployee($taskId, $employeeId)
    {
        try {
            $employeeTask = EmployeeTask::where('employee_id', $employeeId)
                ->where('task_id', $taskId)
                ->first();
            $result = $employeeTask->delete();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
}