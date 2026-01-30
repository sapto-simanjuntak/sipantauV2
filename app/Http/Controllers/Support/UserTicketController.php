<?php

namespace App\Http\Controllers\Support;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Modul\HospitalUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Modul\ServiceRequest;
use Illuminate\Support\Facades\Cache;
use App\Models\Master\ProblemCategory;
use App\Models\Modul\ServiceRequestLog;
use Illuminate\Support\Facades\Storage;

class UserTicketController extends Controller
{
    /**
     * Display mobile-optimized ticket dashboard with pagination
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        // ✅ Build query with eager loading
        $query = ServiceRequest::with([
            'hospitalUnit:id,unit_code,unit_name',
            'problemCategory:id,category_name,category_code',
            'problemSubCategory:id,sub_category_name',
        ])->where('user_id', $user->id);

        // Apply status filter
        if ($status !== 'all') {
            $query->where('ticket_status', $status);
        }

        // Apply search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('issue_title', 'like', "%{$search}%")
                    ->orWhereHas('hospitalUnit', function ($q) use ($search) {
                        $q->where('unit_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('problemCategory', function ($q) use ($search) {
                        $q->where('category_name', 'like', "%{$search}%");
                    });
            });
        }

        // ✅ PAGINATION - 20 tickets per page
        $tickets = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // ✅ Get stats efficiently with caching
        $stats = $this->getUserStats($user->id);

        // For AJAX requests, return only partial HTML
        if ($request->ajax() || $request->wantsJson()) {
            return view('partials.ticket-list', compact('tickets'))->render();
        }

        return view('pages.modul.user-ticket.index', compact('tickets', 'stats', 'user'));
    }

    /**
     * Get user statistics (cached for 5 minutes)
     */
    private function getUserStats($userId)
    {
        return Cache::remember("user_stats_{$userId}", 300, function () use ($userId) {
            // ✅ Single query with conditional aggregation
            $stats = ServiceRequest::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN ticket_status IN ("Open", "Pending", "Approved", "Assigned", "In Progress") THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN ticket_status = "Open" THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN ticket_status = "In Progress" THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN ticket_status = "Resolved" THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN ticket_status = "Closed" THEN 1 ELSE 0 END) as closed
            ')
                ->where('user_id', $userId)
                ->first();

            return [
                'total' => $stats->total ?? 0,
                'active' => $stats->active ?? 0,
                'open' => $stats->open ?? 0,
                'in_progress' => $stats->in_progress ?? 0,
                'resolved' => $stats->resolved ?? 0,
                'closed' => $stats->closed ?? 0,
            ];
        });
    }

    /**
     * AJAX endpoint for stats (real-time update)
     */
    public function getStats()
    {
        $user = auth()->user();

        // Clear cache to get fresh data
        Cache::forget("user_stats_{$user->id}");

        $stats = $this->getUserStats($user->id);

        return response()->json($stats);
    }

    /**
     * AJAX endpoint untuk real-time data (deprecated - use index with AJAX)
     */
    public function getTickets(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Search tickets (deprecated - use index with search param)
     */
    public function search(Request $request)
    {
        $request->merge(['search' => $request->get('q', '')]);
        return $this->index($request);
    }

    // ========================================
    // KEEP EXISTING SHOW METHOD
    // ========================================
    public function show($ticket_number)
    {
        try {
            $ticket = ServiceRequest::with([
                'user:id,name,email',
                'hospitalUnit:id,unit_code,unit_name,unit_type',
                'problemCategory:id,category_name,category_code',
                'problemSubCategory:id,sub_category_name',
                'assignedTechnician:id,name,email',
                'validator:id,name',
                'assignedBy:id,name',
                'closedBy:id,name',
                'logs' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'logs.user:id,name'
            ])->where('ticket_number', $ticket_number)->firstOrFail();

            $technicians = User::role('teknisi')
                ->select('id', 'name', 'email')
                ->get();

            $slaStatus = $this->calculateSLAStatus($ticket);
            $timeline = $this->getTicketTimeline($ticket);

            return view('pages.modul.user-ticket.show', compact(
                'ticket',
                'technicians',
                'slaStatus',
                'timeline'
            ));
        } catch (\Exception $e) {
            Log::error('Show Ticket Error: ' . $e->getMessage());
            return redirect()->route('service.index')
                ->with('error', 'Tiket tidak ditemukan');
        }
    }

    private function calculateSLAStatus($ticket)
    {
        if (!$ticket->sla_deadline) {
            return [
                'status' => 'no_sla',
                'class' => 'secondary',
                'message' => 'No SLA Set',
                'hours_remaining' => null
            ];
        }

        $now = now();
        $deadline = \Carbon\Carbon::parse($ticket->sla_deadline);
        $hoursRemaining = $now->diffInHours($deadline, false);

        if ($hoursRemaining < 0) {
            return [
                'status' => 'overdue',
                'class' => 'danger',
                'message' => 'Overdue ' . abs(round($hoursRemaining)) . ' hours',
                'hours_remaining' => $hoursRemaining
            ];
        } elseif ($hoursRemaining <= 4) {
            return [
                'status' => 'warning',
                'class' => 'warning',
                'message' => round($hoursRemaining) . ' hours left',
                'hours_remaining' => $hoursRemaining
            ];
        } else {
            return [
                'status' => 'ok',
                'class' => 'success',
                'message' => round($hoursRemaining) . ' hours left',
                'hours_remaining' => $hoursRemaining
            ];
        }
    }

    private function getTicketTimeline($ticket)
    {
        $timeline = [];

        $timeline[] = [
            'icon' => 'bx-plus-circle',
            'color' => 'primary',
            'title' => 'Ticket Created',
            'description' => 'Created by ' . $ticket->requester_name,
            'timestamp' => $ticket->created_at,
            'user' => $ticket->user
        ];

        if ($ticket->validated_at) {
            $timeline[] = [
                'icon' => $ticket->validation_status === 'approved' ? 'bx-check-circle' : 'bx-x-circle',
                'color' => $ticket->validation_status === 'approved' ? 'success' : 'danger',
                'title' => $ticket->validation_status === 'approved' ? 'Ticket Approved' : 'Ticket Rejected',
                'description' => $ticket->validation_notes ?? 'No notes provided',
                'timestamp' => $ticket->validated_at,
                'user' => $ticket->validator
            ];
        }

        if ($ticket->assigned_at) {
            $timeline[] = [
                'icon' => 'bx-user-check',
                'color' => 'info',
                'title' => 'Ticket Assigned',
                'description' => 'Assigned to ' . optional($ticket->assignedTechnician)->name . ' ' . optional($ticket->assignedTechnician)->last_name,
                'timestamp' => $ticket->assigned_at,
                'user' => $ticket->assignedBy
            ];
        }

        if ($ticket->closed_at) {
            $timeline[] = [
                'icon' => 'bx-check-double',
                'color' => 'dark',
                'title' => 'Ticket Closed',
                'description' => $ticket->closure_notes ?? 'Ticket resolved and closed',
                'timestamp' => $ticket->closed_at,
                'user' => $ticket->closedBy
            ];
        }

        foreach ($ticket->logs as $log) {
            $timeline[] = [
                'icon' => 'bx-message-square-detail',
                'color' => 'secondary',
                'title' => $log->action_type ?? 'Activity',
                'description' => $log->notes ?? 'No description',
                'timestamp' => $log->created_at,
                'user' => $log->user
            ];
        }

        usort($timeline, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return $timeline;
    }

    public function edit($ticket_number)
    {
        $ticket = ServiceRequest::with([
            'user',
            'hospitalUnit',
            'problemCategory',
            'problemSubCategory'
        ])->where('ticket_number', $ticket_number)->firstOrFail();

        // Authorization check
        $user = auth()->user();

        // Admin/Superadmin bisa edit semua
        // Teknisi hanya bisa edit yang assigned ke dia
        // User hanya bisa edit ticket miliknya sendiri dengan status 'Open'
        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            if ($user->hasRole('teknisi')) {
                if ($ticket->assigned_to !== $user->id) {
                    abort(403, 'Anda tidak memiliki akses untuk edit tiket ini');
                }
            } else {
                // Regular user
                if ($ticket->user_id !== $user->id) {
                    abort(403, 'Anda tidak memiliki akses untuk edit tiket ini');
                }
                // User hanya bisa edit ticket dengan status Open
                if ($ticket->ticket_status !== 'Open') {
                    abort(403, 'Tiket dengan status "' . $ticket->ticket_status . '" tidak dapat diedit');
                }
            }
        }

        return view('pages.modul.user-ticket.edit', compact('ticket'));
    }

    public function update(Request $request, $ticket_number)
    {
        $ticket = ServiceRequest::where('ticket_number', $ticket_number)->firstOrFail();

        // ✅ SIMPAN DATA LAMA SEBELUM UPDATE
        $oldValues = $ticket->only([
            'requester_name',
            'requester_phone',
            'unit_id',
            'issue_title',
            'description',
            'problem_category_id',
            'problem_sub_category_id',
            'severity_level',
            'priority',
            'location',
            'device_affected',
            'expected_action',
        ]);

        // Authorization check
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            if ($user->hasRole('teknisi')) {
                if ($ticket->assigned_to !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk update tiket ini'
                    ], 403);
                }
            } else {
                if ($ticket->user_id !== $user->id || $ticket->ticket_status !== 'Open') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tiket tidak dapat diupdate'
                    ], 403);
                }
            }
        }

        // Validation rules
        $rules = [
            'requester_name' => 'required|string|max:100',
            'requester_phone' => 'nullable|string|max:20',
            'unit_id' => 'required|exists:hospital_units,id',
            'issue_title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_category_id' => 'required|exists:problem_categories,id',
            'problem_sub_category_id' => 'nullable|exists:problem_sub_categories,id',
            'severity_level' => 'required|in:Rendah,Sedang,Tinggi,Kritis',
            'impact_patient_care' => 'nullable|boolean',
            'occurrence_time' => 'required|date',
            'device_affected' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'expected_action' => 'required|string',
            'file_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];

        $category = ProblemCategory::find($request->problem_category_id);

        if ($category && $category->category_code === 'NET') {
            $rules['ip_address'] = 'nullable|ip';
            $rules['connection_status'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $validated = array_filter($validated, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Handle file upload
        if ($request->hasFile('file_path')) {
            if ($ticket->file_path && Storage::disk('public')->exists($ticket->file_path)) {
                Storage::disk('public')->delete($ticket->file_path);
            }

            $file = $request->file('file_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tickets', $filename, 'public');
            $validated['file_path'] = $filePath;

            // ✅ LOG FILE UPLOAD
            $this->logFileActivity($ticket, 'file_uploaded', $filename, $user, $request);
        }

        // Recalculate priority
        $unit = HospitalUnit::find($validated['unit_id']);
        $validated['priority'] = $this->calculatePriority(
            $validated['severity_level'],
            $unit->unit_type
        );

        // Recalculate SLA
        if ($ticket->problem_category_id != $validated['problem_category_id']) {
            $validated['sla_deadline'] = now()->addHours($category->default_sla_hours);
        }

        // ✅ UPDATE TICKET DALAM TRANSACTION
        DB::transaction(function () use ($ticket, $validated, $oldValues, $user, $request) {
            $ticket->update($validated);

            // ✅ LOG PERUBAHAN
            $this->logTicketUpdate($ticket, $oldValues, $validated, $user, $request);

            // ✅ CLEAR CACHE
            Cache::forget("user_stats_{$user->id}");
        });

        $redirectUrl = route('service.index');
        if ($user->hasRole('user') && !$user->hasAnyRole(['superadmin', 'admin', 'teknisi'])) {
            $redirectUrl = route('ticket.index');
        }

        Log::info('Ticket Updated', [
            'ticket_number' => $ticket_number,
            'user_id' => $user->id,
            'changes' => $ticket->getChanges()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil diupdate',
            'ticket_number' => $ticket_number,
            'redirect_url' => $redirectUrl
        ]);
    }

    // =========================================
    // ✅ LOGGING METHODS (TAMBAHKAN INI)
    // =========================================

    /**
     * Log ticket update activity
     */
    private function logTicketUpdate($ticket, array $oldValues, array $newValues, $user, Request $request): void
    {
        // Detect perubahan
        $changes = [];
        foreach ($newValues as $key => $newValue) {
            if (isset($oldValues[$key]) && $oldValues[$key] != $newValue) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $newValue
                ];
            }
        }

        if (empty($changes)) {
            return; // Tidak ada perubahan
        }

        // Generate human-readable notes
        $notes = $this->generateUpdateNotes($changes);

        ServiceRequestLog::create([
            'service_request_id' => $ticket->id,
            'user_id' => $user->id,
            'action_type' => 'ticket_update',
            'notes' => $notes,
            'old_status' => null,
            'new_status' => null,
            'metadata' => json_encode([
                'changes' => $changes,
                'updated_by_role' => $user->getRoleNames()->first(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ])
        ]);
    }

    /**
     * Log file upload/delete activity
     */
    private function logFileActivity($ticket, string $actionType, string $filename, $user, Request $request): void
    {
        ServiceRequestLog::create([
            'service_request_id' => $ticket->id,
            'user_id' => $user->id,
            'action_type' => 'file',
            'notes' => $actionType === 'file_uploaded'
                ? "File uploaded: {$filename}"
                : "File deleted: {$filename}",
            'old_status' => null,
            'new_status' => null,
            'metadata' => json_encode([
                'action' => $actionType,
                'filename' => $filename,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ])
        ]);
    }

    /**
     * Generate human-readable update notes
     */
    private function generateUpdateNotes(array $changes): string
    {
        $notes = [];

        $fieldLabels = [
            'requester_name' => 'Nama Pelapor',
            'requester_phone' => 'No. Telepon',
            'unit_id' => 'Unit',
            'issue_title' => 'Judul Masalah',
            'description' => 'Deskripsi',
            'problem_category_id' => 'Kategori',
            'problem_sub_category_id' => 'Sub-Kategori',
            'severity_level' => 'Tingkat Keparahan',
            'priority' => 'Prioritas',
            'location' => 'Lokasi',
            'device_affected' => 'Perangkat',
            'expected_action' => 'Tindakan yang Diharapkan',
        ];

        foreach ($changes as $field => $change) {
            $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            $notes[] = "{$label} diubah dari '{$change['old']}' ke '{$change['new']}'";
        }

        return implode(', ', $notes);
    }

    /**
     * Calculate priority helper (jika belum ada)
     */
    private function calculatePriority(string $severity, string $unitType): string
    {
        // Critical severity = Critical priority
        if ($severity === 'Kritis') {
            return 'Critical';
        }

        // High severity = High priority
        if ($severity === 'Tinggi') {
            return 'High';
        }

        // Medium severity + critical unit = High priority
        if ($severity === 'Sedang' && $unitType === 'critical') {
            return 'High';
        }

        // Medium severity + non-critical unit = Medium priority
        if ($severity === 'Sedang') {
            return 'Medium';
        }

        // Low severity = Low priority
        return 'Low';
    }
}
