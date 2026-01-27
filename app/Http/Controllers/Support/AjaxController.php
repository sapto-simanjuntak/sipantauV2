<?php

namespace App\Http\Controllers\Support;


use App\Models\Modul\HospitalUnit;
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
    public function getHospitalUnits()
    {
        $units = HospitalUnit::where('is_active', true)
            ->orderBy('unit_type')
            ->orderBy('unit_name')
            ->get(['id', 'unit_code', 'unit_name', 'unit_type']);

        return response()->json($units);
    }

    /**
     * Get Problem Categories
     */
    public function getProblemCategories()
    {
        $categories = ProblemCategory::orderBy('category_name')
            ->get(['id', 'category_name', 'category_code', 'requires_validation']);

        return response()->json($categories);
    }

    /**
     * Get Sub Categories by Category ID
     */
    public function getSubCategories($categoryId)
    {
        $subCategories = ProblemSubCategory::where('problem_category_id', $categoryId)
            ->orderBy('sub_category_name')
            ->get(['id', 'sub_category_name', 'problem_category_id']);

        return response()->json($subCategories);
    }

    /**
     * Get Hardware Types
     */
    public function getHardwareTypes()
    {
        $hardwareTypes = HardwareCategory::orderBy('hardware_name')
            ->get(['id', 'hardware_name']);

        return response()->json($hardwareTypes);
    }

    /**
     * Get Software Types
     */
    public function getSoftwareTypes()
    {
        $softwareTypes = SoftwareCategory::orderBy('software_name')
            ->get(['id', 'software_name']);

        return response()->json($softwareTypes);
    }

    /**
     * Get Ticket Statistics
     */
    public function getTicketStatistics()
    {
        $userId = session('user')['id'] ?? null;
        $userType = session('user')['type'] ?? null;

        $query = ServiceRequest::query();

        // Filter by user if not superadmin
        if ($userType !== 'superadmin') {
            $query->where('user_id', $userId);
        }

        $stats = [
            'open' => (clone $query)->whereIn('ticket_status', ['Open', 'Pending', 'Approved'])->count(),
            'in_progress' => (clone $query)->whereIn('ticket_status', ['Assigned', 'In Progress'])->count(),
            'resolved' => (clone $query)->where('ticket_status', 'Resolved')->count(),
            'critical' => (clone $query)->where('priority', 'Critical')->count(),
        ];

        return response()->json($stats);
    }
}
