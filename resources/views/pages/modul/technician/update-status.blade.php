<!-- Modal Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Tiket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="updateTicketNumber" name="ticket_number">

                    <!-- Current Status Display -->
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i>
                        <strong>Status Saat Ini:</strong> <span id="currentStatusDisplay">-</span>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">
                            Update Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="statusSelect" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Assigned">Assigned (Belum Dikerjakan)</option>
                            <option value="In Progress">In Progress (Sedang Dikerjakan)</option>
                            <option value="Resolved">Resolved (Selesai Dikerjakan)</option>
                            <option value="Completed">Completed (Ditutup)</option>
                        </select>
                        <small class="text-muted">
                            Pilih status sesuai dengan progress pekerjaan Anda
                        </small>
                    </div>

                    <hr>

                    <!-- Diagnosis -->
                    <div class="mb-3">
                        <label for="diagnosisInput" class="form-label">
                            <i class="bx bx-search-alt"></i> Diagnosis Masalah
                        </label>
                        <textarea class="form-control" id="diagnosisInput" name="diagnosis" rows="3"
                            placeholder="Jelaskan penyebab masalah yang ditemukan..."></textarea>
                        <small class="text-muted">
                            Contoh: "Hard disk rusak sektor bad", "Kabel jaringan putus", dll
                        </small>
                    </div>

                    <!-- Action Taken -->
                    <div class="mb-3">
                        <label for="actionTakenInput" class="form-label">
                            <i class="bx bx-wrench"></i> Tindakan yang Dilakukan
                        </label>
                        <textarea class="form-control" id="actionTakenInput" name="action_taken" rows="3"
                            placeholder="Jelaskan tindakan perbaikan yang sudah dilakukan..."></textarea>
                        <small class="text-muted">
                            Contoh: "Mengganti hard disk baru", "Memasang ulang kabel jaringan", dll
                        </small>
                    </div>

                    <!-- Technician Notes -->
                    <div class="mb-3">
                        <label for="techNotesInput" class="form-label">
                            <i class="bx bx-note"></i> Catatan Tambahan
                        </label>
                        <textarea class="form-control" id="techNotesInput" name="technician_notes" rows="2"
                            placeholder="Catatan atau informasi tambahan (opsional)..."></textarea>
                    </div>

                    <!-- Attachment -->
                    <div class="mb-3">
                        <label for="attachmentInput" class="form-label">
                            <i class="bx bx-paperclip"></i> Lampiran (Foto Hasil Perbaikan)
                        </label>
                        <input type="file" class="form-control" id="attachmentInput" name="attachment"
                            accept="image/*,.pdf">
                        <small class="text-muted">
                            Format: JPG, PNG, PDF (Max 5MB). Upload foto kondisi setelah perbaikan
                        </small>
                    </div>

                    <!-- Preview Image -->
                    <div id="imagePreview" class="mb-3" style="display: none;">
                        <label class="form-label">Preview:</label>
                        <div>
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail"
                                style="max-width: 300px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitUpdateStatus">
                        <i class="bx bx-save"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('after-js-modal')
    <script>
        // Preview image before upload
        $('#attachmentInput').change(function() {
            var file = this.files[0];

            if (file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#imagePreview').show();
                }

                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
            }
        });

        // Reset preview when modal closed
        $('#updateStatusModal').on('hidden.bs.modal', function() {
            $('#imagePreview').hide();
            $('#previewImg').attr('src', '');
        });
    </script>
@endpush
