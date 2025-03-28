<?php

namespace App\Services\Repository;

use App\Models\Task;
use App\Services\Interfaces\ITaskRepository;
use Exception;

class TaskRepository extends BaseRepository implements ITaskRepository
{
    private const MODEL = Task::class;
    public function __construct()
    {
        parent::__construct(self::MODEL);
    }
    public function searchByProject($amount, $projectId, array $requestData, $sort = null, $direction = 'asc')
    {
        try {
            $filters = array_filter(
                $requestData,
                fn($value) => $value !== null && $value !== ''
            );
            $columns = \Schema::getColumnListing((new (self::MODEL))->getTable()); // Take column list
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
            \Log::info($e->getMessage());
            return null;
        }

    }
}