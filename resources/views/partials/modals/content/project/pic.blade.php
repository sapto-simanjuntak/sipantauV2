<form id="form-add-edit" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('POST') <!-- Ganti method PUT dengan POST -->
    <input type="hidden" name="project_id" id="project_id" />
    <div class="mb-3">
        <label for="name" class="font-weight-bold">Nama Proyek</label>
        <input type="text" class="form-control" name="name" id="name-pic" disabled>
    </div>
    <div class="mb-3">
        <label for="pic">PIC</label>
        <select class="form-control" name="pic[]" id="pic" multiple></select>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">ADD PIC</button>
    </div>
</form>
