<?php
use App\Models\Project;
use App\Const\TaskStatus;
?>

<div class="container mt-4">
    <div class="card p-4">
        <h4 class="mb-3">Task - Create confirm</h4>

        <form action="{{ route('task.create') . ($projectId ? '?projectId=' . $projectId : '') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label"><strong>Project:</strong></label>
                <p class="border p-2 bg-light">{{ Project::getFieldById(session('task_data.project_id'), 'name')  }}</p>

                <input type="hidden" name="project_id" value="{{ session('task_data.project_id') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Name:</strong></label>
                <p class="border p-2 bg-light">{{ session('task_data.name') }}</p>

                <input type="hidden" name="name" value="{{ session('task_data.name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Description:</strong></label>
                <p class="border p-2 bg-light">{{ session('task_data.description') }}</p>

                <input type="hidden" name="description" value="{{ session('task_data.description') }}">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Status:</strong></label>
                <p class="border p-2 bg-light">{{ TaskStatus::getName(session('task_data.task_status')) }}</p>

                <input type="hidden" name="task_status" value="{{ session('task_data.task_status') }}">
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                    Save
                </button>
                <a href="{{ route('task.create') }}" class="btn btn-secondary">Cancel</a>
            </div>
            @include('dashboard.component.confirm-modal')

        </form>
    </div>
</div>