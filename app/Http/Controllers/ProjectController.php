<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProjectCreateRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Services\Services\ProjectService;
use App\Services\Services\TaskService;
use App\Services\Services\TeamService;
use Auth;
use Exception;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private ProjectService $projectService;
    private TeamService $teamService;
    private TaskService $taskService;
    public function __construct(ProjectService $projectService, TeamService $teamService, TaskService $taskService)
    {
        $this->projectService = $projectService;
        $this->teamService = $teamService;
        $this->taskService = $taskService;
    }
    public function index(Request $request)
    {
        //initial value
        $teams = $this->teamService->findAll();
        $sortBy = $request->input('sortBy');
        $direction = $request->input('direction', 'asc');
        $config = $this->config();

        $projects = $this->projectService
            ->search($request->all(), $sortBy, $direction)
            ->appends($request->query());

        return view(
            'dashboard.layout',
            compact(['config', 'projects', 'direction', 'teams'])
        );
    }
    public function show(Request $request, $id)
    {
        $sortBy = $request->input('sortBy');
        $direction = $request->input('direction', 'asc');
        $project = $this->projectService->findById($id);
        $tasks = $this->taskService->search(
            $id,
            $request->all(),
            $sortBy,
            $direction
        );
        $config = $this->config();

        $config['template'] = "dashboard.project.show";

        return view(
            'dashboard.layout',
            compact(['config', 'project', 'direction', 'tasks', 'id'])
        );
    }
    public function edit($id)
    {
        try {
            $teams = $this->teamService->findAll();
            $project = $this->projectService->findById($id);
            $config = $this->config();

            $config['template'] = "dashboard.project.update";

            return view('dashboard.layout', compact(['config', 'project', 'teams']));
        } catch (Exception $e) {
            \Log::info($e->getMessage(), [
                'action' => __METHOD__,
                'id' => $id
            ]);
            return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function getCreateForm()
    {
        $teams = $this->teamService->findAll();
        $config = $this->config();
        $config['template'] = "dashboard.project.create";

        return view('dashboard.layout', compact(['config', 'teams']));
    }

    public function updateConfirm($id, ProjectUpdateRequest $request)
    {
        $this->projectService->prepareConfirmForUpdate($request);

        $config = $this->config();
        $config['template'] = "dashboard.project.update_confirm";

        return view('dashboard.layout', compact(['config', 'id']));
    }
    public function showUpdateConfirm()
    {
        // Check exists data
        if (!session()->has('project_data')) {
            return redirect()->route('project.index')->with(SESSION_ERROR, ERROR_ACCESS_DENIED);
        }

        $config = $this->config();
        $config['template'] = "dashboard.project.update_confirm";

        return view('dashboard.layout', compact(['config']));
    }
    public function createConfirm(ProjectCreateRequest $request)
    {
        $this->projectService->prepareConfirmForCreate($request);

        $config = $this->config();
        $config['template'] = "dashboard.project.create_confirm";

        return view('dashboard.layout', compact(['config']));
    }
    public function showCreateConfirm()
    {
        // Check exists data
        if (!session()->has('project_data')) {
            return redirect()->route('project.create')->with(SESSION_ERROR, ERROR_ACCESS_DENIED);
        }

        $config = $this->config();
        $config['template'] = "dashboard.project.create_confirm";
        return view('dashboard.layout', compact(['config']));
    }

    public function update(Request $request, $id)
    {
        try {
            $this->projectService->update($id, $request->all());
            return redirect()->route('project.index')->with(SESSION_SUCCESS, UPDATE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => array_merge(['id' => $id], $request->all())
                ]
            );
            return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {
            $this->projectService->create($request->all());
            return redirect()->route('project.index')->with(SESSION_SUCCESS, CREATE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => request()->all()
                ]
            );
            return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->projectService->delete($id);
            return redirect()->route('project.index')->with(SESSION_SUCCESS, DELETE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $id
                ]
            );
            return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function config()
    {
        return [
            'user' => Auth::user(),
            'template' => "dashboard.project.index",
        ];
    }
}