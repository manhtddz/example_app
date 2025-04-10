<div class="container mt-5">
    <!-- Session message -->
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
    <h2>Add Projects to Team</h2>
    <p><strong>ID:</strong> {{ $team->id }}</p>
    <p><strong>Team Name:</strong> {{ $team->name }}</p>

    <!-- Search Form -->
    <form method="GET" action="{{ route('team.addProjects', $id) }}">
        <div class="card p-3 mb-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Project Name:</label>
                <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Project Name"
                    value="{{ request()->query('name') }}">
            </div>
            <div class="d-flex justify-content-between mt-2">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('team.addProjects', $id) }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <!-- Project List -->

    <form method="POST" action="{{ route('team.addProjects', $team->id) }}">
        @csrf
        <div id="selected-projects-container">
            <!-- Selected projects from sessionStorage will be added here -->
        </div>
        @foreach ($selectedProjects as $projectId)
            <input type="hidden" name="selectedProjects[]" value="{{ $projectId }}">
        @endforeach
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td><input class="project-ids" id="project-id-{{ $project->id }}"
                                onclick="addToTeam({{ $project->id }}, {{ $team->id }})" type="checkbox" name="projects[]"
                                value="{{ $project->id }}"></td>
                        <td>{{ $project->id }}</td>
                        <td>{{ $project->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        @if ($projects->hasPages())
            <nav>
                <ul class="pagination pagination-sm">
                    @if ($projects->currentPage() > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">First</a>
                        </li>
                    @endif

                    @if($projects->onFirstPage())
                        <li class="page-item disabled"><a class="page-link">Prev</a></li>
                    @else
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ request()->fullUrlWithQuery(['page' => $projects->currentPage() - 1]) }}">Prev</a>
                        </li>
                    @endif

                    @for ($i = 1; $i <= $projects->lastPage(); $i++)
                        <li class="page-item {{ $i == $projects->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($projects->hasMorePages())
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ request()->fullUrlWithQuery(['page' => $projects->currentPage() + 1]) }}">Next</a>
                        </li>
                    @else
                        <li class="page-item disabled"><a class="page-link">Next</a></li>
                    @endif

                    @if ($projects->currentPage() < $projects->lastPage())
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ request()->fullUrlWithQuery(['page' => $projects->lastPage()]) }}">Last</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
        <div class="d-flex justify-content-between mt-2">
            <button type="submit" class="btn btn-success">Add Selected</button>
            <button type="button" onclick="clearSessionStorage({{ $team->id }})" class="btn btn-secondary btn-sm">Clear
                changes</button>
        </div>
    </form>
</div>

<script>
    function clearSessionStorage(teamId) {
        let itemName = 'Team-id-' + teamId;
        sessionStorage.removeItem(itemName);

        document.querySelectorAll('.project-ids').forEach(checkbox => {
            checkbox.checked = false;
        });

        let selected = @json($selectedProjects);
        sessionStorage.setItem(itemName, JSON.stringify(selected));

        precheckProjectBelongTeam(teamId);
    }

    function addToTeam(projectId, teamId) {
        let itemName = 'Team-id-' + teamId;
        let selectedProjects = sessionStorage.getItem(itemName);
        if (!selectedProjects) {
            selectedProjects = [];
        } else {
            selectedProjects = JSON.parse(selectedProjects);
        }

        let isAddProject = document.getElementById('project-id-' + projectId).checked;

        if (isAddProject) {
            selectedProjects.push(projectId);
        } else {
            selectedProjects = selectedProjects.filter(item => item !== projectId);
        }

        sessionStorage.setItem(itemName, JSON.stringify(selectedProjects));

        updateSelectedProjectsInForm(selectedProjects);
    }

    function updateSelectedProjectsInForm(selectedProjects) {
        let container = document.getElementById('selected-projects-container');
        container.innerHTML = '';

        selectedProjects.forEach(function (projectId) {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selectProjects[]';
            input.value = projectId;
            container.appendChild(input);
        });
    }

    function precheckProjectBelongTeam(teamId) {
        let itemName = 'Team-id-' + teamId;
        let checkboxes = document.querySelectorAll('.project-ids');
        let selected = @json($selectedProjects);

        let selectedProjects = sessionStorage.getItem(itemName);
        if (!selectedProjects) {
            selectedProjects = [];
            selected.forEach(item => {
                selectedProjects.push(item);
            });
            sessionStorage.setItem(itemName, JSON.stringify(selectedProjects));
        } else {
            selectedProjects = JSON.parse(selectedProjects);
        }

        checkboxes.forEach(function (checkbox) {
            if (selectedProjects.includes(parseInt(checkbox.value))) {
                checkbox.checked = true;
            }
        });
        updateSelectedProjectsInForm(selectedProjects);
    }

    precheckProjectBelongTeam({{ $team->id }});
</script>