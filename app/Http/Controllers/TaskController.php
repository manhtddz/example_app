<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProjectCreateRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Services\Services\EmployeeService;
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
    private EmployeeService $employeeService;
    public function __construct(ProjectService $projectService, TaskService $taskService, EmployeeService $employeeService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
        $this->employeeService = $employeeService;
    }
    public function index(Request $request)
    {
        //initial value
        $sortBy = $request->input('sortBy');
        $direction = $request->input('direction', 'asc');
        $config = $this->config();
        $projects = $this->projectService->findAll();
        $tasks = $this->taskService
            ->search($request->all(), $sortBy, $direction)
            ->appends($request->query());

        return view(
            'dashboard.layout',
            compact(['config', 'projects', 'direction', 'tasks'])
        );
    }
    public function show(Request $request, $id)
    {
        $sortBy = $request->input('sortBy');
        $direction = $request->input('direction', 'asc');
        $task = $this->taskService->findById($id);
        $data = $this->taskService->searchDetailsWithTask(
            $id,
            $request->all(),
            $sortBy,
            $direction
        );
        $config = $this->config();

        $config['template'] = "dashboard.task.show";

        return view(
            'dashboard.layout',
            compact(['config', 'task', 'direction', 'id', 'data'])
        );
    }
    public function edit($id, Request $request)
    {
        try {
            $projects = $this->projectService->findAll();
            $task = $this->taskService->findById($id);
            $projectId = $request->input('projectId');
            $employeeId = $request->input('employeeId');
            $config = $this->config();

            $config['template'] = "dashboard.task.update";

            return view('dashboard.layout', compact(['config', 'task', 'projects', 'projectId', 'employeeId']));
        } catch (Exception $e) {
            \Log::info($e->getMessage(), [
                'action' => __METHOD__,
                'id' => $id
            ]);
            if ($request->input('projectId')) {
                return redirect()->route('project.show', $projectId)->with(SESSION_ERROR, $e->getMessage());
            }
            if ($request->input('employeeId')) {
                return redirect()->route('employee.show', $employeeId)->with(SESSION_ERROR, $e->getMessage());
            }
            return redirect()->route('task.index')->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function getCreateForm(Request $request)
    {
        $projectId = $request->input('projectId');
        $projects = $this->projectService->findAll();
        $config = $this->config();
        $config['template'] = "dashboard.task.create";

        return view('dashboard.layout', compact(['config', 'projects', 'projectId']));
    }

    public function updateConfirm($id, TaskUpdateRequest $request)
    {
        $projectId = $request->input('projectId');
        $employeeId = $request->input('employeeId');
        $this->taskService->prepareConfirmForUpdate($request);

        $config = $this->config();
        $config['template'] = "dashboard.task.update_confirm";

        return view('dashboard.layout', compact(['config', 'id', 'projectId', 'employeeId']));
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
        $projectId = $request->input('projectId');
        $this->taskService->prepareConfirmForCreate($request);

        $config = $this->config();
        $config['template'] = "dashboard.task.create_confirm";

        return view('dashboard.layout', compact(['config', 'projectId']));
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
            $projectId = $request->input('projectId');
            $employeeId = $request->input('employeeId');
            $this->taskService->update($id, $request->all());
            if ($request->input('projectId')) {
                return redirect()->route('project.show', $projectId)->with(SESSION_SUCCESS, UPDATE_SUCCESS);
            }
            if ($request->input('employeeId')) {
                return redirect()->route('employee.show', $employeeId)->with(SESSION_SUCCESS, UPDATE_SUCCESS);
            }
            return redirect()->route('task.index', $projectId)->with(SESSION_SUCCESS, UPDATE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => array_merge(['id' => $id], $request->all())
                ]
            );
            if ($request->input('projectId')) {
                return redirect()->route('project.show', $projectId)->with(SESSION_ERROR, $e->getMessage());
            }
            if ($request->input('employeeId')) {
                return redirect()->route('employee.show', $employeeId)->with(SESSION_ERROR, $e->getMessage());
            }
            return redirect()->route('task.index', $projectId)->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {
            $projectId = $request->input('projectId');
            $this->taskService->create($request->all());
            if (!$request->input('projectId')) {
                return redirect()->route('task.index')->with(SESSION_SUCCESS, CREATE_SUCCESS);
            }
            return redirect()->route('project.show', $projectId)->with(SESSION_SUCCESS, CREATE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'data' => request()->all()
                ]
            );
            if (!$request->input('projectId')) {
                return redirect()->route('task.index')->with(SESSION_ERROR, $e->getMessage());
            }
            return redirect()->route('project.show', $projectId)->with(SESSION_ERROR, $e->getMessage());
        }
    }
    public function delete($id, Request $request)
    {
        try {
            $projectId = $request->input('projectId');
            $employeeId = $request->input('employeeId');

            $this->taskService->delete($id);
            if ($request->input('projectId')) {
                return redirect()->route('project.show', $projectId)->with(SESSION_SUCCESS, DELETE_SUCCESS);
            }
            if ($request->input('employeeId')) {
                return redirect()->route('employee.show', $employeeId)->with(SESSION_SUCCESS, DELETE_SUCCESS);
            }
            return redirect()->route('task.index', $projectId)->with(SESSION_SUCCESS, DELETE_SUCCESS);
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $id
                ]
            );
            if ($request->input('projectId')) {
                return redirect()->route('project.show', $projectId)->with(SESSION_ERROR, $e->getMessage());
            }
            if ($request->input('employeeId')) {
                return redirect()->route('employee.show', $employeeId)->with(SESSION_ERROR, $e->getMessage());
            }
            return redirect()->route('task.index', $projectId)->with(SESSION_ERROR, $e->getMessage());
        }
    }

    public function getAddEmployees(Request $request, $id)
    {
        try {
            $sortBy = $request->input('sortBy');
            $direction = $request->input('direction', 'asc');
            $task = $this->taskService->findById($id);

            $employees = $this->employeeService
                ->search($request->all(), $sortBy, $direction);
            $selectedEmployees = $this->employeeService
                ->findAllWithTask($task->id)->toArray();
            $config = $this->config();

            $config['template'] = "dashboard.task.add_employees";

            return view(
                'dashboard.layout',
                compact(['config', 'task', 'direction', 'id', 'employees', 'selectedEmployees'])
            );
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

    public function addEmployees(Request $request, $taskId)
    {
        try {
            $task = $this->taskService->findById($taskId);

            $data = $this->taskService->getSelectData($request);
            $this->taskService->addEmployeesToProject($data['newData'], $taskId);
            $this->taskService->removeEmployeesFromProject($data['unsetData'], $taskId);

            return back()->with(SESSION_SUCCESS, 'Employees added successfully!');
        } catch (Exception $e) {
            \Log::info(
                $e->getMessage(),
                [
                    'action' => __METHOD__,
                    'id' => $taskId
                ]
            );

            if (in_array($e->getMessage(), [NOT_EXIST_ERROR, WRONG_FORMAT_ID])) {
                return redirect()->route('task.index')->with(SESSION_ERROR, $e->getMessage());
            }

            return back()->with(SESSION_ERROR, $e->getMessage());
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