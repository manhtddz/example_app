<?php

namespace App\Services\Repository;

use App\Models\EmployeeProject;
use App\Models\Project;
use App\Services\Interfaces\IProjectRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProjectRepository extends BaseRepository implements IProjectRepository
{
    private const MODEL = Project::class;
    public function __construct()
    {
        parent::__construct(self::MODEL);
    }

    public function findAllWithTeamName()
    {
        try {
            return Project::with([
                'team' => function ($query) {
                    $query->select('id', 'name');
                }
            ])->get();
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
                if ($key === 'team_id') {
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
    public function findAllWithTeamPaging($amount, $teamId)
    {
        try {
            return Project::where('team_id', $teamId)
                ->paginate($amount);
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
                    $query->where($key, 'like', '%' . $value . '%');
                }
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

    public function createRelationWithEmployee($projectId, $employeeId)
    {
        try {
            $result = EmployeeProject::create([
                'project_id' => $projectId,
                'employee_id' => $employeeId
            ]);
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function deleteRelationWithEmployee($projectId, $employeeId)
    {
        try {
            $employeeProject = EmployeeProject::where('employee_id', $employeeId)
                ->where('project_id', $projectId)
                ->first();
            $result = $employeeProject->delete();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
}