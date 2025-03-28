<div class="container mt-4">
    <h2 class="mb-3">Project - Update</h2>
    <form action="{{ route('project.updateConfirm', $project->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="team">Team:</label><br>

            <select class="form-control" id="team" name="team_id">
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}" {{ $project->team_id === $team->id ? 'selected' : '' }}>{{ $team->name }}
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
                value="{{ old('name', session('project_data.name', $project->name)) }}">
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