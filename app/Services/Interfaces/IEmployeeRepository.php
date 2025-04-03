<?php

namespace App\Services\Interfaces;

interface IEmployeeRepository
{
    public function findNotActiveEmployeeByEmail($email);
    public function findActiveEmployeeByEmail($email);
    public function findAllEmployeeId();
    public function findAllSearchedId(array $requestData);
    public function findAllWithProjectPaging($amount, $projectId);
    public function findAllWithTeamPaging($amount, $teamId);
    public function findAllWithTaskPaging($amount, $taskId);
    public function searchWithProject($amount, $projectId, array $requestData);
    public function searchWithTeam($amount, $teamId, array $requestData);
    public function searchWithTask($amount, $taskId, array $requestData);

}
