<form id="form-edit" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" id="id" />
    <div class="mb-3">
        <label for="name" class="form-label">Nama Bidang</label>
        <input type="text" class="form-control" name="name" id="name-edit" />
        <div class="invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" name="deskripsi" id="description-edit"></textarea>
        <div class="invalid-feedback"></div>
    </div>
    <button type="submit" class="btn btn-primary w-100">Submit</button>
</form>
