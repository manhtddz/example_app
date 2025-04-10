<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamCreateRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Services\Services\ProjectService;
use App\Services\Services\TeamService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class TeamController extends Controller
{
    private TeamService $teamService;
    private ProjectService $projectService;
    public function __construct(TeamService $teamService, ProjectService $projectService)
    {
        $this->teamService = $teamService;
        $this->projectService = $projectService;
    }
    public function index(Request $request)
    {
        //initial value
        $sortBy = $request->input('sortBy');
        $direction = $request->input('direction', 'asc');
        $config = $this->config();

        $teams = $this->teamService
            ->search($request->all(), $sortBy, $direction)
            ->appends($request->query());

        return view(
            'dashboard.layout',
            compact(['config', 'teams', 'direction'])
        );
    }
    public function show(Request $request, $id)
    {
        $sortBy = $request->input('sortBy');
        $direction = $request->input('direction', 'asc');
        $team = $this->teamService->findById($id);
        $data = $this->teamService->searchDetailsWithTeam(
            $id,
            $request->input('tab'),
            $request->all(),
            $sortBy,
            $direction
        );
        $config = $this->config();

        $config['template'] = "dashboard.team.show";

        return view(
            'dashboard.layout',
            compact(['config', 'team', 'direction', 'id', 'data'])
        );
    }
    public function edit($id)
    {
        try {
            $team = $this->teamService->findById($id);
            $config = $this->config();

            $config['template'] = "dashboard.team.update";

            return view('dashboard.layout', compact(['config', 'team']));
        } catch (Exception $e) {
            Log::info($e->getMessage(), [
                'action' => __METHOD__,
                'id' => $id
            ]);
            return redirect()->route('team.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function getCreateForm()
    {
        $config = $this->config();
        $config['template'] = "dashboard.team.create";

        return view('dashboard.layout', compact(['config']));
    }

    public function updateConfirm($id, TeamUpdateRequest $request)
    {
        $this->teamService->prepareConfirmForUpdate($request);

        $config = $this->config();
        $config['template'] = "dashboard.team.update_confirm";

        return view('dashboard.layout', compact(['config', 'id']));
    }
    public function showUpdateConfirm()
    {
        // Check exists data
        if (!session()->has('team_data')) {
            return redirect()->route('team.index')->with(SESSION_ERROR, ERROR_ACCESS_DENIED);
        }

        $config = $this->config();
        $config['template'] = "dashboard.team.update_confirm";

        return view('dashboard.layout', compact(['config']));
    }
    public function createConfirm(TeamCreateRequest $request)
    {
        $this->teamService->prepareConfirmForCreate($request);

        $config = $this->config();
        $config['template'] = "dashboard.team.create_confirm";

        return view('dashboard.layout', compact(['config']));
    }
    public function showCreateConfirm()
    {
        // Check exists data
        if (!session()->has('team_data')) {
            return redirect()->route('team.create')->with(SESSION_ERROR, ERROR_ACCESS_DENIED);
        }

        $config = $this->config();
        $config['template'] = "dashboard.team.create_confirm";
        return view('dashboard.layout', compact(['config']));
    }

    public function update(Request $request, $id)
    {
        try {
            $this->teamService->update($id, $request->all());
            return redirect()->route('team.index')->with(SESSION_SUCCESS, UPDATE_SUCCESS);
        } catch (Exception $e) {
            Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => array_merge(['id' => $id], $request->all())
                ]
            );
            return redirect()->route('team.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {
            $this->teamService->create($request->all());
            return redirect()->route('team.index')->with(SESSION_SUCCESS, CREATE_SUCCESS);
        } catch (Exception $e) {
            Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => request()->all()
                ]
            );
            return redirect()->route('team.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->teamService->delete($id);
            return redirect()->route('team.index')->with(SESSION_SUCCESS, DELETE_SUCCESS);
        } catch (Exception $e) {
            Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $id
                ]
            );
            return redirect()->route('team.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function getAddProjects(Request $request, $id)
    {
        try {
            $sortBy = $request->input('sortBy');
            $direction = $request->input('direction', 'asc');
            $team = $this->teamService->findById($id);

            $projects = $this->projectService
                ->search($request->all(), $sortBy, $direction);
            $selectedProjects = $this->projectService
                ->findAllWithTeam($team->id)->toArray();
            $config = $this->config();

            $config['template'] = "dashboard.team.add_projects";

            return view(
                'dashboard.layout',
                compact(['config', 'team', 'direction', 'id', 'projects', 'selectedProjects'])
            );
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $id
                ]
            );
            return redirect()->route('team.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function addProjects(Request $request, $teamId)
    {
        try {
            // dd($request);
            $task = $this->teamService->findById($teamId);
            $data = $this->teamService->getSelectData($request);

            $this->teamService->addProjectsToTeam($data['newData'], $teamId);
            $this->teamService->removeProjectsFromTeam($data['unsetData'], $teamId);

            return back()->with(SESSION_SUCCESS, 'Employees added successfully!');
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $teamId
                ]
            );

            if (in_array($e->getMessage(), [NOT_EXIST_ERROR, WRONG_FORMAT_ID])) {
                return redirect()->route('team.index')->with(SESSION_ERROR, $e->getMessage());
            }

            return back()->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function config()
    {
        return [
            'user' => Auth::user(),
            'template' => "dashboard.team.index",
        ];
    }
}