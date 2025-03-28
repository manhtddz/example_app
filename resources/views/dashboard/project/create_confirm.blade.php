<?php
use App\Models\Team;
?>

<div class="container mt-4">
    <div class="card p-4">
        <h4 class="mb-3">Project - Create confirm</h4>

        <form action="{{ route('project.create') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label"><strong>Team:</strong></label>
                <p class="border p-2 bg-light">{{ Team::getFieldById(session('project_data.team_id'), 'name')  }}</p>

                <input type="hidden" name="team_id" value="{{ session('project_data.team_id') }}">
                @error('team_id')
                    <p style="color: red;">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Name:</strong></label>
                <p class="border p-2 bg-light">{{ session('project_data.name') }}</p>

                <input type="hidden" name="name" value="{{ session('project_data.name') }}">
                @error('name')
                    <p style="color: red;">{{ $message }}</p>
                @enderror
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                    Save
                </button>
                <a href="{{ route('project.create') }}" class="btn btn-secondary">Cancel</a>
            </div>
            @include('dashboard.component.confirm-modal')

        </form>
    </div>
</div>