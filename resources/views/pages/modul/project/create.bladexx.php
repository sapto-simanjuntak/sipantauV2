<!-- resources/views/pages/modul/project/form.blade.php -->
{{-- <form action="{{ route('projects.store') }}" method="POST">
@csrf
<div class="form-group">
    <label for="name">Nama Proyek</label>
    <input type="text" class="form-control" name="name" placeholder="Nama Proyek" required>
</div>
<div class="form-group">
    <label for="description">Deskripsi</label>
    <textarea class="form-control" name="description" placeholder="Deskripsi Proyek" required></textarea>
</div>
<div class="form-group">
    <label for="start_date">Tanggal Mulai</label>
    <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}" required>
</div>
<div class="form-group">
    <label for="end_date">Tanggal Selesai</label>
    <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
</div>
<div class="form-group">
    <label for="status">Status Proyek</label>
    <select name="status" class="form-control">
        @foreach ($statuses as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
<button type="submit" class="btn btn-primary">Simpan</button>
</form> --}}