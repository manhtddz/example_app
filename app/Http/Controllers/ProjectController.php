<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProjectCreateRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Employee;
use App\Models\EmployeeProject;
use App\Models\Project;
use App\Services\Services\EmployeeService;
use App\Services\Services\ProjectService;
use App\Services\Services\TaskService;
use App\Services\Services\TeamService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ProjectController extends Controller
{
    private ProjectService $projectService;
    private TeamService $teamService;
    private TaskService $taskService;
    private EmployeeService $employeeService;
    public function __construct(ProjectService $projectService, TeamService $teamService, TaskService $taskService, EmployeeService $employeeService)
    {
        $this->projectService = $projectService;
        $this->teamService = $teamService;
        $this->taskService = $taskService;
        $this->employeeService = $employeeService;
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
        // dd($request->input('tab'));
        try {
            $sortBy = $request->input('sortBy');
            $direction = $request->input('direction', 'asc');
            $project = $this->projectService->findById($id);
            $data = $this->projectService->searchDetailsWithProject(
                $id,
                $request->input('tab'),
                $request->all(),
                $sortBy,
                $direction
            );
            $config = $this->config();

            $config['template'] = "dashboard.project.show";

            return view(
                'dashboard.layout',
                compact(['config', 'project', 'direction', 'id', 'data'])
            );
        } catch (Exception $e) {
            Log::info($e->getMessage(), [
                'action' => __METHOD__,
                'id' => $id
            ]);
            return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
        }
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
            Log::info($e->getMessage(), [
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
    // public function getAddTaskForm(Request $request)
    // {
    //     $projects = $this->projectService->findAllWithTeamName();
    //     $projectId = $request->input('project_id');
    //     $config = $this->config();
    //     $config['template'] = "dashboard.task.create";

    //     return view('dashboard.layout', compact(['config', 'projects', 'projectId']));
    // }

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
            Log::info(
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
            Log::info(
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
            Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $id
                ]
            );
            return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function getAddEmployees(Request $request, $id)
    {
        try {
            $sortBy = $request->input('sortBy');
            $direction = $request->input('direction', 'asc');
            $project = $this->projectService->findById($id);
            $employees = $this->employeeService
                ->searchWithTeamPaging($project->team->id, $request->all(), $sortBy, $direction);
            $selectedEmployees = $this->employeeService
                ->findAllWithProject($project->id)->toArray();
            $config = $this->config();

            $config['template'] = "dashboard.project.add_employees";

            return view(
                'dashboard.layout',
                compact(['config', 'project', 'direction', 'id', 'employees', 'selectedEmployees'])
            );
        } catch (Exception $e) {
            Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $id
                ]
            );
            return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function addEmployees(Request $request, $projectId)
    {
        try {
            $project = $this->projectService->findById($projectId);

            $data = $this->projectService->getSelectData($request);
            $this->projectService->addEmployeesToProject($data['newData'], $projectId);
            $this->projectService->removeEmployeesFromProject($data['unsetData'], $projectId);

            return back()->with(SESSION_SUCCESS, 'Employees added successfully!');
        } catch (Exception $e) {
            Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $projectId
                ]
            );

            if (in_array($e->getMessage(), [NOT_EXIST_ERROR, WRONG_FORMAT_ID])) {
                return redirect()->route('project.index')->with(SESSION_ERROR, $e->getMessage());
            }

            return back()->with(SESSION_ERROR, $e->getMessage());
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