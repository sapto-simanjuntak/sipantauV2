{{-- Modal Assign --}}
<div class="modal fade" id="modal-assign" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bx bx-user-check me-2"></i>Assign Technician
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="form-assign">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        Pilih teknisi untuk menangani tiket <strong>{{ $ticket->ticket_number }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Teknisi <span class="text-danger">*</span></label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">- Pilih Teknisi -</option>
                            @foreach ($technicians as $tech)
                                <option value="{{ $tech->id }}"
                                    {{ $ticket->assigned_to == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->first_name }} {{ $tech->last_name }} - {{ $tech->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assign_notes" class="form-label">Catatan untuk Teknisi (Optional)</label>
                        <textarea class="form-control" id="assign_notes" name="notes" rows="3"
                            placeholder="Instruksi khusus atau informasi tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-assign">
                        <i class="bx bx-user-check me-1"></i>Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
