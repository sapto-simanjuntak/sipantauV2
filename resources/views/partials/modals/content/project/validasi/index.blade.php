<form id="form-add-validasi" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('POST') <!-- Ganti method PUT dengan POST -->
    <input type="hidden" name="project_id" id="project_id_validasi" />
    <div class="mb-3">
        <label for="name" class="font-weight-bold">Nama Proyek</label>
        <input type="text" class="form-control" name="name" id="name-validasi" disabled>
    </div>
    <div class="mb-3">
        <label for="status">Status Validasi</label>
        <select name="validated" id="validasi" class="form-control">
        </select>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary btn-sm">Validated</button>
    </div>
</form>
