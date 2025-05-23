<div class="container mt-4">
    <h2 class="mb-3">Project - Update</h2>
    <form action="{{ route('project.updateConfirm', $project->id) . ($teamId ? '?teamId=' . $teamId : '') }}"
        method="POST">
        @csrf
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
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>