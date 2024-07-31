<form id="form-add-task" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="mb-3">
        <input type="hidden" name="project_id" id="project_id" />
        <label for="name" class="font-weight-bold">Task Name</label>
        <input type="text" class="form-control" name="title" placeholder="Task Name" required>
    </div>
    <div class="mb-3">
        <label for="description">Description</label>
        <textarea class="form-control" name="description" placeholder="Task Description" required></textarea>
    </div>
    <div class="mb-3">
        <label for="start_date">Start Date</label>
        <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}" required>
    </div>
    <div class="mb-3">
        <label for="end_date">End Date</label>
        <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d', strtotime('+1 month')) }}"
            required>
    </div>
    <div class="mb-3">
        <label for="status">Status Proyek</label>
        <select name="status" id="status" class="form-control">
        </select>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
