<form id="form-edit" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" id="id" />
    <div class="mb-3">
        <label for="name" class="form-label">Permission Name</label>
        <input type="text" class="form-control" name="name" id="name-edit" />
        <div class="invalid-feedback"></div>
    </div>
    <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
</form>
