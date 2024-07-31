<form id="form" novalidate="" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="name" class="font-weight-bold">Nama Proyek</label>
        <input type="text" class="form-control" name="name" placeholder="Nama Proyek" required>
    </div>
    <div class="mb-3">
        <label for="description">Deskripsi</label>
        <textarea class="form-control" name="description" placeholder="Deskripsi Proyek" required></textarea>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
