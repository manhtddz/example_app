<?php

namespace App\Services\Interfaces;

interface ITaskRepository
{
    public function searchByProject($amount, $projectId ,array $requestData);
}
