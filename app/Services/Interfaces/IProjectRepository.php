<?php

namespace App\Services\Interfaces;

interface IProjectRepository
{
    public function search($amount, array $requestData);
    // public function findAllWithTeamName();
    public function findAllWithTeamPaging($amount, $teamId);
    public function searchWithTeam($amount, $teamId, array $requestData);
}
