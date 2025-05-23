<?php
use App\Const\Gender;
use App\Const\Position;
use App\Const\Status;
use App\Const\TypeOfWork;
use App\Models\Team;
?>

<div class="container mt-4">
    <div class="card p-4">
        <h4 class="mb-3">Employee - Create confirm</h4>

        <form action="{{ route('employee.create') . ($teamId ? '?teamId=' . $teamId : '')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label"><strong>Avatar:</strong></label>
                @php
                    $avatarPath = session('employee_data.avatar');
                @endphp

                @if ($avatarPath)
                    <img id="previewImage" src="{{ url(TEMP_URL . $avatarPath) }}"
                        style="max-width: 200px; margin-top: 10px;">
                    <input type="hidden" name="avatar" value="{{ session('employee_data.avatar') }}">
                @else
                    <p style="color: red;">{{ NO_AVATAR }}</p>
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Team:</strong></label>
                <p class="border p-2 bg-light">{{ Team::getFieldById(session('employee_data.team_id'), 'name')  }}</p>

                <input type="hidden" name="team_id" value="{{ session('employee_data.team_id') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>First name:</strong></label>
                <p class="border p-2 bg-light">{{ session('employee_data.first_name') }}</p>

                <input type="hidden" name="first_name" value="{{ session('employee_data.first_name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Last name:</strong></label>
                <p class="border p-2 bg-light">{{ session('employee_data.last_name') }}</p>

                <input type="hidden" name="last_name" value="{{ session('employee_data.last_name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Email:</strong></label>
                <p class="border p-2 bg-light">{{ session('employee_data.email') }}</p>

                <input type="hidden" name="email" value="{{ session('employee_data.email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Gender:</strong></label>
                <p class="border p-2 bg-light">{{ Gender::getName(session('employee_data.gender'))}}</p>

                <input type="hidden" name="gender" value="{{ session('employee_data.gender') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Birthday:</strong></label>
                <p class="border p-2 bg-light">{{ session('employee_data.birthday')}}</p>

                <input type="hidden" name="birthday" value="{{ session('employee_data.birthday') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Address:</strong></label>
                <p class="border p-2 bg-light">{{ session('employee_data.address') }}</p>

                <input type="hidden" name="address" value="{{ session('employee_data.address') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Salary:</strong></label>
                <p class="border p-2 bg-light">{{ session('employee_data.salary') }}</p>

                <input type="hidden" name="salary" value="{{ session('employee_data.salary') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Position:</strong></label>
                <p class="border p-2 bg-light">{{ Position::getName(session('employee_data.position'))}}</p>

                <input type="hidden" name="position" value="{{ session('employee_data.position') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Type Of Work:</strong></label>
                <p class="border p-2 bg-light">{{ TypeOfWork::getName(session('employee_data.type_of_work'))}}</p>

                <input type="hidden" name="type_of_work" value="{{ session('employee_data.type_of_work') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Status:</strong></label>
                <p class="border p-2 bg-light">{{ Status::getName(session('employee_data.status'))}}</p>

                <input type="hidden" name="status" value="{{ session('employee_data.status') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Password:</strong></label>
                <p class="border p-2 bg-light">{{ session('employee_data.password') }}</p>

                <input type="hidden" name="password" value="{{ session('employee_data.password') }}">
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                    Save
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
            </div>
            <!-- confirm modal -->
            @include('dashboard.component.confirm-modal')

        </form>
    </div>
</div>