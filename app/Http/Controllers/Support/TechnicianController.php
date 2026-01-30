<?php

namespace App\Http\Controllers\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Modul\ServiceRequest;
use Illuminate\Support\Facades\Cache;
use App\Models\Modul\ServiceRequestLog;
use Illuminate\Support\Facades\Storage;
use App\Models\Modul\TechnicianResponse;
use Yajra\DataTables\Facades\DataTables;

class TechnicianController extends Controller
{
    // =========================================
    // CONFIGURATION CONSTANTS
    // =========================================

    const CACHE_TTL = 5; // minutes
    const ITEMS_PER_PAGE = 20;
    const SLA_WARNING_HOURS = 4;

    const ALLOWED_STATUS_TRANSITIONS = [
        'Assigned' => ['In Progress'],
        'In Progress' => ['Pending', 'Resolved'],
        'Pending' => ['In Progress', 'Resolved'],
    ];

    // =========================================
    // MAIN CONTROLLER METHODS
    // =========================================

    /**
     * Display technician's assigned tickets
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $this->getFiltersFromRequest($request);

        // Build query
        $query = $this->buildTicketQuery($user->id, $filters);

        // Paginate results
        $tickets = $query->orderByRaw($this->getPriorityOrderSQL())
            ->orderBy('created_at', 'desc')
            ->paginate(self::ITEMS_PER_PAGE)
            ->withQueryString();

        // Get statistics
        $stats = $this->getTechnicianStats($user->id);

        // Return partial view for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return view('pages.modul.technician.partials.ticket-list', compact('tickets'))->render();
        }

        return view('pages.modul.technician.index', compact('tickets', 'stats', 'user'));
    }

    /**
     * Show ticket detail
     */
    public function show(string $ticket_number)
    {
        try {
            $user = auth()->user();

            // Get ticket with authorization check
            $ticket = $this->getAuthorizedTicket($ticket_number, $user->id);

            // Prepare view data
            $viewData = [
                'ticket' => $ticket,
                'slaStatus' => $this->calculateSLAStatus($ticket),
                'timeline' => $this->getTicketTimeline($ticket),
                'workDuration' => $this->getWorkDuration($ticket),
                'allowedActions' => $this->getAllowedActions($ticket),
            ];

            return view('pages.modul.technician.show', $viewData);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->handleUnauthorizedAccess($ticket_number);
        } catch (\Exception $e) {
            return $this->handleShowError($ticket_number, $e);
        }
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, string $ticket_number)
    {
        try {
            $user = auth()->user();
            $ticket = $this->getAuthorizedTicket($ticket_number, $user->id);

            // Validate request
            $validated = $request->validate([
                'status' => 'required|in:In Progress,Pending,Resolved',
                'note' => 'nullable|string|max:1000'
            ]);

            // Check if transition is allowed
            if (!$this->isStatusTransitionAllowed($ticket->ticket_status, $validated['status'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat mengubah status dari {$ticket->ticket_status} ke {$validated['status']}"
                ], 422);
            }

            // Perform update in transaction
            DB::transaction(function () use ($ticket, $validated, $user, $request) {
                $oldStatus = $ticket->ticket_status;

                // Update ticket
                $ticket->update(['ticket_status' => $validated['status']]);

                // Log the change
                $this->logStatusChange($ticket, $oldStatus, $validated['status'], $validated['note'] ?? null, $user, $request);

                // Clear cache
                $this->clearTechnicianCache($user->id);

                // TODO: Send notification if resolved
                if ($validated['status'] === 'Resolved' && $ticket->user) {
                    // Notification::send($ticket->user, new TicketResolvedNotification($ticket));
                }
            });

            Log::info('Technician updated ticket status', [
                'technician_id' => $user->id,
                'ticket_number' => $ticket_number,
                'old_status' => $ticket->ticket_status,
                'new_status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status berhasil diupdate ke {$validated['status']}",
                'ticket' => [
                    'ticket_number' => $ticket->ticket_number,
                    'status' => $ticket->ticket_status
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan atau tidak di-assign ke Anda'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update Status Error', [
                'technician_id' => auth()->id(),
                'ticket_number' => $ticket_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status tiket'
            ], 500);
        }
    }

    /**
     * Add progress note
     */
    public function addNote(Request $request, string $ticket_number)
    {
        try {
            $user = auth()->user();
            $ticket = $this->getAuthorizedTicket($ticket_number, $user->id);

            $validated = $request->validate([
                'note' => 'required|string|max:1000'
            ]);

            // Log the note
            $this->logProgressNote($ticket, $validated['note'], $user, $request);

            Log::info('Technician added progress note', [
                'technician_id' => $user->id,
                'ticket_number' => $ticket_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan progress berhasil ditambahkan'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan atau tidak di-assign ke Anda'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan tidak boleh kosong',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Add Note Error', [
                'technician_id' => auth()->id(),
                'ticket_number' => $ticket_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan catatan'
            ], 500);
        }
    }

    /**
     * Get statistics (AJAX endpoint)
     */
    public function getStats()
    {
        $user = auth()->user();
        $stats = $this->getTechnicianStats($user->id);

        return response()->json($stats);
    }

    // =========================================
    // QUERY BUILDERS
    // =========================================

    /**
     * Build ticket query with filters
     */
    private function buildTicketQuery(int $technicianId, array $filters)
    {
        $query = ServiceRequest::with([
            'user:id,name,email',
            'hospitalUnit:id,unit_code,unit_name,unit_type',
            'problemCategory:id,category_name,category_code',
            'problemSubCategory:id,sub_category_name',
            'assignedTechnician:id,name',
        ])->where('assigned_to', $technicianId);

        // Apply status filter
        if ($filters['status'] !== 'all') {
            $query->where('ticket_status', $filters['status']);
        }

        // Apply priority filter
        if ($filters['priority'] !== 'all') {
            $query->where('priority', $filters['priority']);
        }

        // Apply search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('ticket_number', 'like', "%{$filters['search']}%")
                    ->orWhere('issue_title', 'like', "%{$filters['search']}%")
                    ->orWhere('requester_name', 'like', "%{$filters['search']}%")
                    ->orWhere('location', 'like', "%{$filters['search']}%")
                    ->orWhereHas('hospitalUnit', function ($q) use ($filters) {
                        $q->where('unit_name', 'like', "%{$filters['search']}%")
                            ->orWhere('unit_code', 'like', "%{$filters['search']}%");
                    })
                    ->orWhereHas('problemCategory', function ($q) use ($filters) {
                        $q->where('category_name', 'like', "%{$filters['search']}%");
                    })
                    ->orWhereHas('user', function ($q) use ($filters) {
                        $q->where('name', 'like', "%{$filters['search']}%")
                            ->orWhere('email', 'like', "%{$filters['search']}%");
                    });
            });
        }

        return $query;
    }

    /**
     * Get authorized ticket
     */
    private function getAuthorizedTicket(string $ticket_number, int $technicianId)
    {
        return ServiceRequest::with([
            'user:id,name,email',
            'hospitalUnit:id,unit_code,unit_name,unit_type',
            'problemCategory:id,category_name,category_code',
            'problemSubCategory:id,sub_category_name',
            'assignedTechnician:id,name,email',
            'validator:id,name',
            'assignedBy:id,name',
            'closedBy:id,name',
            'logs' => fn($query) => $query->orderBy('created_at', 'desc'),
            'logs.user:id,name'
        ])
            ->where('ticket_number', $ticket_number)
            ->where('assigned_to', $technicianId)
            ->firstOrFail();
    }

    // =========================================
    // STATISTICS & ANALYTICS
    // =========================================

    /**
     * Get technician statistics with caching
     */
    private function getTechnicianStats(int $technicianId): array
    {
        $cacheKey = "technician_stats_{$technicianId}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($technicianId) {
            $baseQuery = ServiceRequest::where('assigned_to', $technicianId);

            return [
                // Status counts
                'total' => (clone $baseQuery)->count(),
                'open' => (clone $baseQuery)->where('ticket_status', 'Open')->count(),
                'assigned' => (clone $baseQuery)->where('ticket_status', 'Assigned')->count(),
                'in_progress' => (clone $baseQuery)->where('ticket_status', 'In Progress')->count(),
                'pending' => (clone $baseQuery)->where('ticket_status', 'Pending')->count(),
                'resolved' => (clone $baseQuery)->where('ticket_status', 'Resolved')->count(),
                'closed' => (clone $baseQuery)->where('ticket_status', 'Closed')->count(),

                // Priority counts (active only)
                'critical' => (clone $baseQuery)
                    ->where('priority', 'Critical')
                    ->whereNotIn('ticket_status', ['Resolved', 'Closed'])
                    ->count(),
                'high' => (clone $baseQuery)
                    ->where('priority', 'High')
                    ->whereNotIn('ticket_status', ['Resolved', 'Closed'])
                    ->count(),

                // SLA metrics
                'overdue' => (clone $baseQuery)
                    ->where('sla_deadline', '<', now())
                    ->whereNotIn('ticket_status', ['Resolved', 'Closed'])
                    ->count(),
                'due_soon' => (clone $baseQuery)
                    ->whereBetween('sla_deadline', [now(), now()->addHours(self::SLA_WARNING_HOURS)])
                    ->whereNotIn('ticket_status', ['Resolved', 'Closed'])
                    ->count(),
            ];
        });
    }

    /**
     * Calculate SLA status
     */
    private function calculateSLAStatus($ticket): array
    {
        if (!$ticket->sla_deadline) {
            return [
                'status' => 'no_sla',
                'class' => 'secondary',
                'message' => 'No SLA',
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
                'message' => 'Overdue ' . abs(round($hoursRemaining)) . 'h',
                'hours_remaining' => $hoursRemaining
            ];
        }

        if ($hoursRemaining <= self::SLA_WARNING_HOURS) {
            return [
                'status' => 'warning',
                'class' => 'warning',
                'message' => round($hoursRemaining) . 'h left',
                'hours_remaining' => $hoursRemaining
            ];
        }

        return [
            'status' => 'ok',
            'class' => 'success',
            'message' => round($hoursRemaining) . 'h left',
            'hours_remaining' => $hoursRemaining
        ];
    }

    /**
     * Get work duration
     */
    private function getWorkDuration($ticket): ?array
    {
        if ($ticket->ticket_status !== 'In Progress') {
            return null;
        }

        $inProgressLog = $ticket->logs()
            ->where('action_type', 'status_change')
            ->where('new_status', 'In Progress')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$inProgressLog) {
            return null;
        }

        $duration = $inProgressLog->created_at->diffInMinutes(now());

        return [
            'minutes' => $duration,
            'hours' => floor($duration / 60),
            'formatted' => $this->formatDuration($duration)
        ];
    }

    // =========================================
    // TIMELINE & ACTIVITY LOGS
    // =========================================

    /**
     * Get ticket timeline
     */
    private function getTicketTimeline($ticket): array
    {
        $timeline = [];

        // Ticket created
        $timeline[] = [
            'icon' => 'bx-plus-circle',
            'color' => 'primary',
            'title' => 'Ticket Created',
            'description' => "Ticket dibuat oleh {$ticket->requester_name}",
            'timestamp' => $ticket->created_at,
            'user' => $ticket->user
        ];

        // Validation event
        if ($ticket->validation_status !== 'pending' && $ticket->validated_at) {
            $timeline[] = [
                'icon' => $ticket->validation_status === 'approved' ? 'bx-check-circle' : 'bx-x-circle',
                'color' => $ticket->validation_status === 'approved' ? 'success' : 'danger',
                'title' => 'Ticket ' . ucfirst($ticket->validation_status),
                'description' => $ticket->validation_notes ?? 'Ticket telah di-' . $ticket->validation_status,
                'timestamp' => \Carbon\Carbon::parse($ticket->validated_at),
                'user' => $ticket->validator
            ];
        }

        // Assignment event
        if ($ticket->assigned_to && $ticket->assigned_at) {
            $timeline[] = [
                'icon' => 'bx-user-check',
                'color' => 'info',
                'title' => 'Ticket Assigned',
                'description' => "Assigned to " . ($ticket->assignedTechnician->name ?? 'Technician'),
                'timestamp' => \Carbon\Carbon::parse($ticket->assigned_at),
                'user' => $ticket->assignedBy
            ];
        }

        // Activity logs
        if ($ticket->logs && $ticket->logs->count() > 0) {
            foreach ($ticket->logs as $log) {
                $timelineItem = $this->parseLogToTimeline($log);
                if ($timelineItem) {
                    $timeline[] = $timelineItem;
                }
            }
        }

        // Closure event
        if ($ticket->ticket_status === 'Closed' && $ticket->closed_at) {
            $timeline[] = [
                'icon' => 'bx-lock-alt',
                'color' => 'secondary',
                'title' => 'Ticket Closed',
                'description' => $ticket->closure_notes ?? 'Ticket telah ditutup',
                'timestamp' => \Carbon\Carbon::parse($ticket->closed_at),
                'user' => $ticket->closedBy
            ];
        }

        // Sort by timestamp (newest first)
        usort($timeline, fn($a, $b) => $b['timestamp']->timestamp - $a['timestamp']->timestamp);

        return $timeline;
    }

    /**
     * Parse log entry to timeline item
     */
    private function parseLogToTimeline($log): ?array
    {
        $item = [
            'timestamp' => $log->created_at,
            'user' => $log->user
        ];

        switch ($log->action_type) {
            case 'status_change':
                $item['icon'] = 'bx-refresh';
                $item['color'] = $this->getStatusColor($log->new_status);
                $item['title'] = $this->getStatusChangeTitle($log->old_status, $log->new_status);
                $item['description'] = "Status: {$log->old_status} → {$log->new_status}";
                if ($log->notes) {
                    $item['description'] .= " | {$log->notes}";
                }
                break;

            case 'progress_note':
                $item['icon'] = 'bx-note';
                $item['color'] = 'warning';
                $item['title'] = 'Progress Note';
                $item['description'] = $log->notes ?? 'Catatan progress ditambahkan';
                break;

            case 'comment':
                $item['icon'] = 'bx-comment-dots';
                $item['color'] = 'info';
                $item['title'] = 'Comment';
                $item['description'] = $log->notes ?? 'Komentar ditambahkan';
                break;

            case 'assignment':
                $item['icon'] = 'bx-user-check';
                $item['color'] = 'info';
                $item['title'] = 'Assignment Changed';
                $item['description'] = $log->notes ?? 'Teknisi di-assign ulang';
                break;

            case 'validation':
                $item['icon'] = $log->new_status === 'Approved' ? 'bx-check-circle' : 'bx-x-circle';
                $item['color'] = $log->new_status === 'Approved' ? 'success' : 'danger';
                $item['title'] = "Ticket {$log->new_status}";
                $item['description'] = $log->notes ?? 'Ticket divalidasi';
                break;

            case 'file':
                $item['icon'] = 'bx-paperclip';
                $item['color'] = 'secondary';
                $item['title'] = 'File Activity';
                $item['description'] = $log->notes ?? 'File diupload/dihapus';
                break;

            default:
                $item['icon'] = 'bx-info-circle';
                $item['color'] = 'secondary';
                $item['title'] = ucfirst(str_replace('_', ' ', $log->action_type));
                $item['description'] = $log->notes ?? 'Activity logged';
                break;
        }

        return $item;
    }

    // =========================================
    // LOGGING METHODS
    // =========================================

    /**
     * Log status change
     */
    private function logStatusChange($ticket, string $oldStatus, string $newStatus, ?string $note, $user, Request $request): void
    {
        ServiceRequestLog::create([
            'service_request_id' => $ticket->id,
            'user_id' => $user->id,
            'action_type' => 'status_change',
            'notes' => $note,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'metadata' => json_encode([
                'changed_by_role' => $user->getRoleNames()->first(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ])
        ]);
    }

    /**
     * Log progress note
     */
    private function logProgressNote($ticket, string $note, $user, Request $request): void
    {
        ServiceRequestLog::create([
            'service_request_id' => $ticket->id,
            'user_id' => $user->id,
            'action_type' => 'progress_note',
            'notes' => $note,
            'old_status' => null,
            'new_status' => null,
            'metadata' => json_encode([
                'added_by_role' => $user->getRoleNames()->first(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ])
        ]);
    }

    // =========================================
    // HELPER METHODS
    // =========================================

    /**
     * Get filters from request
     */
    private function getFiltersFromRequest(Request $request): array
    {
        return [
            'status' => $request->get('status', 'all'),
            'priority' => $request->get('priority', 'all'),
            'search' => $request->get('search', ''),
        ];
    }

    /**
     * Get priority order SQL
     */
    private function getPriorityOrderSQL(): string
    {
        return "CASE priority
            WHEN 'Critical' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
            ELSE 5
        END";
    }

    /**
     * Check if status transition is allowed
     */
    private function isStatusTransitionAllowed(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return false;
        }

        return in_array($newStatus, self::ALLOWED_STATUS_TRANSITIONS[$currentStatus] ?? []);
    }

    /**
     * Get allowed actions for ticket
     */
    private function getAllowedActions($ticket): array
    {
        $actions = [];

        switch ($ticket->ticket_status) {
            case 'Assigned':
                $actions[] = 'start_work';
                break;

            case 'In Progress':
                $actions[] = 'add_note';
                $actions[] = 'set_pending';
                $actions[] = 'resolve';
                break;

            case 'Pending':
                $actions[] = 'add_note';
                $actions[] = 'resume_work';
                $actions[] = 'resolve';
                break;
        }

        return $actions;
    }

    /**
     * Get status color
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'Open' => 'primary',
            'Pending' => 'warning',
            'Approved' => 'info',
            'Assigned' => 'info',
            'In Progress' => 'primary',
            'Resolved' => 'success',
            'Closed' => 'secondary',
            'Rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get user-friendly status change title
     */
    private function getStatusChangeTitle(string $oldStatus, string $newStatus): string
    {
        return match ("{$oldStatus}->{$newStatus}") {
            'Assigned->In Progress' => 'Work Started',
            'In Progress->Resolved' => 'Ticket Resolved',
            'In Progress->Pending' => 'Work Paused',
            'Pending->In Progress' => 'Work Resumed',
            'Resolved->Closed' => 'Ticket Closed',
            default => 'Status Changed',
        };
    }

    /**
     * Format duration to human readable
     */
    private function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours < 24) {
            return $mins > 0 ? "{$hours} jam {$mins} menit" : "{$hours} jam";
        }

        $days = floor($hours / 24);
        $hrs = $hours % 24;

        return $hrs > 0 ? "{$days} hari {$hrs} jam" : "{$days} hari";
    }

    /**
     * Clear technician cache
     */
    private function clearTechnicianCache(int $technicianId): void
    {
        Cache::forget("technician_stats_{$technicianId}");
    }

    /**
     * Handle unauthorized access
     */
    private function handleUnauthorizedAccess(string $ticket_number)
    {
        Log::warning('Unauthorized ticket access attempt', [
            'technician_id' => auth()->id(),
            'ticket_number' => $ticket_number
        ]);

        return redirect()->route('technician.tickets.index')
            ->with('error', 'Tiket tidak ditemukan atau tidak di-assign ke Anda');
    }

    /**
     * Handle show error
     */
    private function handleShowError(string $ticket_number, \Exception $e)
    {
        Log::error('Technician Show Ticket Error', [
            'technician_id' => auth()->id(),
            'ticket_number' => $ticket_number,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->route('technician.tickets.index')
            ->with('error', 'Terjadi kesalahan saat memuat detail tiket');
    }
}
