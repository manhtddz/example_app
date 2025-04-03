<?php
use App\Const\TaskStatus;
?>
@if(session(SESSION_ERROR))
    <div class="alert alert-danger">
        {{ session(SESSION_ERROR) }}
    </div>
@endif
<div class="container mt-4">
    <h2 class="mb-3">Task - Create</h2>
    <form action="{{ route('task.createConfirm', $projectId ?? null) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="team">Team:</label><br>
            <select class="form-control" id="team" name="project_id">
                @php
                    $selectedProjectId = old('project_id', session('task_data.project_id', $projectId ?? ''));
                @endphp
                <option value="">{{ '' }}
                </option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected ' : '' }}>
                        {{ $project->name . ' - ' . $project->team->name}}
                    </option>
                @endforeach
            </select>
            @error('project_id')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', session('task_data.name')) }}">
            @error('name') <p style="color: red;">{{ $message }}</p> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Task Status:</label><br>
            @php $statusOptions = TaskStatus::LIST; @endphp
            @foreach ($statusOptions as $value => $label)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="task_status" id="status_{{ $value }}"
                        value="{{ $value }}" {{ old('task_status', session('task_data.task_status')) == $value ? 'checked' : '' }}>
                    <label class="form-check-label" for="status_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
            @error('task_status') <p style="color: red;">{{ $message }}</p> @enderror
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">
                Confirm
            </button>
            <a href="{{ route('task.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>