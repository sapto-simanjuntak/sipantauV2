<?php

namespace App\Http\Controllers\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Modul\ServiceRequest;
use Illuminate\Support\Facades\Cache;

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
}
