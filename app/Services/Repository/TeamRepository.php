<?php

namespace App\Services\Repository;

use App\Models\Team;
use App\Models\TeamProject;
use App\Services\Interfaces\ITeamRepository;
use Exception;
use Log;

class TeamRepository extends BaseRepository implements ITeamRepository
{
    private const MODEL = Team::class;
    public function __construct()
    {
        parent::__construct(self::MODEL);
    }
    public function createRelationWithProject($teamId, $projectId)
    {
        try {
            $result = TeamProject::create([
                'team_id' => $teamId,
                'project_id' => $projectId
            ]);
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
    public function deleteRelationWithProject($teamId, $projectId)
    {
        try {
            $teamProject = TeamProject::where('project_id', $projectId)
                ->where('team_id', $teamId)
                ->first();
            $result = $teamProject->delete();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }
}