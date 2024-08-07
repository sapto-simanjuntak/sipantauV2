<form id="form-set-project" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <input type="hidden" name="id" id="set-id" />
    <div class="mb-3">
        <label for="name" class="font-weight-bold">Nama Proyek</label>
        <input type="text" class="form-control" name="name" id="set_name-edit" placeholder="Nama Proyek" required
            disabled>
    </div>
    <div class="mb-3">
        <label for="start_date">Tanggal Mulai</label>
        <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}" required>
    </div>
    <div class="mb-3">
        <label for="end_date">Tanggal Selesai</label>
        <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d', strtotime('+1 month')) }}"
            required>
    </div>
    <div class="mb-3">
        <label for="status">Status Proyek</label>
        <select name="status" id="set-status-project" class="form-control">
        </select>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">Set Status </button>

    </div>
</form>
