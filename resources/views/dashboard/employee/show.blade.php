<?php
use App\Const\TaskStatus;
?>
<div class="container mt-5">
    <h2 class="mb-4">Employee - Details</h2>

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
    <!-- Information -->
    <p><strong>ID:</strong> {{ $employee->id }}</p>
    <p><strong>Name:</strong> {{ $employee->name }}</p>
    <p><strong>Email:</strong> {{ $employee->email }}</p>
    <p><strong>Team:</strong> {{ $employee->team->name }}</p>

    <!-- Tabs Content -->
    <div class="tab-content mt-3">
        <!-- Tasks Tab -->
        <div class="card p-3 mb-3">
            <!-- Search Form -->
            <form action="{{ route('employee.show', $id) }}" method="GET">
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
                    <a href="{{ route('employee.show', $id) }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
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
                            <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'project_id', 'direction' => ($sortBy === 'project_id' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                class="text-white">
                                Project {!! $sortBy === 'project_id' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
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
                            <td>{{ $task->project->name }}</td>
                            <td>{{ $task->name }}</td>
                            <td>{{ TaskStatus::getName($task->task_status) }}</td>
                            <td>
                                <a href="{{ route('task.edit', $task->id) . '?employeeId=' . $employee->id }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <a href="{{ route('task.show', $task->id) }}" class="btn btn-primary btn-sm">Details</a>
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
        <!-- Pagination -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>