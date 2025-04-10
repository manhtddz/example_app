<?php

namespace App\Services\Repository;

use App\Models\Employee;
use App\Services\Interfaces\IEmployeeRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EmployeeRepository extends BaseRepository implements IEmployeeRepository
{
    private const MODEL = Employee::class;
    public function __construct()
    {
        parent::__construct(self::MODEL);
    }
    public function findNotActiveEmployeeByEmail($email)
    {
        try {
            return Employee::withoutGlobalScopes()
                ->where('email', $email)
                ->where('del_flag', IS_DELETED)
                ->first();
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function findActiveEmployeeByEmail($email)
    {
        try {
            return Employee::withoutGlobalScopes()
                ->where('email', $email)
                ->where('del_flag', IS_NOT_DELETED)
                ->first();
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function findAllEmployeeId()
    {
        try {
            return Employee::all()->pluck('id')->toArray();
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function findAllSearchedId(array $requestData, $sort = null, $direction = 'asc')
    {
        try {
            $filters = array_filter(
                $requestData,
                fn($value) => $value !== null && $value !== ''
            );
            $columns = Schema::getColumnListing((new Employee())->getTable());
            $query = Employee::query();
            foreach ($filters as $key => $value) {
                if ($key === 'name') {
                    $query->searchName($value);
                }
                // if (in_array(strtolower($key), $columns)) {
                if ($key === 'email') {
                    $query->where($key, 'like', '%' . $value . '%');
                }
                if ($key === 'team_id') {
                    $query->where($key, $value);
                }
                // }
            }

            if ($sort === 'name') {
                $query->orderByName($direction);
            }

            if ($sort && in_array(strtolower($sort), $columns)) {
                $query->orderBy($sort, strtolower($direction));
            }

            return $query->pluck("id")->toArray();

        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }

    }
    public function findAllWithProjectPaging($amount, $projectId)
    {
        try {
            return Employee::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($projectId) {
                    $query
                        ->from('employee_project')
                        ->join('projects', 'employee_project.project_id', '=', 'projects.id')
                        ->whereColumn('employee_project.employee_id', 'm_employees.id')
                        ->where('projects.id', $projectId)
                        ->where('projects.del_flag', IS_NOT_DELETED)
                        ->where('employee_project.del_flag', IS_NOT_DELETED);
                })
                ->paginate($amount);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function findAllWithTeamPaging($amount, $teamId)
    {
        try {
            return Employee::where('team_id', $teamId)
                ->paginate($amount);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function findAllWithTaskPaging($amount, $taskId)
    {
        try {
            return (self::MODEL)::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($taskId) {
                    $query
                        ->from('employee_task')
                        ->join('tasks', 'employee_task.task_id', '=', 'tasks.id')
                        ->whereColumn('employee_task.employee_id', 'm_employees.id')
                        ->where('tasks.id', $taskId)
                        ->where('tasks.del_flag', IS_NOT_DELETED)
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

            $columns = Schema::getColumnListing((new (self::MODEL))->getTable());

            $query = Employee::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($projectId) {
                    $query
                        ->from('employee_project')
                        ->join('projects', 'employee_project.project_id', '=', 'projects.id')
                        ->whereColumn('employee_project.employee_id', 'm_employees.id')
                        ->where('projects.id', $projectId)
                        ->where('projects.del_flag', IS_NOT_DELETED)
                        ->where('employee_project.del_flag', IS_NOT_DELETED);
                });

            foreach ($filters as $key => $value) {
                if ($key === 'name') {
                    $query->searchName($value);
                }
                if (in_array(strtolower($key), $columns)) {
                    if ($key === 'email') {
                        $query->where($key, 'like', '%' . $value . '%');
                    }
                    if ($key === 'team_id') {
                        $query->where($key, $value);
                    }
                }
            }

            if ($sort === 'name') {
                $query->orderByName($direction);
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
    public function findAllIdWithProject($projectId)
    {
        try {
            $query = Employee::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($projectId) {
                    $query
                        ->from('employee_project')
                        ->join('projects', 'employee_project.project_id', '=', 'projects.id')
                        ->whereColumn('employee_project.employee_id', 'm_employees.id')
                        ->where('projects.id', $projectId)
                        ->where('projects.del_flag', IS_NOT_DELETED)
                        ->where('employee_project.del_flag', IS_NOT_DELETED);
                });

            return $query->pluck('id');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function findAllIdWithTask($taskId)
    {
        try {
            $query = Employee::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($taskId) {
                    $query
                        ->from('employee_task')
                        ->join('tasks', 'employee_task.task_id', '=', 'tasks.id')
                        ->whereColumn('employee_task.employee_id', 'm_employees.id')
                        ->where('tasks.id', $taskId)
                        ->where('tasks.del_flag', IS_NOT_DELETED)
                        ->where('employee_task.del_flag', IS_NOT_DELETED);
                });

            return $query->pluck('id');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function searchWithTeam($amount, $teamId, array $requestData, $sort = null, $direction = 'asc')
    {
        try {
            $filters = array_filter(
                $requestData,
                fn($value) => $value !== null && $value !== ''
            );
            $columns = Schema::getColumnListing((new (self::MODEL))->getTable()); // Take column list
            $query = (self::MODEL)::query();
            $query->where("team_id", $teamId);
            foreach ($filters as $key => $value) {
                if ($key === 'name') {
                    $query->searchName($value);
                }
                if ($key === 'email') {
                    $query->where($key, 'like', '%' . $value . '%');
                }
            }

            if ($sort === 'name') {
                $query->orderByName($direction);
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
    public function searchWithTask($amount, $taskId, array $requestData, $sort = null, $direction = 'asc')
    {
        try {
            $filters = array_filter(
                $requestData,
                fn($value) => $value !== null && $value !== ''
            );
            $columns = Schema::getColumnListing((new (self::MODEL))->getTable()); // Take column list
            $query = (self::MODEL)::withoutGlobalScopes()
                ->where('del_flag', IS_NOT_DELETED)
                ->whereExists(function ($query) use ($taskId) {
                    $query
                        ->from('employee_task')
                        ->join('tasks', 'employee_task.task_id', '=', 'tasks.id')
                        ->whereColumn('employee_task.employee_id', 'm_employees.id')
                        ->where('tasks.id', $taskId)
                        ->where('tasks.del_flag', IS_NOT_DELETED)
                        ->where('employee_task.del_flag', IS_NOT_DELETED);
                });
            foreach ($filters as $key => $value) {
                // if (in_array(strtolower($key), $columns)) {
                if ($key === 'name') {
                    $query->searchName($value);
                }
                if ($key === 'email') {
                    $query->where($key, 'like', '%' . $value . '%');
                }
                // }
            }
            if ($sort === 'name') {
                $query->orderByName($direction);
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
}