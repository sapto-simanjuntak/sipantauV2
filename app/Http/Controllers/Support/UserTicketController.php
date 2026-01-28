<?php

namespace App\Http\Controllers\Support;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Modul\ServiceRequest;

class UserTicketController extends Controller
{
    /**
     * Display mobile-optimized ticket dashboard for regular users
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get filter dari request
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        // Build query
        $query = ServiceRequest::with([
            'hospitalUnit:id,unit_code,unit_name',
            'problemCategory:id,category_name,category_code',
            'problemSubCategory:id,sub_category_name',
        ])
            ->where('user_id', $user->id);

        // Apply status filter
        if ($status !== 'all') {
            $query->where('ticket_status', $status);
        }

        // Apply search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('issue_title', 'like', "%{$search}%");
            });
        }

        // Get tickets
        $tickets = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $stats = [
            'active' => ServiceRequest::where('user_id', $user->id)
                ->whereIn('ticket_status', ['Open', 'Pending', 'Approved', 'Assigned', 'In Progress'])
                ->count(),
            'open' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'Open')
                ->count(),
            'in_progress' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'In Progress')
                ->count(),
            'resolved' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'Resolved')
                ->count(),
            'total' => ServiceRequest::where('user_id', $user->id)->count(),
        ];

        // Get recent activity (last 5 tickets)
        $recentActivity = ServiceRequest::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get(['ticket_number', 'issue_title', 'ticket_status', 'updated_at']);

        return view('pages.modul.user-ticket.index', compact('tickets', 'stats', 'recentActivity', 'user', 'status', 'search'));
    }

    /**
     * AJAX endpoint untuk real-time data
     */
    public function getTickets(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status', 'all');

        $query = ServiceRequest::with([
            'hospitalUnit:id,unit_code,unit_name',
            'problemCategory:id,category_name,category_code',
            'problemSubCategory:id,sub_category_name',
        ])
            ->where('user_id', $user->id);

        if ($status !== 'all') {
            $query->where('ticket_status', $status);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        $user = auth()->user();

        $stats = [
            'active' => ServiceRequest::where('user_id', $user->id)
                ->whereIn('ticket_status', ['Open', 'Pending', 'Approved', 'Assigned', 'In Progress'])
                ->count(),
            'open' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'Open')
                ->count(),
            'pending' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'Pending')
                ->count(),
            'in_progress' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'In Progress')
                ->count(),
            'resolved' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'Resolved')
                ->count(),
            'closed' => ServiceRequest::where('user_id', $user->id)
                ->where('ticket_status', 'Closed')
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Search tickets
     */
    public function search(Request $request)
    {
        $user = auth()->user();
        $keyword = $request->get('q', '');

        $tickets = ServiceRequest::with([
            'hospitalUnit:id,unit_code,unit_name',
            'problemCategory:id,category_name,category_code',
        ])
            ->where('user_id', $user->id)
            ->where(function ($query) use ($keyword) {
                $query->where('ticket_number', 'like', "%{$keyword}%")
                    ->orWhere('issue_title', 'like', "%{$keyword}%")
                    ->orWhere('issue_description', 'like', "%{$keyword}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

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

            // ✅ Get technicians using Spatie role() method
            $technicians = User::role('teknisi') // ← Pakai role name yang lo set
                ->select('id', 'name', 'email')
                ->get();

            // Calculate SLA status
            $slaStatus = $this->calculateSLAStatus($ticket);

            // Get activity timeline
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

    /**
     * Get Ticket Timeline/Activity Log
     */
    private function getTicketTimeline($ticket)
    {
        $timeline = [];

        // Created
        $timeline[] = [
            'icon' => 'bx-plus-circle',
            'color' => 'primary',
            'title' => 'Ticket Created',
            'description' => 'Created by ' . $ticket->requester_name,
            'timestamp' => $ticket->created_at,
            'user' => $ticket->user
        ];

        // Validated
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

        // Assigned
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

        // Closed
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

        // Add logs
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

        // Sort by timestamp desc
        usort($timeline, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return $timeline;
    }
}
