<form id="form-edit" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" id="id" />
    <div class="mb-3">
        <label for="name" class="form-label">Nama User</label>
        <input type="text" class="form-control" name="name" id="name-edit" />
        <div class="invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Email</label>
        <textarea class="form-control" name="email" id="email-edit"></textarea>
        <div class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <label for="nama">Password</label>
        <input type="text" class="form-control" name="password">
    </div>

    <div class="form-group">
        <label for="nama">Roles</label>
        <select name="roles[]" class="form-control" multiple id="roles_edit">
        </select>
    </div>

    <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
</form>
