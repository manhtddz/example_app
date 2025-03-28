@if(session(SESSION_ERROR))
    <div class="alert alert-danger">
        {{ session(SESSION_ERROR) }}
    </div>
@endif
<div class="container mt-4">
    <h2 class="mb-3">Project - Create</h2>
    <form action="{{ route('project.createConfirm') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="team">Team:</label><br>
            <select class="form-control" id="team" name="team_id">
                @php
                    $selectedTeamId = old('team_id', session('project_data.team_id'));
                @endphp
                <option value="">{{ '' }}
                </option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}" {{ $selectedTeamId == $team->id ? 'selected ' : '' }}>{{ $team->name }}
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
                value="{{ old('name', session('employee_data.name')) }}">
            @error('name') <p style="color: red;">{{ $message }}</p> @enderror
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">
                Confirm
            </button>
            <a href="{{ route('project.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>