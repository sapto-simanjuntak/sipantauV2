{{-- Modal Reject --}}
<div class="modal fade" id="modal-reject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bx bx-x-circle me-2"></i>Reject Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="form-reject">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bx bx-error-alt me-2"></i>
                        Anda akan menolak tiket <strong>{{ $ticket->ticket_number }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Alasan Penolakan <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="validation_notes" rows="4"
                            placeholder="Jelaskan alasan penolakan tiket ini..." required></textarea>
                        <small class="text-muted">Catatan ini akan dikirim ke pelapor</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger" id="btn-submit-reject">
                        <i class="bx bx-x-circle me-1"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
