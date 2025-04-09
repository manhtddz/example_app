<?php

namespace App\Services\Services;

use App\Services\Interfaces\IEmployeeRepository;
use App\Services\Interfaces\ITaskRepository;
use App\Services\Repository\EmployeeRepository;
use App\Services\Repository\TaskRepository;
use Exception;

class TaskService
{
    private TaskRepository $taskRepository;
    private EmployeeRepository $employeeRepository;
    public function __construct(ITaskRepository $taskRepository, IEmployeeRepository $employeeRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function findAll()
    {
        return $this->taskRepository->findAll();
    }
    public function findAllPaging()
    {
        return $this->taskRepository->findAllPaging(ITEM_PER_PAGE);
    }
    public function findById($id)
    {
        if (!is_numeric($id)) {
            throw new Exception(WRONG_FORMAT_ID);
        }
        $task = $this->taskRepository->findById($id);
        if (!$task) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        return $task;
    }
    public function searchDetailsWithTask($taskId, $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );
        $data = $this->employeeRepository->findAllWithTaskPaging(ITEM_PER_PAGE, $taskId);
        if (!empty($filtered)) { // Call service when search data is not empty
            $data = $this->employeeRepository->searchWithTask(
                ITEM_PER_PAGE,
                $taskId,
                $filtered,
                $sort,
                $direction
            );
        }

        return $data;
    }
    public function search(array $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );

        $tasks = $this->findAllPaging();

        if (!empty($filtered)) { // Call service when search data is not empty
            $tasks = $this->taskRepository
                ->search(
                    ITEM_PER_PAGE,
                    $filtered,
                    $sort,
                    $direction
                );
        }

        return $tasks;
    }
    public function create(array $request)
    {
        $result = $this->taskRepository->create($request);

        if (empty($result)) {
            throw new Exception(CREATE_FAILED);
        }

        return $result;
    }
    public function update(int $id, array $request)
    {
        $task = $this->taskRepository->findById($id);

        if (!$task) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        $result = $this->taskRepository->update($id, $request);
        if (!$result) {
            throw new Exception(UPDATE_FAILED);
        }
        return $result;
    }
    public function delete(int $id)
    {
        $task = $this->taskRepository->findById($id);
        if (!$task) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        $result = $this->taskRepository->delete($id);
        if (!$result) {
            throw new Exception(DELETE_FAILED);
        }
        return $result;
    }

    public function prepareConfirmForUpdate($request)
    {
        $validatedData = $request->validated();

        session()->flash('task_data', $validatedData);
    }

    public function prepareConfirmForCreate($request)
    {
        $validatedData = $request->validated();
        unset($validatedData['projectId']);

        session()->flash('task_data', $validatedData);
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

    public function addEmployeesToProject(array $data, $taskId)
    {
        foreach ($data as $employeeId) {
            $result = $this->taskRepository->createRelationWithEmployee($taskId, $employeeId);
            if (!$result) {
                throw new Exception(CREATE_FAILED);
            }
        }
    }
    public function removeEmployeesFromProject(array $data, $taskId)
    {
        foreach ($data as $employeeId) {
            $result = $this->taskRepository->deleteRelationWithEmployee($taskId, $employeeId);
            if (!$result) {
                throw new Exception(DELETE_FAILED);
            }
        }
    }
}