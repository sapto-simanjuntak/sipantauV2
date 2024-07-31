<form id="form" novalidate="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="nama">User Name</label>
        <input type="text" class="form-control" name="name" placeholder="Nama">
    </div>
    <div class="form-group">
        <label for="nama">Email</label>
        <input type="text" class="form-control" name="email">
    </div>
    <div class="form-group">
        <label for="nama">Password</label>
        <input type="text" class="form-control" name="password">
    </div>
    <div class="form-group">
        <label for="nama">Roles</label>
        <select class="form-control" name="roles[]" id="roles" multiple></select>
    </div>

    <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>
</form>
