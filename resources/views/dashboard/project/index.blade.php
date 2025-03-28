<div class="container mt-5">
    <h2 class="mb-4">Project - Search</h2>
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
    <!-- Search Form -->
    <form action="{{ route('project.index') }}" method="GET" class="mb-3">
        <div class="col-md-4">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                value="{{ request()->query('name') }}">
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
        <div class="d-flex justify-content-between mt-3 w-100">
            <button type="submit" class="btn btn-primary me-2">Search</button>

            <a href="{{ route('project.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
    <!-- Search result -->
    <h3>Search result:</h3>
    @if($projects->isNotEmpty())

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th><a href="{{ request()->fullUrlWithQuery(['sortBy' => 'id', 'direction' => $direction === "asc" ? "desc" : "asc"]) }}"
                            class="text-white">ID ↕</a></th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sortBy' => 'team_id', 'direction' => $direction === "asc" ? "desc" : "asc"]) }}"
                            class="text-white">Team ↕</a></th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sortBy' => 'name', 'direction' => $direction === "asc" ? "desc" : "asc"]) }}"
                            class="text-white">Name ↕</a></th>
                    <th>Action</th>
                </tr>
            </thead>
            @foreach($projects as $project)
                <tr>
                    <td>{{ $project->id }}</td>
                    <td>
                        {{ $project->team->name }}
                    </td>
                    <td>
                        {{ $project->name }}
                    </td>
                    <td>
                        <a href="{{ route('project.edit', $project->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('project.show', $project->id) }}" class="btn btn-primary btn-sm">Details</a>
                        <form method="POST" action="{{ route('project.delete', $project->id) }}" style="display:inline-block;">
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

        @if ($projects->hasPages())

            <ul class="pagination">
                {{-- First --}}
                @if ($projects->currentPage() > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $projects->url(1) }}">First</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <a class="page-link">First</a>
                    </li>
                @endif

                {{-- Prev --}}
                @if($projects->onFirstPage())
                    <li class="page-item disabled">
                        <a class="page-link">Prev</a>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $projects->previousPageUrl() }}">Prev</a>
                    </li>
                @endif

                {{-- Index page --}}
                @for ($i = 1; $i <= $projects->lastPage(); $i++)
                    @if ($i == $projects->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $projects->url($i) }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Next --}}
                @if ($projects->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $projects->nextPageUrl() }}">Next</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <a class="page-link">Next</a>
                    </li>
                @endif

                {{-- Last --}}
                @if ($projects->currentPage() < $projects->lastPage())
                    <li class="page-item">
                        <a class="page-link" href="{{ $projects->url($projects->lastPage()) }}">Last</a>
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