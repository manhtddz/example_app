<?php
use App\Const\TaskStatus;
// dd($data);
?>
<div class="container mt-5">
    <h2 class="mb-4">Team - Details</h2>

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
    <p><strong>ID:</strong> {{ $team->id }}</p>
    <p><strong>Team Name:</strong> {{ $team->name }}</p>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mt-3" id="teamTabs">
        <li class="nav-item">
            <a class="nav-link {{ request()->query('tab', 'projects') == 'projects' ? 'active' : '' }}"
                href="{{ route('team.show', ['id' => $id, 'tab' => 'projects']) }}">
                Projects
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->query('tab') == 'employees' ? 'active' : '' }}"
                href="{{ route('team.show', ['id' => $id, 'tab' => 'employees']) }}">
                Employees
            </a>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content mt-3">
        <!-- Projects Tab -->
        <div class="tab-pane fade {{ request()->query('tab', 'projects') == 'projects' ? 'show active' : '' }}"
            id="projects">
            <div class="card p-3 mb-3">
                <form action="{{ route('team.show', $id) }}" method="GET">
                    <input type="hidden" name="tab" value="projects"> <!-- Giữ tab khi submit -->
                    <div class="mb-2">
                        <label for="name" class="form-label">Project Name:</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name"
                            placeholder="Project Name" value="{{ request()->query('name') }}">
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <a href="{{ route('team.show', ['id' => $id, 'tab' => 'projects']) }}"
                            class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>
                                    <a href="{{ route('project.edit', $project->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('project.delete', $project->id) }}"
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
            <div class="card p-3 mb-3">
                <form action="{{ route('team.show', $id) }}" method="GET">
                    <input type="hidden" name="tab" value="employees"> <!-- Giữ tab khi submit -->
                    <div class="mb-2">
                        <label for="name" class="form-label">Employee Name:</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name"
                            placeholder="Employee Name" value="{{ request()->query('name') }}">
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email:</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                            value="{{ request()->query('email') }}">
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <a href="{{ route('team.show', ['id' => $id, 'tab' => 'employees']) }}"
                            class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
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
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'name', 'direction' => ($sortBy === 'name' && $direction === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-white">
                                    Name {!! $sortBy === 'name' ? ($direction === 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sortBy' => 'email', 'direction' => ($sortBy === 'email' && $direction === 'asc') ? 'desc' : 'asc']) }}"
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
                                <img src="{{ url(APP_URL . $employee->avatar) }}" width="50" height="50" class="rounded-circle"
                                    title="{{ $employee->avatar ?? NO_AVATAR }}">
                            </td>
                            <td>
                                {{ $employee->name }}
                            </td>
                            <td>
                                {{ $employee->email }}
                            </td>
                            <td>
                                <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" action="{{ route('employee.delete', $employee->id) }}"
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