{{-- Modal Update Status --}}
<div class="modal fade" id="modal-update-status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bx bx-edit me-2"></i>Update Ticket Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update-status">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        Ubah status tiket <strong>{{ $ticket->ticket_number }}</strong>
                        <br>
                        <small>Current status: <span
                                class="badge bg-secondary">{{ $ticket->ticket_status }}</span></small>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status Baru <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">- Pilih Status -</option>
                            <option value="Open" {{ $ticket->ticket_status === 'Open' ? 'selected' : '' }}>Open
                            </option>
                            <option value="Pending" {{ $ticket->ticket_status === 'Pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="Approved" {{ $ticket->ticket_status === 'Approved' ? 'selected' : '' }}>
                                Approved</option>
                            <option value="Assigned" {{ $ticket->ticket_status === 'Assigned' ? 'selected' : '' }}>
                                Assigned</option>
                            <option value="In Progress"
                                {{ $ticket->ticket_status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $ticket->ticket_status === 'Resolved' ? 'selected' : '' }}>
                                Resolved</option>
                            <option value="Closed" {{ $ticket->ticket_status === 'Closed' ? 'selected' : '' }}>Closed
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status_notes" class="form-label">Catatan (Optional)</label>
                        <textarea class="form-control" id="status_notes" name="notes" rows="3"
                            placeholder="Jelaskan alasan perubahan status..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" id="btn-submit-status">
                        <i class="bx bx-edit me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
