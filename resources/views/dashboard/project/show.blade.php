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

    <p><strong>ID:</strong> {{ $project->id }}</p>
    <p><strong>Name:</strong> {{ $project->name }}</p>

    <!-- Search Form -->
    <form action="{{ route('project.show', $id) }}" method="GET" class="mb-3">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                value="{{ request()->query('name') }}">
        </div>

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
            <a href="{{ route('project.show', $id) }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- <a class="btn btn-primary" href="{{ route('task.create', $id) }}">Create</a> -->
    <!-- Search Result -->
    <h3>Search Result:</h3>
    @if($tasks->isNotEmpty())
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th><a href="{{ request()->fullUrlWithQuery(['sortBy' => 'id', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                            class="text-white">ID ↕</a></th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sortBy' => 'name', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                            class="text-white">Name ↕</a></th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sortBy' => 'task_status', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                            class="text-white">Status ↕</a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->name }}</td>
                        <td>{{ TaskStatus::getName($task->task_status) }}</td>
                        <td>
                            <!-- Action Buttons (Commented out for now) -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
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
                    <th>Name</th>
                    <th>Task Status</th>
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