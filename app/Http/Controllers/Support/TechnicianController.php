<?php

namespace App\Http\Controllers\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Modul\ServiceRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Modul\TechnicianResponse;
use Yajra\DataTables\Facades\DataTables;

class TechnicianController extends Controller
{
    // ============================================
    // DASHBOARD - List tiket yang di-assign
    // ============================================
    public function index()
    {
        if (request()->ajax()) {
            try {
                $technicianId = session('user')['id'] ?? null;

                // Query tiket yang di-assign ke teknisi ini
                $query = ServiceRequest::where('assigned_to', $technicianId)
                    ->orderBy('created_at', 'desc');

                return DataTables::of($query)
                    ->addColumn('ticket_number', function ($ticket) {
                        return '<span class="badge bg-dark fs-6">' . $ticket->ticket_number . '</span>';
                    })
                    ->addColumn('reporter', function ($ticket) {
                        $user = DB::connection('mysql_passport')
                            ->table('users')
                            ->where('id', $ticket->user_created)
                            ->select('first_name', 'last_name')
                            ->first();

                        return $user
                            ? $user->first_name . ' ' . $user->last_name
                            : 'Unknown';
                    })
                    ->addColumn('category', function ($ticket) {
                        $category = DB::table('problem_category')
                            ->where('id', $ticket->problem_category_id)
                            ->first();

                        return $category
                            ? '<span class="badge bg-primary">' . $category->problem_name . '</span>'
                            : '-';
                    })
                    ->editColumn('severity_level', function ($ticket) {
                        $badges = [
                            'Rendah' => 'success',
                            'Sedang' => 'warning',
                            'Tinggi' => 'danger',
                            'Mendesak' => 'dark'
                        ];
                        $badgeClass = $badges[$ticket->severity_level] ?? 'secondary';

                        return '<span class="badge bg-' . $badgeClass . '">' . $ticket->severity_level . '</span>';
                    })
                    ->addColumn('status', function ($ticket) {
                        $statusBadges = [
                            'Assigned' => 'info',
                            'In Progress' => 'warning',
                            'Resolved' => 'success',
                            'Completed' => 'secondary',
                            'Closed' => 'secondary'
                        ];
                        $badgeClass = $statusBadges[$ticket->ticket_status] ?? 'secondary';

                        return '<span class="badge bg-' . $badgeClass . '">' . $ticket->ticket_status . '</span>';
                    })
                    ->editColumn('assigned_at', function ($ticket) {
                        return $ticket->assigned_at
                            ? \Carbon\Carbon::parse($ticket->assigned_at)->format('d M Y, H:i')
                            : '-';
                    })
                    ->addColumn('action', function ($ticket) {
                        $viewUrl = route('technician.ticket.show', $ticket->ticket_number);

                        // ============================================
                        // CEK APAKAH TIKET SUDAH SELESAI
                        // ============================================
                        $isCompleted = in_array($ticket->ticket_status, ['Resolved', 'Completed']);

                        $html = '<div class="d-flex align-items-center">';

                        // Button Lihat (Selalu aktif)
                        $html .= '
                        <button type="button" class="btn btn-primary btn-sm me-2"
                                onclick="window.location.href=\'' . $viewUrl . '\'">
                            <i class="bx bx-show me-0"></i> Lihat
                        </button>';

                        // Button Update (Disable kalau udah selesai)
                        if ($isCompleted) {
                            $html .= '
                        <button type="button" class="btn btn-secondary btn-sm" disabled
                                title="Pekerjaan sudah selesai">
                            <i class="bx bx-lock me-0"></i> Selesai
                        </button>';
                        } else {
                            $html .= '
                        <button type="button" class="btn btn-success btn-sm update-status"
                                data-ticket="' . $ticket->ticket_number . '"
                                data-status="' . $ticket->ticket_status . '">
                            <i class="bx bx-edit me-0"></i> Update
                        </button>';
                        }

                        $html .= '</div>';

                        return $html;
                    })
                    ->rawColumns(['ticket_number', 'category', 'severity_level', 'status', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('DataTable Error (Technician): ' . $e->getMessage());

                return response()->json([
                    'error' => 'Error loading data: ' . $e->getMessage()
                ], 500);
            }
        }

        return view('pages.modul.technician.index');
    }

    // ============================================
    // SHOW - Detail tiket untuk teknisi
    // ============================================
    public function show($ticketNumber)
    {
        try {
            $technicianId = session('user')['id'] ?? null;

            // Load dengan relationship
            $serviceRequest = ServiceRequest::with('technicianResponse')
                ->where('ticket_number', $ticketNumber)
                ->firstOrFail();

            if ($serviceRequest->assigned_to != $technicianId) {
                abort(403, 'Anda tidak memiliki akses ke tiket ini');
            }

            $user = DB::connection('mysql_passport')
                ->table('users')
                ->where('id', $serviceRequest->user_created)
                ->select('first_name', 'last_name', 'email')
                ->first();

            $category = DB::table('problem_category')
                ->where('id', $serviceRequest->problem_category_id)
                ->first();

            $subCategory = null;
            if ($serviceRequest->problem_sub_category_id) {
                $subCategory = DB::table('problem_sub_category')
                    ->where('id', $serviceRequest->problem_sub_category_id)
                    ->first();
            }

            // AMBIL DARI RELATIONSHIP
            $technicianResponse = $serviceRequest->technicianResponse;

            // DEBUG LOG
            Log::info('📄 Show Page Data', [
                'ticket' => $ticketNumber,
                'service_request_id' => $serviceRequest->id,
                'has_response' => $technicianResponse ? 'YES' : 'NO',
                'response_data' => $technicianResponse ? $technicianResponse->toArray() : null
            ]);

            return view('pages.modul.technician.show', compact(
                'serviceRequest',
                'user',
                'category',
                'subCategory',
                'technicianResponse'
            ));
        } catch (\Exception $e) {
            Log::error('❌ Error showing technician ticket', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('technician.dashboard')
                ->with('error', 'Tiket tidak ditemukan');
        }
    }

    // ============================================
    // UPDATE STATUS - Teknisi update progress
    // ============================================
    public function updateStatus(Request $request, $ticketNumber)
    {
        try {
            $technicianId = session('user')['id'] ?? null;

            Log::info('🔵 Update Status Started', [
                'ticket' => $ticketNumber,
                'technician' => $technicianId,
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'status' => 'required|in:Assigned,In Progress,Resolved,Completed',
                'diagnosis' => 'nullable|string',
                'action_taken' => 'nullable|string',
                'technician_notes' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);

            $serviceRequest = ServiceRequest::where('ticket_number', $ticketNumber)->firstOrFail();

            if ($serviceRequest->assigned_to != $technicianId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke tiket ini'
                ], 403);
            }

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('technician_attachments', $filename, 'public');

                Log::info('📎 File uploaded', ['path' => $attachmentPath]);
            }

            // Get or create technician response
            $technicianResponse = TechnicianResponse::where('service_request_id', $serviceRequest->id)->first();

            if (!$technicianResponse) {
                Log::info('🆕 Creating new technician response');

                $technicianResponse = TechnicianResponse::create([
                    'service_request_id' => $serviceRequest->id,
                    'ticket_number' => $serviceRequest->ticket_number,
                    'technician_id' => $technicianId,
                    'assigned_by' => $serviceRequest->assigned_by,
                    'assigned_at' => $serviceRequest->assigned_at,
                    'status' => $validated['status'],
                    'diagnosis' => $validated['diagnosis'] ?? null,
                    'action_taken' => $validated['action_taken'] ?? null,
                    'technician_notes' => $validated['technician_notes'] ?? null,
                    'attachment_path' => $attachmentPath,
                ]);
            } else {
                Log::info('♻️ Updating existing technician response', ['id' => $technicianResponse->id]);

                // Update data
                $technicianResponse->status = $validated['status'];

                if ($request->filled('diagnosis')) {
                    $technicianResponse->diagnosis = $validated['diagnosis'];
                }

                if ($request->filled('action_taken')) {
                    $technicianResponse->action_taken = $validated['action_taken'];
                }

                if ($request->filled('technician_notes')) {
                    $technicianResponse->technician_notes = $validated['technician_notes'];
                }

                if ($attachmentPath) {
                    $technicianResponse->attachment_path = $attachmentPath;
                }

                $technicianResponse->save();
            }

            // Update timeline based on status
            if ($validated['status'] === 'In Progress' && !$technicianResponse->started_at) {
                $technicianResponse->started_at = now();
                $technicianResponse->save();
                Log::info('⏰ Started at updated');
            }

            if ($validated['status'] === 'Resolved' && !$technicianResponse->resolved_at) {
                $technicianResponse->resolved_at = now();
                $technicianResponse->save();
                Log::info('✅ Resolved at updated');
            }

            if ($validated['status'] === 'Completed' && !$technicianResponse->completion_time) {
                $technicianResponse->completion_time = now();
                $technicianResponse->save();
                Log::info('🎉 Completion time updated');
            }

            // Update service request status
            $serviceRequest->ticket_status = $validated['status'];
            $serviceRequest->save();

            Log::info('💾 Service request updated', ['status' => $validated['status']]);

            // Refresh data dari DB
            $technicianResponse->refresh();
            $serviceRequest->refresh();

            Log::info('✅ Update Success', [
                'technician_response' => $technicianResponse->toArray(),
                'service_request_status' => $serviceRequest->ticket_status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status tiket berhasil diupdate',
                'data' => [
                    'status' => $technicianResponse->status,
                    'diagnosis' => $technicianResponse->diagnosis,
                    'action_taken' => $technicianResponse->action_taken,
                    'technician_notes' => $technicianResponse->technician_notes,
                    'attachment_path' => $technicianResponse->attachment_path,
                    'started_at' => $technicianResponse->started_at,
                    'resolved_at' => $technicianResponse->resolved_at,
                    'completion_time' => $technicianResponse->completion_time,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error updating ticket status', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
