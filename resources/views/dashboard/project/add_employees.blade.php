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
    <h2>Add Employees to Project</h2>
    <p><strong>ID:</strong> {{ $project->id }}</p>
    <p><strong>Name:</strong> {{ $project->name }}</p>

    <!-- Search Form -->
    <form method="GET" action="{{ route('project.addEmployees', $id) }}">
        <div class="card p-3 mb-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Employee Name:</label>
                <input type="text" class="form-control form-control-sm" id="name" name="name"
                    placeholder="Employee Name" value="{{ request()->query('name') }}">
            </div>
            <div class="col-md-4">
                <label for="email" class="form-label">Email:</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                    value="{{ request()->query('email') }}">
            </div>
            <div class="d-flex justify-content-between mt-2">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('project.addEmployees', $id) }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <!-- Employee List -->

    <form method="POST" action="{{ route('project.addEmployees', $project->id) }}">
        @csrf
        <div id="selected-employees-container">
            <!-- Selected employees from sessionStorage will be added here -->
        </div>
        @foreach ($selectedEmployees as $employeeId)
            <input type="hidden" name="selectedEmployees[]" value="{{ $employeeId }}">
        @endforeach
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr>
                        <td><input class="employee-ids" id="employee-id-{{ $employee->id }}"
                                onclick="addToProject({{ $employee->id }}, {{  $project->id }})" type="checkbox"
                                name="employees[]" value="{{ $employee->id }}"></td>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($employees->hasPages())

            <nav>
                <ul class="pagination pagination-sm">
                    @if ($employees->currentPage() > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">First</a>
                        </li>
                    @endif

                    @if($employees->onFirstPage())
                        <li class="page-item disabled"><a class="page-link">Prev</a></li>
                    @else
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ request()->fullUrlWithQuery(['page' => $employees->currentPage() - 1]) }}">Prev</a>
                        </li>
                    @endif

                    @for ($i = 1; $i <= $employees->lastPage(); $i++)
                        <li class="page-item {{ $i == $employees->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($employees->hasMorePages())
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ request()->fullUrlWithQuery(['page' => $employees->currentPage() + 1]) }}">Next</a>
                        </li>
                    @else
                        <li class="page-item disabled"><a class="page-link">Next</a></li>
                    @endif

                    @if ($employees->currentPage() < $employees->lastPage())
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ request()->fullUrlWithQuery(['page' => $employees->lastPage()]) }}">Last</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
        <div class="d-flex justify-content-between mt-2">
            <button type="submit" class="btn btn-success">Add Selected</button>
            <button type="button" onclick="clearSessionStorage({{ $project->id }})"
                class="btn btn-secondary btn-sm">Clear changes</button>
        </div>
    </form>


</div>
<script>
    function clearSessionStorage(projectId) {
        let itemName = 'Project-id-' + projectId;
        sessionStorage.removeItem(itemName);

        // Lấy tất cả checkbox và bỏ chọn hết trước khi cập nhật
        document.querySelectorAll('.employee-ids').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Reset lại từ dữ liệu ban đầu của Laravel
        let selected = @json($selectedEmployees);
        sessionStorage.setItem(itemName, JSON.stringify(selected));

        // Gọi lại để cập nhật giao diện với dữ liệu ban đầu
        precheckEmployeeBelongProject(projectId);
    }

    function addToProject(employeeId, projectId) {
        let itemName = 'Project-id-' + projectId;
        let selectedEmployees = sessionStorage.getItem(itemName);
        if (!selectedEmployees) {
            selectedEmployees = [];  // Khởi tạo mảng nếu chưa có
        } else {
            selectedEmployees = JSON.parse(selectedEmployees);  // Nếu đã có dữ liệu trong sessionStorage, chuyển nó về kiểu mảng
        }

        let isAddEmployee = document.getElementById('employee-id-' + employeeId).checked;

        if (isAddEmployee) {
            selectedEmployees.push(employeeId);
        } else {
            selectedEmployees = selectedEmployees.filter(item => item !== employeeId);
        }

        sessionStorage.setItem(itemName, JSON.stringify(selectedEmployees));

        updateSelectedEmployeesInForm(selectedEmployees);
    }

    function updateSelectedEmployeesInForm(selectedEmployees) {
        let container = document.getElementById('selected-employees-container');
        container.innerHTML = '';

        console.log(selectedEmployees);
        selectedEmployees.forEach(function (employeeId) {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selectEmployees[]';
            input.value = employeeId;
            container.appendChild(input);
        });
    }

    function precheckEmployeeBelongProject(projectId) {
        let itemName = 'Project-id-' + projectId;

        // Lấy tất cả checkbox có class 'employee-ids'
        let checkboxes = document.querySelectorAll('.employee-ids');

        // Lấy dữ liệu từ sessionStorage
        let selectedEmployees = sessionStorage.getItem(itemName);
        console.log(selectedEmployees);
        let selected = @json($selectedEmployees);  // Laravel truyền biến PHP vào JavaScript

        // Nếu sessionStorage không có dữ liệu, thêm item từ Laravel vào và set lại sessionStorage
        if (!selectedEmployees) {
            selectedEmployees = [];  // Khởi tạo mảng nếu chưa có
            selected.forEach(item => {
                selectedEmployees.push(item);  // Thêm item vào mảng
            });
            sessionStorage.setItem(itemName, JSON.stringify(selectedEmployees));  // Lưu lại vào sessionStorage
        } else {
            selectedEmployees = JSON.parse(selectedEmployees);  // Nếu đã có dữ liệu trong sessionStorage, chuyển nó về kiểu mảng
        }

        // Kiểm tra và đánh dấu các checkbox tương ứng với các ID trong sessionStorage
        checkboxes.forEach(function (checkbox) {
            if (selectedEmployees.includes(parseInt(checkbox.value))) {
                checkbox.checked = true;
            }
        });
        updateSelectedEmployeesInForm(selectedEmployees);

    }

    precheckEmployeeBelongProject({{ $project->id }});
</script>