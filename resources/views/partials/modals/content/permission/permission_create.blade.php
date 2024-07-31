<form id="form" novalidate="" enctype="multipart/form-data">
    {{-- @csrf --}}
    <div class="mb-3">
        <label for="name" class="form-label">Nama Bidang</label>
        <input type="text" class="form-control" name="name" id="name" />
        <div class="invalid-feedback"></div>
    </div>
    <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
</form>
