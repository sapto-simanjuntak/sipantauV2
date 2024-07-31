<form id="form" novalidate="" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Nama Bidang</label>
        <input type="text" class="form-control" name="name" id="name" />
        <div class="invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" name="deskripsi" id="description"></textarea>
        <div class="invalid-feedback"></div>
    </div>
    <button type="submit" class="btn btn-primary w-100">Submit</button>
</form>
