<?php

namespace App\Services\Services;


use App\Services\Interfaces\ITaskRepository;
use App\Services\Repository\TaskRepository;
use Exception;

class TaskService
{
    private TaskRepository $taskRepository;
    public function __construct(ITaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
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
    public function search($taskId, array $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );

        $tasks = $this->findAllPaging();

        if (!empty($filtered)) { // Call service when search data is not empty
            $tasks = $this->taskRepository
                ->searchByProject(
                    ITEM_PER_PAGE,
                    $taskId,
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

        session()->flash('task_data', $validatedData);
    }
}