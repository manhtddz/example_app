<?php

namespace App\Services\Services;

use App\Http\Requests\TeamCreateRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Services\Interfaces\IEmployeeRepository;
use App\Services\Interfaces\IProjectRepository;
use App\Services\Interfaces\ITaskRepository;
use App\Services\Repository\EmployeeRepository;
use App\Services\Repository\ProjectRepository;
use App\Services\Repository\TaskRepository;
use Exception;

class ProjectService
{
    private ProjectRepository $projectRepository;
    private TaskRepository $taskRepository;
    private EmployeeRepository $employeeRepository;
    public function __construct(IProjectRepository $projectRepository, ITaskRepository $taskRepository, IEmployeeRepository $employeeRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function findAll()
    {
        return $this->projectRepository->findAll();
    }
    public function findAllWithTeam($teamId)
    {
        return $this->projectRepository->findAllIdWithTeam($teamId);
    }
    public function findAllPaging()
    {
        return $this->projectRepository->findAllPaging(ITEM_PER_PAGE);
    }
    public function findById($id)
    {
        if (!is_numeric($id)) {
            throw new Exception(WRONG_FORMAT_ID);
        }
        $project = $this->projectRepository->findById($id);
        if (!$project) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        return $project;
    }
    public function search(array $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );

        $projects = $this->findAllPaging();

        if (!empty($filtered)) { // Call service when search data is not empty
            $projects = $this->projectRepository
                ->search(ITEM_PER_PAGE, $filtered, $sort, $direction);
        }

        return $projects;
    }
    public function searchDetailsWithProject($projectId, $tab = 'tasks', array $request, $sort, $direction)
    {
        unset($request['tab']);
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );
        $data = $this->taskRepository->findAllWithProjectPaging(ITEM_PER_PAGE, $projectId);
        if ($tab === 'tasks') {
            if (!empty($filtered)) { // Call service when search data is not empty
                $data = $this->taskRepository->searchWithProject(
                    ITEM_PER_PAGE,
                    $projectId,
                    $filtered,
                    $sort,
                    $direction
                );
            }
        }
        if ($tab === 'employees') {
            $data = $this->employeeRepository->findAllWithProjectPaging(ITEM_PER_PAGE, $projectId);

            if (!empty($filtered)) { // Call service when search data is not empty
                $data = $this->employeeRepository->searchWithProject(
                    ITEM_PER_PAGE,
                    $projectId,
                    $filtered,
                    $sort,
                    $direction
                );
            }
        }
        return $data;
    }
    public function create(array $request)
    {
        $result = $this->projectRepository->create($request);

        if (empty($result)) {
            throw new Exception(CREATE_FAILED);
        }

        return $result;
    }
    public function update(int $id, array $request)
    {
        $project = $this->projectRepository->findById($id);

        if (!$project) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        $result = $this->projectRepository->update($id, $request);
        if (!$result) {
            throw new Exception(UPDATE_FAILED);
        }
        return $result;
    }
    public function delete(int $id)
    {
        $project = $this->projectRepository->findById($id);
        if (!$project) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        $result = $this->projectRepository->delete($id);
        if (!$result) {
            throw new Exception(DELETE_FAILED);
        }
        return $result;
    }

    public function prepareConfirmForUpdate($request)
    {
        $validatedData = $request->validated();

        unset($validatedData['teamId']);

        session()->flash('project_data', $validatedData);
    }

    public function prepareConfirmForCreate($request)
    {
        $validatedData = $request->validated();

        session()->flash('project_data', $validatedData);
    }

    public function getSelectData($request)
    {
        $selectedEmployees = $request->input('selectedEmployees') ?? [];
        $selectEmployees = $request->input('selectEmployees') ?? [];

        $unsetData = array_unique($selectedEmployees);
        $unsetData = array_diff($unsetData, $selectEmployees);

        $newData = array_unique($selectEmployees);
        $newData = array_diff($newData, $selectedEmployees);

        return [
            'unsetData' => $unsetData,
            'newData' => $newData
        ];
    }

    public function addEmployeesToProject(array $data, $projectId)
    {
        foreach ($data as $employeeId) {
            $result = $this->projectRepository->createRelationWithEmployee($projectId, $employeeId);
            if (!$result) {
                throw new Exception(CREATE_FAILED);
            }
        }
    }
    public function removeEmployeesFromProject(array $data, $projectId)
    {
        foreach ($data as $employeeId) {
            $result = $this->projectRepository->deleteRelationWithEmployee($projectId, $employeeId);
            if (!$result) {
                throw new Exception(DELETE_FAILED);
            }
        }
    }


}