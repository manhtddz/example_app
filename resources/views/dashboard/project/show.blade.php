<?php
use App\Const\TaskStatus;
?>
<div class="container mt-5">
    <h2 class="mb-4">Project - Details</h2>

    <!-- Session Messages -->
    @if (session(SESSION_ERROR))
        <div class="alert alert-danger">{{ session(SESSION_ERROR) }}</div>
    @endif
    @if (session(SESSION_SUCCESS))
        <div class="alert alert-primary">{{ session(SESSION_SUCCESS) }}</div>
    @endif
    @php
        $sortBy = request()->query('sortBy', 'id'); // Default sort by id
        $direction = request()->query('direction', 'asc'); //asc default 
    @endphp
    <p><strong>ID:</strong> {{ $project->id }}</p>
    <p><strong>Project Name:</strong> {{ $project->name }}</p>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mt-3" id="projectTabs">
        <li class="nav-item">
            <a class="nav-link {{ request()->query('tab', 'tasks') == 'tasks' ? 'active' : '' }}"
                href="{{ route('project.show', ['id' => $id, 'tab' => 'tasks']) }}">
                Tasks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->query('tab') == 'employees' ? 'active' : '' }}"
                href="{{ route('project.show', ['id' => $id, 'tab' => 'employees']) }}">
                Employees
            </a>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content mt-3">
        <!-- Tasks Tab -->
        <div class="tab-pane fade {{ request()->query('tab', 'tasks') == 'tasks' ? 'show active' : '' }}" id="tasks">
            <div class="card p-3 mb-3">
                <!-- Search Form -->
                <form action="{{ route('project.show', $id) }}" method="GET">
                    <input type="hidden" name="tab" value="tasks"> <!--  Keep tab when submitting  -->
                    <div class="mb-2">
                        <label for="name" class="form-label">Task Name:</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name"
                            placeholder="Task Name" value="{{ request()->query('name') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Task Status:</label><br>
                        @php $statusOptions = TaskStatus::LIST; @endphp
                        @foreach ($statusOptions as $value => $label)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="task_status" id="status_{{ $value }}"
                                    value="{{ $value }}" {{ request()->query('task_status') == $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <a href="{{ route('project.show', ['id' => $id, 'tab' => 'tasks']) }}"
                            class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
            <!-- Create from details -->
            <a href="{{ route('task.create') . '?projectId=' . $project->id }}" class="btn btn-primary btn-sm">Create
                Task</a>
            <!-- Search Result -->
            @if($data->isNotEmpty())
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'id', 'direction' => ($sortBy === 'id' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    ID {!! $sortBy === 'id' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'name', 'direction' => ($sortBy === 'name' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    Name {!! $sortBy === 'name' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'task_status', 'direction' => ($sortBy === 'task_status' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    Status {!! $sortBy === 'task_status' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $task)
                            <tr>
                                <td>{{ $task->id }}</td>
                                <td>{{ $task->name }}</td>
                                <td>{{ TaskStatus::getName($task->task_status) }}</td>
                                <td>
                                    <a href="{{ route('task.edit', $task->id) . '?projectId=' . $project->id}}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <a href="{{ route('task.show', $task->id) }}" class="btn btn-primary btn-sm">Details</a>
                                    <form method="POST"
                                        action="{{ route('task.delete', $task->id) . '?projectId=' . $project->id }}"
                                        style="display:inline-block;">
                                        @csrf
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#confirmModal">
                                            Delete
                                        </button>
                                        @include('dashboard.component.confirm-modal')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tr>
                        <td colspan="3">{{ NO_RESULT }}</td>
                    </tr>
                </table>
            @endif
        </div>

        <!-- Employees Tab -->
        <div class="tab-pane fade {{ request()->query('tab') == 'employees' ? 'show active' : '' }}" id="employees">
            <!-- Search Form -->
            <div class="card p-3 mb-3">
                <form action="{{ route('project.show', $id) }}" method="GET">
                    <input type="hidden" name="tab" value="employees"> <!--  Keep tab when submitting  -->
                    <div class="mb-2">
                        <label for="name" class="form-label">Employee Name:</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name"
                            placeholder="Employee Name" value="{{ request()->query('name') }}">
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email:</label>
                        <input type="text" class="form-control form-control-sm" id="email" name="email"
                            placeholder="Email" value="{{ request()->query('email') }}">
                    </div>
                    <label class="form-label" for="team">Team:</label><br>
                    <select class="form-control w-25" id="team" name="team_id">
                        <option value="0" {{ request()->query('team_id') == 0 ? 'selected' : '' }}>{{ '' }}</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ request()->query('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <a href="{{ route('project.show', ['id' => $id, 'tab' => 'employees']) }}"
                            class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Add employee to project -->
            <a href="{{ route('project.addEmployees', $project->id) }}" class="btn btn-primary btn-sm">Add Employee</a>

            <!-- Search Result -->
            @if($data->isNotEmpty())
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'id', 'direction' => ($sortBy === 'id' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    ID {!! $sortBy === 'id' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>Avatar</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'team_id', 'direction' => ($sortBy === 'team_id' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    Team {!! $sortBy === 'team_id' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'name', 'direction' => ($sortBy === 'id' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    Name {!! $sortBy === 'name' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'email', 'direction' => ($sortBy === 'id' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    Email {!! $sortBy === 'email' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    @foreach($data as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>
                                @if (!$employee->avatar)
                                    <small class="text-muted">NO_AVATAR</small>
                                @else
                                    <img src="{{ url(APP_URL . $employee->avatar) }}" width="50" height="50" class="rounded-circle"
                                        title="{{ $employee->avatar }}">
                                @endif
                            </td>
                            <td>
                                {{ $employee->team->name ?? ''}}
                            </td>
                            <td>
                                {{ $employee->name }}
                            </td>
                            <td>
                                {{ $employee->email }}
                            </td>
                            <td>
                                <a href="{{ route('employee.edit', $employee->id) . '?projectId=' . $project->id }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <a href="{{ route('employee.show', $employee->id) }}" class="btn btn-primary btn-sm">Details</a>
                                <form method="POST"
                                    action="{{ route('employee.delete', $employee->id) . '?projectId=' . $project->id}}"
                                    style="display:inline-block;">
                                    @csrf
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#confirmModal_employee{{ $employee->id }}">
                                        Delete
                                    </button>
                                    @include('dashboard.component.confirm-modal', ['modalId' => 'confirmModal_employee' . $employee->id])
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tr>
                        <td colspan="5">{{ NO_RESULT }}</td>
                    </tr>
                </table>
            @endif
        </div>

        <div class="d-flex justify-content-between ">
            @if ($data->hasPages())

                <nav>
                    <ul class="pagination pagination-sm">
                        @if ($data->currentPage() > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">First</a>
                            </li>
                        @endif

                        @if($data->onFirstPage())
                            <li class="page-item disabled"><a class="page-link">Prev</a></li>
                        @else
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ request()->fullUrlWithQuery(['page' => $data->currentPage() - 1]) }}">Prev</a>
                            </li>
                        @endif

                        @for ($i = 1; $i <= $data->lastPage(); $i++)
                            <li class="page-item {{ $i == $data->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                            </li>
                        @endfor

                        @if ($data->hasMorePages())
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ request()->fullUrlWithQuery(['page' => $data->currentPage() + 1]) }}">Next</a>
                            </li>
                        @else
                            <li class="page-item disabled"><a class="page-link">Next</a></li>
                        @endif

                        @if ($data->currentPage() < $data->lastPage())
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ request()->fullUrlWithQuery(['page' => $data->lastPage()]) }}">Last</a>
                            </li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>