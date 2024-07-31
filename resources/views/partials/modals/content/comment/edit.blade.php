<form id="form-edit" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" id="id" />
    <div class="mb-3">
        <label for="name" class="font-weight-bold">Nama Task</label>
        <input type="text" class="form-control" name="title" id="title-edit" placeholder="Nama Proyek" required>
    </div>
    <div class="mb-3">
        <label for="description">Deskripsi</label>
        <textarea class="form-control" name="description" id="description-edit" placeholder="Deskripsi Proyek" required></textarea>
    </div>
    <div class="mb-3">
        <label for="start_date">Tanggal Mulai</label>
        <input type="date" class="form-control" name="start_date" id="start-edit" value="{{ date('Y-m-d') }}"
            required>
    </div>
    <div class="mb-3">
        <label for="end_date">Tanggal Selesai</label>
        <input type="date" class="form-control" name="end_date" id="end-edit"
            value="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
    </div>
    <div class="mb-3">
        <label for="status">Status Proyek</label>
        <select name="status" id="status-edit" class="form-control">
            <!-- Opsi status akan diisi secara dinamis melalui JavaScript -->
        </select>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">Update</button>

    </div>
</form>
