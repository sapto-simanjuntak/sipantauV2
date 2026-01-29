<?php

namespace App\Http\Controllers\Support;


use App\Models\Modul\HospitalUnit;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Modul\ServiceRequest;
use App\Models\Master\ProblemCategory;
use App\Models\Master\HardwareCategory;
use App\Models\Master\SoftwareCategory;
use App\Models\Master\ProblemSubCategory;

class AjaxController extends Controller
{
    /**
     * Get Hospital Units
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getTicketStatistics()
    {
        Log::info('===== AjaxController::getTicketStatistics() CALLED =====');

        $user = auth()->user();

        Log::info('User Info', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'roles' => $user->getRoleNames()->toArray()
        ]);

        $statistics = [];

        if ($user->hasRole(['admin', 'superadmin', 'super admin'])) {

            $totalTickets = ServiceRequest::count();
            Log::info('Total tickets in database: ' . $totalTickets);

            $statistics = [
                'open' => ServiceRequest::where('ticket_status', 'Open')->count(),
                'pending' => ServiceRequest::where('ticket_status', 'Pending')->count(),
                'approved' => ServiceRequest::where('ticket_status', 'Approved')->count(),
                'assigned' => ServiceRequest::where('ticket_status', 'Assigned')->count(),
                'in_progress' => ServiceRequest::where('ticket_status', 'In Progress')->count(),
                'resolved' => ServiceRequest::where('ticket_status', 'Resolved')->count(),
                'closed' => ServiceRequest::where('ticket_status', 'Closed')->count(),
                'rejected' => ServiceRequest::where('ticket_status', 'Rejected')->count(),
                'critical' => ServiceRequest::where('priority', 'Critical')->count(),
                'high' => ServiceRequest::where('priority', 'High')->count(),
                'total' => $totalTickets,
            ];

            Log::info('Admin Statistics', $statistics);
        } elseif ($user->hasRole(['technician', 'teknisi'])) {
            $statistics = [
                'tech_assigned' => ServiceRequest::where('assigned_to', $user->id)
                    ->whereIn('ticket_status', ['Assigned', 'In Progress'])
                    ->count(),
                'tech_progress' => ServiceRequest::where('assigned_to', $user->id)
                    ->where('ticket_status', 'In Progress')
                    ->count(),
                'tech_resolved' => ServiceRequest::where('assigned_to', $user->id)
                    ->whereIn('ticket_status', ['Resolved', 'Closed'])
                    ->count(),
                'tech_overdue' => ServiceRequest::where('assigned_to', $user->id)
                    ->whereNotIn('ticket_status', ['Resolved', 'Closed', 'Rejected'])
                    ->where('sla_deadline', '<', now())
                    ->count(),
                'tech_total' => ServiceRequest::where('assigned_to', $user->id)->count(),
            ];

            Log::info('Technician Statistics', $statistics);
        } else {
            $statistics = [
                'user_open' => ServiceRequest::where('user_id', $user->id)
                    ->where('ticket_status', 'Open')
                    ->count(),
                'user_pending' => ServiceRequest::where('user_id', $user->id)
                    ->whereIn('ticket_status', ['Pending', 'Approved'])
                    ->count(),
                'user_progress' => ServiceRequest::where('user_id', $user->id)
                    ->whereIn('ticket_status', ['Assigned', 'In Progress'])
                    ->count(),
                'user_resolved' => ServiceRequest::where('user_id', $user->id)
                    ->where('ticket_status', 'Resolved')
                    ->count(),
                'user_closed' => ServiceRequest::where('user_id', $user->id)
                    ->where('ticket_status', 'Closed')
                    ->count(),
                'user_total' => ServiceRequest::where('user_id', $user->id)->count(),
            ];

            Log::info('User Statistics', $statistics);
        }

        return response()->json($statistics);
    }

    /**
     * Get all hospital units
     */
    public function getHospitalUnits()
    {
        $units = HospitalUnit::select('id', 'unit_code', 'unit_name')
            ->orderBy('unit_name')
            ->get();

        return response()->json($units);
    }

    /**
     * Get all problem categories
     */
    public function getProblemCategories()
    {
        $categories = ProblemCategory::select('id', 'category_code', 'category_name')
            ->orderBy('category_name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Get sub categories by category ID
     */
    public function getSubCategories($categoryId)
    {
        $subCategories = ProblemSubCategory::where('category_id', $categoryId)
            ->select('id', 'sub_category_name')
            ->orderBy('sub_category_name')
            ->get();

        return response()->json($subCategories);
    }


    // public function getHospitalUnits()
    // {
    //     $units = HospitalUnit::where('is_active', true)
    //         ->orderBy('unit_type')
    //         ->orderBy('unit_name')
    //         ->get(['id', 'unit_code', 'unit_name', 'unit_type']);

    //     return response()->json($units);
    // }

    /**
     * Get Problem Categories
     */
    // public function getProblemCategories()
    // {
    //     $categories = ProblemCategory::orderBy('category_name')
    //         ->get(['id', 'category_name', 'category_code', 'requires_validation']);

    //     return response()->json($categories);
    // }

    /**
 * Get Sub Categories by Category ID
 */
    // public function getSubCategories($categoryId)
    // {
    //     $subCategories = ProblemSubCategory::where('problem_category_id', $categoryId)
    //         ->orderBy('sub_category_name')
    //         ->get(['id', 'sub_category_name', 'problem_category_id']);

    //     return response()->json($subCategories);
    // }

    /**
 * Get Hardware Types
 */
    // public function getHardwareTypes()
    // {
    //     $hardwareTypes = Hardware::orderBy('hardware_name')
    //         ->get(['id', 'hardware_name']);

    //     return response()->json($hardwareTypes);
    // }

    /**
 * Get Software Types
 */
    // public function getSoftwareTypes()
    // {
    //     $softwareTypes = SoftwareCategory::orderBy('software_name')
    //         ->get(['id', 'software_name']);

    //     return response()->json($softwareTypes);
    // }

    /**
 * Get Ticket Statistics
 */
    // public function getTicketStatistics()
    // {
    //     $userId = session('user')['id'] ?? null;
    //     $userType = session('user')['type'] ?? null;

    //     $query = ServiceRequest::query();

    //     // Filter by user if not superadmin
    //     if ($userType !== 'superadmin') {
    //         $query->where('user_id', $userId);
    //     }

    //     $stats = [
    //         'open' => (clone $query)->whereIn('ticket_status', ['Open', 'Pending', 'Approved'])->count(),
    //         'in_progress' => (clone $query)->whereIn('ticket_status', ['Assigned', 'In Progress'])->count(),
    //         'resolved' => (clone $query)->where('ticket_status', 'Resolved')->count(),
    //         'critical' => (clone $query)->where('priority', 'Critical')->count(),
    //     ];

    //     return response()->json($stats);
    // }
}
