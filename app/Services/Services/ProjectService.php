<?php

namespace App\Services\Services;

use App\Http\Requests\TeamCreateRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Services\Interfaces\IProjectRepository;
use App\Services\Repository\ProjectRepository;
use Exception;

class ProjectService
{
    private ProjectRepository $projectRepository;
    public function __construct(IProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function findAll()
    {
        return $this->projectRepository->findAll();
    }
    public function findAllWithTeamName()
    {
        return $this->projectRepository->findAllWithTeam();
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

        session()->flash('project_data', $validatedData);
    }

    public function prepareConfirmForCreate($request)
    {
        $validatedData = $request->validated();

        session()->flash('project_data', $validatedData);
    }
}