<?php

namespace App\Services\Interfaces;

interface ITaskRepository
{
    public function searchWithProject($amount, $projectId, array $requestData);
    public function searchWithEmployee($amount, $employeeId, array $requestData);
    public function findAllWithProjectPaging($amount, $projectId);
    public function findAllWithEmployeePaging($amount, $employeeId);
    public function search($amount, array $requestData);

}
