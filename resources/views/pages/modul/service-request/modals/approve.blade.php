{{-- Modal Approve --}}
<div class="modal fade" id="modal-approve" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bx bx-check-circle me-2"></i>Approve Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="form-approve">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        Anda akan menyetujui tiket <strong>{{ $ticket->ticket_number }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">Catatan Approval (Optional)</label>
                        <textarea class="form-control" id="approve_notes" name="validation_notes" rows="3"
                            placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="btn-submit-approve">
                        <i class="bx bx-check-circle me-1"></i>Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
