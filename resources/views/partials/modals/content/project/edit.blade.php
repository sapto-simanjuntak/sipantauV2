<form id="form-edit" novalidate="" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" id="id" />
    <div class="mb-3">
        <label for="name" class="font-weight-bold">Nama Proyek</label>
        <input type="text" class="form-control" name="name" id="name-edit" placeholder="Nama Proyek" required>
    </div>
    <div class="mb-3">
        <label for="description" class="font-weight-bold"> <b>Sebelumnya :</b></label>
        <textarea style="height: 200px;" class="form-control" name="description_before" id="description-before-project"
            placeholder="Deskripsi Sebelumnya" required></textarea>
    </div>
    <div class="mb-3">
        <label for="description" class="font-weight-bold"><b>Setelahnya :</b></label>
        <textarea style="height: 200px;" class="form-control" name="description_after" id="description-after-project"
            placeholder="Deskripsi Setelahnya" required></textarea>
        <p class="text-danger"><b>NB : Lampirkan Flowchart/Alur/Form Sistem Untuk Pembuatan Baru</b></p>
    </div>
    <div class="mb-3">
        <label for="fileUpload" class="font-weight-bold"><b>Upload Gambar atau Dokumen PDF :</b></label>
        <input type="file" class="form-control" name="fileUpload" id="fileUpload" accept=".jpg,.jpeg,.png,.pdf">
        <small class="form-text text-muted">Hanya file gambar (.jpg, .jpeg, .png) dan dokumen PDF (.pdf) yang
            diperbolehkan.</small>
        <br>
        {{-- <img id="project-image" src="" alt="Project Image"> --}}
    </div>
    {{-- <div class="mb-3">
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
    </div> --}}
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">Update</button>

    </div>
</form>
