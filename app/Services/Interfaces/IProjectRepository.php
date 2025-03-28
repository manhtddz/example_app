<?php

namespace App\Services\Interfaces;

interface IProjectRepository
{
    public function search($amount, array $requestData);
}
