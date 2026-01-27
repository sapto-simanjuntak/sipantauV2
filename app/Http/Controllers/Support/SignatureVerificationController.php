<?php

namespace App\Http\Controllers\Support;

use Illuminate\Http\Request;
// use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
// use App\Models\Modul\ServiceRequest;
use App\Models\Modul\ServiceRequest;
use App\Models\Modul\SignatureVerification;

class SignatureVerificationController extends Controller
{
    /**
     * Landing page untuk verification
     */
    public function index()
    {
        return view('pages.modul.verify.index');
    }

    /**
     * Verify signature dari QR code
     */
    public function verifyFromQr($data)
    {
        try {
            // Decode QR data (dari base64 JSON)
            $qrData = json_decode(base64_decode($data), true);

            if (!$qrData || !isset($qrData['ticket_number'])) {
                return $this->showVerificationResult([
                    'status' => 'invalid',
                    'message' => 'QR Code format invalid',
                    'details' => 'Unable to decode QR data'
                ]);
            }

            // Verify signature
            $result = $this->verifySignature($qrData);

            // Log verification attempt
            $this->logVerification($qrData, $result);

            return $this->showVerificationResult($result);
        } catch (\Exception $e) {
            Log::error('QR Verification Error: ' . $e->getMessage());

            return $this->showVerificationResult([
                'status' => 'invalid',
                'message' => 'Verification failed',
                'details' => 'An error occurred during verification'
            ]);
        }
    }

    /**
     * Manual verification via form
     */
    public function verifyManual(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string',
            'role' => 'required|in:requester,validator,technician',
            'verification_code' => 'required|string',
        ]);

        $qrData = [
            'ticket_number' => $request->ticket_number,
            'role' => $request->role,
            'verification_code' => $request->verification_code,
            'manual_verification' => true,
        ];

        $result = $this->verifySignature($qrData);
        $this->logVerification($qrData, $result);

        // ✅ GANTI INI: Redirect ke result page langsung
        return $this->showVerificationResult($result);
    }

    /**
     * Core verification logic
     */
    private function verifySignature(array $qrData)
    {
        $ticketNumber = $qrData['ticket_number'] ?? null;
        $role = $qrData['role'] ?? null;

        // 1. CHECK: Ticket exists?
        $ticket = ServiceRequest::with(['user', 'validator', 'assignedTechnician'])
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (!$ticket) {
            return [
                'status' => 'not_found',
                'message' => 'Ticket not found',
                'details' => "Ticket {$ticketNumber} does not exist in the system",
                'ticket_number' => $ticketNumber,
                'role' => $role,
                'qr_data' => $qrData,
            ];
        }

        // 2. VERIFY based on role
        switch ($role) {
            case 'requester':
                return $this->verifyRequesterSignature($ticket, $qrData);

            case 'validator':
                return $this->verifyValidatorSignature($ticket, $qrData);

            case 'technician':
                return $this->verifyTechnicianSignature($ticket, $qrData);

            default:
                return [
                    'status' => 'invalid',
                    'message' => 'Invalid role',
                    'details' => "Role '{$role}' is not recognized",
                    'ticket' => $ticket,
                    'role' => $role,
                ];
        }
    }

    /**
     * Verify REQUESTER signature
     */
    private function verifyRequesterSignature($ticket, $qrData)
    {
        // ✅ FIX: Format timestamp konsisten
        $ticketTimestamp = $ticket->created_at->format('Y-m-d H:i:s');

        // Expected verification code
        $expectedHash = hash('sha256', 'requester-' . $ticket->ticket_number . $ticket->user_id . $ticketTimestamp);

        // Check if hash matches
        if (isset($qrData['verification_code']) && $qrData['verification_code'] === $expectedHash) {
            return [
                'status' => 'valid',
                'message' => 'Requester signature verified successfully',
                'details' => [
                    'signer_name' => $ticket->requester_name,
                    'signer_role' => 'Requester',
                    'user_id' => $ticket->user_id,
                    'signed_at' => $ticket->created_at->format('d M Y, H:i:s') . ' WIB',
                    'action' => 'Created Service Request',
                    'ticket_status' => $ticket->ticket_status,
                ],
                'ticket' => $ticket,
                'role' => 'requester',
            ];
        }

        return [
            'status' => 'tampered',
            'message' => 'Signature verification failed',
            'details' => 'The signature hash does not match. This document may have been tampered with.',
            'ticket' => $ticket,
            'role' => 'requester',
        ];
    }

    /**
     * Verify VALIDATOR signature
     */
    private function verifyValidatorSignature($ticket, $qrData)
    {
        // Check if ticket has been validated
        if (!$ticket->validated_by || !$ticket->validated_at) {
            // Placeholder QR
            if (isset($qrData['status']) && $qrData['status'] === 'pending_validation') {
                return [
                    'status' => 'valid',
                    'message' => 'Pending validation',
                    'details' => [
                        'signer_role' => 'Validator',
                        'validation_status' => 'Awaiting validation',
                        'ticket_created' => $ticket->created_at->format('d M Y, H:i:s') . ' WIB',
                        'current_status' => $ticket->ticket_status,
                    ],
                    'ticket' => $ticket,
                    'role' => 'validator',
                    'is_placeholder' => true,
                ];
            }

            return [
                'status' => 'invalid',
                'message' => 'Validation signature not found',
                'details' => 'This ticket has not been validated yet',
                'ticket' => $ticket,
                'role' => 'validator',
            ];
        }

        // ✅ FIX: Format timestamp konsisten
        $validatorTimestamp = $ticket->validated_at->format('Y-m-d H:i:s');

        // Expected verification code
        $expectedHash = hash('sha256', 'validator-' . $ticket->ticket_number . $ticket->validated_by . $validatorTimestamp);

        // Check if hash matches
        if (isset($qrData['verification_code']) && $qrData['verification_code'] === $expectedHash) {
            return [
                'status' => 'valid',
                'message' => 'Validator signature verified successfully',
                'details' => [
                    'signer_name' => optional($ticket->validator)->name,
                    'signer_role' => 'Validator/Admin',
                    'user_id' => $ticket->validated_by,
                    'signed_at' => $ticket->validated_at->format('d M Y, H:i:s') . ' WIB',
                    'action' => 'Validated & ' . ucfirst($ticket->validation_status),
                    'validation_status' => $ticket->validation_status,
                    'validation_notes' => $ticket->validation_notes,
                    'ticket_status' => $ticket->ticket_status,
                ],
                'ticket' => $ticket,
                'role' => 'validator',
            ];
        }

        return [
            'status' => 'tampered',
            'message' => 'Signature verification failed',
            'details' => 'The signature hash does not match. This document may have been tampered with.',
            'ticket' => $ticket,
            'role' => 'validator',
        ];
    }

    /**
     * Verify TECHNICIAN signature
     */
    private function verifyTechnicianSignature($ticket, $qrData)
    {
        // Check if ticket has been assigned
        if (!$ticket->assigned_to) {
            // Placeholder
            if (isset($qrData['status']) && $qrData['status'] === 'unassigned') {
                return [
                    'status' => 'valid',
                    'message' => 'Pending assignment',
                    'details' => [
                        'signer_role' => 'Technician',
                        'assignment_status' => 'Awaiting technician assignment',
                        'ticket_created' => $ticket->created_at->format('d M Y, H:i:s') . ' WIB',
                        'current_status' => $ticket->ticket_status,
                    ],
                    'ticket' => $ticket,
                    'role' => 'technician',
                    'is_placeholder' => true,
                ];
            }

            return [
                'status' => 'invalid',
                'message' => 'Technician signature not found',
                'details' => 'This ticket has not been assigned to a technician yet',
                'ticket' => $ticket,
                'role' => 'technician',
            ];
        }

        // Check if resolved/closed
        if (!in_array($ticket->ticket_status, ['Resolved', 'Closed'])) {
            // In progress
            if (isset($qrData['status']) && $qrData['status'] === 'in_progress') {
                return [
                    'status' => 'valid',
                    'message' => 'Work in progress',
                    'details' => [
                        'signer_name' => optional($ticket->assignedTechnician)->name,
                        'signer_role' => 'Technician',
                        'assignment_status' => 'Assigned and working',
                        'assigned_at' => $ticket->assigned_at ? $ticket->assigned_at->format('d M Y, H:i:s') . ' WIB' : '-',
                        'current_status' => $ticket->ticket_status,
                    ],
                    'ticket' => $ticket,
                    'role' => 'technician',
                    'is_placeholder' => true,
                ];
            }
        }

        // Ticket is resolved/closed - verify signature
        $resolvedAt = $ticket->closed_at ?? $ticket->updated_at;

        // ✅ FIX: Format timestamp konsisten
        $technicianTimestamp = $resolvedAt->format('Y-m-d H:i:s');

        $expectedHash = hash('sha256', 'technician-' . $ticket->ticket_number . $ticket->assigned_to . $technicianTimestamp);

        // Check if hash matches
        if (isset($qrData['verification_code']) && $qrData['verification_code'] === $expectedHash) {
            return [
                'status' => 'valid',
                'message' => 'Technician signature verified successfully',
                'details' => [
                    'signer_name' => optional($ticket->assignedTechnician)->name,
                    'signer_role' => 'Technician',
                    'user_id' => $ticket->assigned_to,
                    'assigned_at' => $ticket->assigned_at ? $ticket->assigned_at->format('d M Y, H:i:s') . ' WIB' : '-',
                    'signed_at' => $resolvedAt->format('d M Y, H:i:s') . ' WIB',
                    'action' => 'Resolved & Completed',
                    'ticket_status' => $ticket->ticket_status,
                    'closure_notes' => $ticket->closure_notes,
                ],
                'ticket' => $ticket,
                'role' => 'technician',
            ];
        }

        return [
            'status' => 'tampered',
            'message' => 'Signature verification failed',
            'details' => 'The signature hash does not match. This document may have been tampered with.',
            'ticket' => $ticket,
            'role' => 'technician',
        ];
    }
    /**
     * Log verification attempt
     */
    private function logVerification($qrData, $result)
    {
        try {
            SignatureVerification::create([
                'ticket_number' => $qrData['ticket_number'] ?? null,
                'verified_role' => $qrData['role'] ?? null,
                'verification_status' => $result['status'],
                'verified_by_ip' => request()->ip(),
                'verified_by_user_agent' => request()->userAgent(),
                'verified_by_user_id' => auth()->check() ? auth()->id() : null,
                'verification_data' => $qrData,
                'failure_reason' => $result['status'] !== 'valid' ? ($result['details'] ?? $result['message']) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log verification: ' . $e->getMessage());
        }
    }

    /**
     * Show verification result page
     */
    private function showVerificationResult($result)
    {
        return view('pages.modul.verify.result', compact('result'));
    }

    /**
     * Show verification history for a ticket
     */
    public function history($ticketNumber)
    {
        $ticket = ServiceRequest::where('ticket_number', $ticketNumber)->firstOrFail();

        $verifications = SignatureVerification::where('ticket_number', $ticketNumber)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.modul.verify.history', compact('ticket', 'verifications'));
    }
}
