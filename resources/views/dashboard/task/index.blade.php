<?php
use App\Const\TaskStatus;
?>
<div class="container mt-5">
    <h2 class="mb-4">Task - Search</h2>
    @if (session(SESSION_ERROR))
        <div class="alert alert-danger">
            {{ session(SESSION_ERROR) }}
        </div>
    @endif
    @if (session(SESSION_SUCCESS))
        <div class="alert alert-primary">
            {{ session(SESSION_SUCCESS) }}
        </div>
    @endif
    @php
        $sortBy = request()->query('sortBy', 'id'); // Default sort by id
        $direction = request()->query('direction', 'asc'); //asc default 
    @endphp
    <!-- Search Form -->
    <form action="{{ route('task.index') }}" method="GET" class="mb-3">
        <div class="col-md-4">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                value="{{ request()->query('name') }}">
        </div>
        <label class="form-label" for="project">Project:</label><br>
        <select class="form-control w-25" id="project" name="project_id">
            <option value="0" {{ request()->query('project_id') == 0 ? 'selected' : '' }}>{{ '' }}</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" {{ request()->query('project_id') == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        <div class="mb-3">
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
        <div class="d-flex justify-content-between mt-3 w-100">
            <button type="submit" class="btn btn-primary me-2">Search</button>

            <a href="{{ route('task.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
    <!-- Search result -->
    <h3>Search result:</h3>
    @if($tasks->isNotEmpty())

        <table class="table table-bordered">
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
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>
                        {{ $task->project->name }}
                    </td>
                    <td>
                        {{ $task->name }}
                    </td>
                    <td>
                        {{ TaskStatus::getName($task->task_status) }}
                    </td>
                    <td>
                        <a href="{{ route('task.edit', $task->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('task.show', $task->id) }}" class="btn btn-primary btn-sm">Details</a>
                        <form method="POST" action="{{ route('task.delete', $task->id) }}" style="display:inline-block;">
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
        </table>

        @if ($tasks->hasPages())

            <ul class="pagination">
                {{-- First --}}
                @if ($tasks->currentPage() > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $tasks->url(1) }}">First</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <a class="page-link">First</a>
                    </li>
                @endif

                {{-- Prev --}}
                @if($tasks->onFirstPage())
                    <li class="page-item disabled">
                        <a class="page-link">Prev</a>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $tasks->previousPageUrl() }}">Prev</a>
                    </li>
                @endif

                {{-- Index page --}}
                @for ($i = 1; $i <= $tasks->lastPage(); $i++)
                    @if ($i == $tasks->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $tasks->url($i) }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Next --}}
                @if ($tasks->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $tasks->nextPageUrl() }}">Next</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <a class="page-link">Next</a>
                    </li>
                @endif

                {{-- Last --}}
                @if ($tasks->currentPage() < $tasks->lastPage())
                    <li class="page-item">
                        <a class="page-link" href="{{ $tasks->url($tasks->lastPage()) }}">Last</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <a class="page-link">Last</a>
                    </li>
                @endif
            </ul>
        @endif
    @else
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Team</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tr>
                <td colspan="3">{{ NO_RESULT }}</td>
            </tr>
        </table>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>