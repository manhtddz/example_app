<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProjectCreateRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Services\Services\ProjectService;
use App\Services\Services\TaskService;
use App\Services\Services\TeamService;
use Auth;
use Exception;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private ProjectService $projectService;
    private TaskService $taskService;
    public function __construct(ProjectService $projectService, TaskService $taskService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }
    public function index(Request $request)
    {
        //initial value
        $teams = $this->taskService->findAll();
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
    public function edit($id)
    {
        try {
            $projects = $this->projectService->findAllWithTeamName();
            $task = $this->projectService->findById($id);
            $config = $this->config();

            $config['template'] = "dashboard.task.update";

            return view('dashboard.layout', compact(['config', 'task', 'projects']));
        } catch (Exception $e) {
            \Log::info($e->getMessage(), [
                'action' => __METHOD__,
                'id' => $id
            ]);
            return redirect()->route('task.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function getCreateForm()
    {
        $projects = $this->projectService->findAllWithTeamName();
        $config = $this->config();
        $config['template'] = "dashboard.task.create";

        return view('dashboard.layout', compact(['config', 'projects']));
    }

    public function updateConfirm($id, TaskUpdateRequest $request)
    {
        $this->projectService->prepareConfirmForUpdate($request);

        $config = $this->config();
        $config['template'] = "dashboard.task.update_confirm";

        return view('dashboard.layout', compact(['config', 'id']));
    }
    public function showUpdateConfirm()
    {
        // Check exists data
        if (!session()->has('task_data')) {
            return redirect()->route('task.index')->with(SESSION_ERROR, ERROR_ACCESS_DENIED);
        }

        $config = $this->config();
        $config['template'] = "dashboard.task.update_confirm";

        return view('dashboard.layout', compact(['config']));
    }
    public function createConfirm(TaskCreateRequest $request)
    {
        $this->taskService->prepareConfirmForCreate($request);

        $config = $this->config();
        $config['template'] = "dashboard.task.create_confirm";

        return view('dashboard.layout', compact(['config']));
    }
    public function showCreateConfirm()
    {
        // Check exists data
        if (!session()->has('task_data')) {
            return redirect()->route('task.create')->with(SESSION_ERROR, ERROR_ACCESS_DENIED);
        }

        $config = $this->config();
        $config['template'] = "dashboard.task.create_confirm";
        return view('dashboard.layout', compact(['config']));
    }

    public function update(Request $request, $id)
    {
        try {
            $this->taskService->update($id, $request->all());
            return redirect()->route('task.index')->with(SESSION_SUCCESS, UPDATE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => array_merge(['id' => $id], $request->all())
                ]
            );
            return redirect()->route('task.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {
            $this->taskService->create($request->all());
            return redirect()->route('task.index')->with(SESSION_SUCCESS, CREATE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => request()->all()
                ]
            );
            return redirect()->route('task.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->taskService->delete($id);
            return redirect()->route('task.index')->with(SESSION_SUCCESS, DELETE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $id
                ]
            );
            return redirect()->route('task.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function config()
    {
        return [
            'user' => Auth::user(),
            'template' => "dashboard.task.index",
        ];
    }
}