<?php
use App\Const\TaskStatus;
?>
<div class="container mt-4">
    <h2 class="mb-3">Task - Update</h2>
    <form action="{{ route('task.updateConfirm', $task->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="project">Project:</label><br>
            <select class="form-control" id="project" name="project_id">
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ $task->project_id === $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
            @error('team_id')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" name="name"
                value="{{ old('name', session('task_data.name', $task->name)) }}">
            @error('name') <p style="color: red;">{{ $message }}</p> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Task Status:</label><br>
            @php $statusOptions = TaskStatus::LIST; @endphp
            @foreach ($statusOptions as $value => $label)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="task_status" id="status_{{ $value }}"
                        value="{{ $value }}" {{ old('task_status', session('task_data.task_status', $task->task_status)) == $value ? 'checked' : '' }}>
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