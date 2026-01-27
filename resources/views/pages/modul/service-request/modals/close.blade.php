{{-- Modal Close Ticket --}}
<div class="modal fade" id="modal-close" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bx bx-check-double me-2"></i>Close Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="form-close">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bx bx-check-circle me-2"></i>
                        Anda akan menutup tiket <strong>{{ $ticket->ticket_number }}</strong>
                        <br><small>Pastikan masalah sudah terselesaikan dengan baik</small>
                    </div>

                    <div class="mb-3">
                        <label for="close_notes" class="form-label">Catatan Penutupan <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="close_notes" name="notes" rows="4"
                            placeholder="Jelaskan solusi dan tindakan yang telah dilakukan..." required></textarea>
                        <small class="text-muted">Catatan ini akan dikirim ke pelapor</small>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Note:</strong> Setelah ditutup, tiket tidak dapat diubah lagi kecuali dibuka kembali
                        oleh admin.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-dark" id="btn-submit-close">
                        <i class="bx bx-check-double me-1"></i>Close Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
