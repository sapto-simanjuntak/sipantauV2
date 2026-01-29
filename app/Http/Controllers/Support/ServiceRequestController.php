<?php

namespace App\Http\Controllers\Support;

use App\Models\User;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QROptions;
use App\Models\Modul\HospitalUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Modul\ServiceRequest;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Encoding\Encoding;

use App\Models\Master\ProblemCategory;
use Endroid\QrCode\RoundBlockSizeMode;
use App\Models\Modul\ServiceRequestLog;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\ErrorCorrectionLevel;
use Yajra\DataTables\Facades\DataTables;


class ServiceRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        $role = $role ? strtolower($role) : 'user';

        Log::info('User Role Debug', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $role,
            'all_roles' => $user->getRoleNames()->toArray(),
        ]);

        if (request()->ajax()) {
            try {
                $query = ServiceRequest::with([
                    'user:id,name,email',
                    'hospitalUnit:id,unit_code,unit_name,unit_type',
                    'problemCategory:id,category_name,category_code',
                    'problemSubCategory:id,sub_category_name',
                    'assignedTechnician:id,name', // Relationship name tetap
                ]);

                // ✅ FILTER BERDASARKAN ROLE
                if ($user->hasRole(['admin', 'superadmin', 'super admin'])) {
                    // Admin & Superadmin bisa lihat semua tiket

                } elseif ($user->hasRole(['technician', 'teknisi'])) {
                    // ✅ FIX: Ganti ke 'assigned_to'
                    $query->where('assigned_to', $user->id);
                } elseif ($user->hasRole(['user', 'pegawai', 'staff'])) {
                    $query->where('user_id', $user->id);
                } else {
                    Log::warning('User without recognized role, defaulting to user view', [
                        'user_id' => $user->id,
                        'roles' => $user->getRoleNames()->toArray()
                    ]);
                    $query->where('user_id', $user->id);
                    $role = 'user';
                }

                // ✅ APPLY FILTERS
                if ($user->hasAnyRole(['admin', 'superadmin', 'super admin', 'technician', 'teknisi'])) {
                    if (request()->filled('filter_unit')) {
                        $query->where('unit_id', request('filter_unit'));
                    }
                    if (request()->filled('filter_category')) {
                        $query->where('problem_category_id', request('filter_category'));
                    }
                    if (request()->filled('filter_status')) {
                        $query->where('ticket_status', request('filter_status'));
                    }
                    if (request()->filled('filter_priority')) {
                        $query->where('priority', request('filter_priority'));
                    }
                }

                return DataTables::of($query)
                    ->addIndexColumn()

                    ->addColumn('ticket_number', function ($pro) {
                        $html = '<div class="d-flex flex-column">';
                        $html .= '<a href="' . route('service.show', ['ticket_number' => $pro->ticket_number]) . '" class="fw-bold text-primary text-decoration-none">' .
                            htmlspecialchars($pro->ticket_number, ENT_QUOTES, 'UTF-8') . '</a>';
                        $html .= '<small class="text-muted"><i class="bx bx-time-five me-1"></i>' .
                            $pro->created_at->format('d M Y, H:i') . '</small>';
                        $html .= '</div>';
                        return $html;
                    })

                    ->addColumn('requester', function ($pro) use ($user) {
                        if ($user->hasRole(['user', 'pegawai', 'staff'])) {
                            return null;
                        }

                        $html = '<div class="d-flex flex-column" style="min-width: 180px;">';
                        $html .= '<span class="fw-semibold">' . htmlspecialchars($pro->requester_name, ENT_QUOTES, 'UTF-8') . '</span>';

                        if ($pro->requester_phone) {
                            $html .= '<small class="text-muted"><i class="bx bx-phone me-1"></i>' .
                                htmlspecialchars($pro->requester_phone, ENT_QUOTES, 'UTF-8') . '</small>';
                        }

                        if ($pro->user) {
                            $html .= '<small class="text-muted text-truncate" style="max-width: 180px;" title="' .
                                htmlspecialchars($pro->user->email, ENT_QUOTES, 'UTF-8') . '"><i class="bx bx-envelope me-1"></i>' .
                                htmlspecialchars($pro->user->email, ENT_QUOTES, 'UTF-8') . '</small>';
                        }

                        $html .= '</div>';
                        return $html;
                    })

                    ->addColumn('unit', function ($pro) {
                        if (!$pro->hospitalUnit) {
                            return '<span class="text-muted">-</span>';
                        }

                        $html = '<div class="d-flex flex-column" style="min-width: 150px;">';
                        $html .= '<span class="badge bg-secondary mb-1">' .
                            htmlspecialchars($pro->hospitalUnit->unit_code, ENT_QUOTES, 'UTF-8') . '</span>';
                        $html .= '<small class="fw-semibold">' .
                            htmlspecialchars($pro->hospitalUnit->unit_name, ENT_QUOTES, 'UTF-8') . '</small>';

                        if ($pro->location) {
                            $html .= '<small class="text-muted"><i class="bx bx-map me-1"></i>' .
                                htmlspecialchars($pro->location, ENT_QUOTES, 'UTF-8') . '</small>';
                        }

                        $html .= '</div>';
                        return $html;
                    })

                    ->addColumn('issue_title', function ($pro) {
                        $maxLength = 50;
                        $title = htmlspecialchars($pro->issue_title, ENT_QUOTES, 'UTF-8');

                        if (strlen($title) > $maxLength) {
                            $truncated = substr($title, 0, $maxLength) . '...';
                            return '<span class="d-inline-block text-truncate" style="max-width: 250px;" title="' . $title . '">' .
                                $truncated . '</span>';
                        }

                        return '<span>' . $title . '</span>';
                    })

                    ->addColumn('category', function ($pro) {
                        if (!$pro->problemCategory) {
                            return '<span class="text-muted">-</span>';
                        }

                        $categoryBadges = [
                            'HW' => 'primary',
                            'SW' => 'info',
                            'NET' => 'warning',
                            'PABX' => 'secondary',
                            'CCTV' => 'dark',
                            'AC' => 'success',
                            'DEV' => 'danger',
                            'EMAIL' => 'primary'
                        ];

                        $badgeClass = $categoryBadges[$pro->problemCategory->category_code] ?? 'secondary';

                        $html = '<div class="d-flex flex-column gap-1">';
                        $html .= '<span class="badge bg-' . $badgeClass . '">' .
                            htmlspecialchars($pro->problemCategory->category_name, ENT_QUOTES, 'UTF-8') . '</span>';

                        if ($pro->problemSubCategory) {
                            $html .= '<small class="badge bg-light text-dark">' .
                                htmlspecialchars($pro->problemSubCategory->sub_category_name, ENT_QUOTES, 'UTF-8') . '</small>';
                        }

                        $html .= '</div>';
                        return $html;
                    })

                    ->addColumn('device_info', function ($pro) {
                        $html = '<div class="d-flex flex-column" style="min-width: 150px;">';

                        if ($pro->device_affected) {
                            $html .= '<small class="fw-semibold"><i class="bx bx-laptop me-1"></i>' .
                                htmlspecialchars($pro->device_affected, ENT_QUOTES, 'UTF-8') . '</small>';
                        }

                        if ($pro->ip_address) {
                            $html .= '<small class="text-muted"><i class="bx bx-network-chart me-1"></i>' .
                                htmlspecialchars($pro->ip_address, ENT_QUOTES, 'UTF-8') . '</small>';
                        }

                        if ($pro->connection_status) {
                            $statusColor = (stripos($pro->connection_status, 'online') !== false ||
                                stripos($pro->connection_status, 'connected') !== false)
                                ? 'success' : 'danger';
                            $html .= '<small class="badge bg-' . $statusColor . ' mt-1">' .
                                htmlspecialchars($pro->connection_status, ENT_QUOTES, 'UTF-8') . '</small>';
                        }

                        if (!$pro->device_affected && !$pro->ip_address && !$pro->connection_status) {
                            $html .= '<span class="text-muted">-</span>';
                        }

                        $html .= '</div>';
                        return $html;
                    })

                    ->addColumn('severity_level', function ($pro) {
                        $severityColors = [
                            'Kritis' => 'danger',
                            'Tinggi' => 'warning',
                            'Sedang' => 'info',
                            'Rendah' => 'secondary'
                        ];

                        $color = $severityColors[$pro->severity_level] ?? 'secondary';

                        $html = '<span class="badge bg-' . $color . '">' . htmlspecialchars($pro->severity_level) . '</span>';

                        if ($pro->impact_patient_care) {
                            $html .= '<br><small class="badge bg-danger mt-1 impact-patient">' .
                                '<i class="bx bx-error-circle me-1"></i>Patient Impact</small>';
                        }

                        return $html;
                    })

                    ->addColumn('priority', function ($pro) {
                        $priorityColors = [
                            'Critical' => 'danger',
                            'High' => 'warning',
                            'Medium' => 'info',
                            'Low' => 'secondary'
                        ];

                        $color = $priorityColors[$pro->priority] ?? 'secondary';
                        return '<span class="badge bg-' . $color . '">' . htmlspecialchars($pro->priority) . '</span>';
                    })

                    ->addColumn('status', function ($pro) {
                        $statusColors = [
                            'Open' => 'primary',
                            'Pending' => 'warning',
                            'Approved' => 'info',
                            'Assigned' => 'secondary',
                            'In Progress' => 'primary',
                            'Resolved' => 'success',
                            'Closed' => 'dark',
                            'Rejected' => 'danger'
                        ];

                        $color = $statusColors[$pro->ticket_status] ?? 'secondary';

                        $html = '<span class="badge bg-' . $color . '">' . htmlspecialchars($pro->ticket_status) . '</span>';

                        if ($pro->assignedTechnician) {
                            $html .= '<br><small class="text-muted mt-1">' .
                                htmlspecialchars($pro->assignedTechnician->name, ENT_QUOTES, 'UTF-8') .
                                '</small>';
                        }

                        return $html;
                    })

                    ->addColumn('sla', function ($pro) {
                        if (!$pro->sla_deadline) {
                            return '<span class="text-muted">-</span>';
                        }

                        $now = now();
                        $deadline = \Carbon\Carbon::parse($pro->sla_deadline);
                        $hoursRemaining = $now->diffInHours($deadline, false);

                        if ($hoursRemaining < 0) {
                            $slaClass = 'danger sla-warning';
                            $slaIcon = 'bx-error-circle';
                            $slaText = 'Overdue ' . abs(round($hoursRemaining)) . 'h';
                        } elseif ($hoursRemaining <= 4) {
                            $slaClass = 'warning sla-warning';
                            $slaIcon = 'bx-time-five';
                            $slaText = round($hoursRemaining) . 'h left';
                        } else {
                            $slaClass = 'success';
                            $slaIcon = 'bx-check-circle';
                            $slaText = round($hoursRemaining) . 'h left';
                        }

                        $html = '<div class="d-flex flex-column">';
                        $html .= '<span class="badge bg-' . $slaClass . '">';
                        $html .= '<i class="bx ' . $slaIcon . ' me-1"></i>' . $slaText;
                        $html .= '</span>';
                        $html .= '<small class="text-muted mt-1">' . $deadline->format('d M, H:i') . '</small>';
                        $html .= '</div>';

                        return $html;
                    })

                    ->addColumn('occurrence_time', function ($pro) {
                        if (!$pro->occurrence_time) {
                            return '<span class="text-muted">-</span>';
                        }

                        $time = \Carbon\Carbon::parse($pro->occurrence_time);
                        return '<div class="d-flex flex-column">' .
                            '<small class="fw-semibold">' . $time->format('d M Y') . '</small>' .
                            '<small class="text-muted">' . $time->format('H:i') . '</small>' .
                            '</div>';
                    })

                    ->addColumn('action', function ($pro) use ($user) {
                        return $this->getActionButtons($pro, $user);
                    })

                    ->rawColumns([
                        'ticket_number',
                        'requester',
                        'unit',
                        'issue_title',
                        'category',
                        'device_info',
                        'severity_level',
                        'priority',
                        'status',
                        'sla',
                        'occurrence_time',
                        'action'
                    ])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('DataTable Error: ' . $e->getMessage());
                Log::error('Stack Trace: ' . $e->getTraceAsString());

                return response()->json([
                    'error' => 'Error loading data: ' . $e->getMessage()
                ], 500);
            }
        }

        return view('pages.modul.service-request.index', [
            'role' => $role,
            'user' => $user
        ]);
    }



    private function getActionButtons($ticket, $user)
    {
        $html = '<div class="btn-group" role="group">';

        // ✅ View button (semua role)
        $html .= '<a href="' . route('service.show', $ticket->ticket_number) . '"
              class="btn btn-sm btn-info" title="Detail">
              <i class="bx bx-show"></i>
              </a>';

        // ✅ Edit button (conditional)
        $canEdit = false;
        if ($user->hasAnyRole(['admin', 'superadmin'])) {
            $canEdit = true;
        } elseif ($user->hasRole('teknisi') && $ticket->assigned_to == $user->id) {
            $canEdit = true;
        } elseif ($ticket->user_id == $user->id && $ticket->ticket_status == 'Open') {
            $canEdit = true;
        }

        if ($canEdit) {
            $html .= '<a href="' . route('service.edit', $ticket->ticket_number) . '"
                  class="btn btn-sm btn-warning" title="Edit">
                  <i class="bx bx-edit"></i>
                  </a>';
        }

        // ✅ Delete button (admin only, status Open)
        if ($user->hasAnyRole(['admin', 'superadmin']) && $ticket->ticket_status == 'Open') {
            $html .= '<button class="btn btn-sm btn-danger delete"
                data-ticket="' . e($ticket->ticket_number) . '"
                data-url="' . route('service.destroy', $ticket->ticket_number) . '"
                title="Hapus">
                <i class="bx bx-trash"></i>
            </button>';
        }



        $html .= '</div>';
        return $html;
    }

    public function create()
    {
        return view('pages.modul.service-request.create');
    }

    public function store(Request $request)
    {
        // ✅ Base validation (fields yang SELALU required)
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
            'occurrence_time' => 'nullable|date',           // ✅ UBAH: required → nullable
            'device_affected' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',        // ✅ UBAH: required → nullable
            'expected_action' => 'required|string',
            'file_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];

        // ✅ Get category untuk check conditional validation
        $category = ProblemCategory::find($request->problem_category_id);

        // ✅ Conditional validation based on category
        if ($category && $category->category_code === 'NET') {
            // Hanya validate network fields kalau kategori = Network
            $rules['ip_address'] = 'nullable|ip';
            $rules['connection_status'] = 'nullable|string';
        }

        // ✅ TAMBAHAN: Conditional validation untuk kategori NON-DEV
        if ($category && $category->category_code !== 'DEV') {
            // Kalau BUKAN kategori DEV, maka location dan occurrence_time wajib
            $rules['location'] = 'required|string|max:255';
            $rules['occurrence_time'] = 'required|date';
        }

        // Validate
        $validated = $request->validate($rules);

        // ✅ Clean up: Remove NULL/empty values
        $validated = array_filter($validated, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Generate ticket number
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tickets', $filename, 'public');
            $validated['file_path'] = $filePath;
        }

        // Add system fields
        $validated['user_id'] = Auth::user()->id;
        $validated['ticket_number'] = $ticketNumber;
        $validated['ticket_status'] = 'Open';
        $validated['validation_status'] = 'pending';

        // Auto-calculate priority
        $unit = HospitalUnit::find($validated['unit_id']);
        $validated['priority'] = $this->calculatePriority(
            $validated['severity_level'],
            $unit->unit_type
        );

        // Calculate SLA deadline
        $validated['sla_deadline'] = now()->addHours($category->default_sla_hours);

        // ✅ TAMBAHAN: Set default occurrence_time jika kosong (untuk DEV)
        if (!isset($validated['occurrence_time'])) {
            $validated['occurrence_time'] = now();
        }

        // ✅ TAMBAHAN: Set default location jika kosong (untuk DEV)
        if (!isset($validated['location'])) {
            $validated['location'] = '-'; // atau bisa dikosongkan sesuai kebutuhan
        }

        // Create ticket
        $ticket = ServiceRequest::create($validated);

        // ============================================
        // ROLE-BASED REDIRECT
        // ============================================
        $user = Auth::user();
        $redirectUrl = route('service.index'); // Default untuk admin/superadmin/teknisi

        // Check if user role is 'user' (bukan admin/teknisi)
        if ($user->hasRole('user') && !$user->hasAnyRole(['superadmin', 'admin', 'teknisi'])) {
            $redirectUrl = route('ticket.index'); // Redirect ke user ticket page
        }

        // Log for debugging
        Log::info('Ticket Created - Redirect Info', [
            'ticket_number' => $ticketNumber,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'redirect_url' => $redirectUrl
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat',
            'ticket_number' => $ticketNumber,
            'redirect_url' => $redirectUrl
        ]);
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

        return view('pages.modul.service-request.edit', compact('ticket'));
    }

    public function update(Request $request, $ticket_number)
    {
        $ticket = ServiceRequest::where('ticket_number', $ticket_number)->firstOrFail();

        // Authorization check (sama seperti edit)
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

        // ✅ Base validation
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
            'occurrence_time' => 'nullable|date',           // ✅ UBAH: required → nullable
            'device_affected' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',        // ✅ UBAH: required → nullable

            'expected_action' => 'required|string',
            'file_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];

        // ✅ Get category untuk conditional validation
        $category = ProblemCategory::find($request->problem_category_id);

        // ✅ Conditional validation for Network category
        if ($category && $category->category_code === 'NET') {
            $rules['ip_address'] = 'nullable|ip';
            $rules['connection_status'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // ✅ Clean up NULL/empty values
        $validated = array_filter($validated, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Handle file upload (jika ada file baru)
        if ($request->hasFile('file_path')) {
            // Delete old file if exists
            if ($ticket->file_path && Storage::disk('public')->exists($ticket->file_path)) {
                Storage::disk('public')->delete($ticket->file_path);
            }

            $file = $request->file('file_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tickets', $filename, 'public');
            $validated['file_path'] = $filePath;
        }

        // Recalculate priority if severity or unit changed
        $unit = HospitalUnit::find($validated['unit_id']);
        $validated['priority'] = $this->calculatePriority(
            $validated['severity_level'],
            $unit->unit_type
        );

        // Recalculate SLA if category changed
        if ($ticket->problem_category_id != $validated['problem_category_id']) {
            $validated['sla_deadline'] = now()->addHours($category->default_sla_hours);
        }

        // Update ticket
        $ticket->update($validated);

        // ✅ Role-based redirect (sama seperti store)
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

    private function calculatePriority($severity, $unitType)
    {
        if (in_array($unitType, ['Critical']) && in_array($severity, ['Tinggi', 'Kritis'])) {
            return 'Critical';
        }

        if ($severity === 'Kritis') return 'Critical';
        if ($severity === 'Tinggi') return 'High';
        if ($severity === 'Sedang') return 'Medium';

        return 'Low';
    }

    // ============================================
    // GET PROBLEM CATEGORIES
    // ============================================
    public function getProblemCategories()
    {
        try {
            $categories = DB::table('problem_category')
                ->select('id', 'problem_name')
                ->get();

            Log::info('Categories loaded:', ['count' => $categories->count()]);

            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Error loading categories: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ============================================
    // GET SUB-CATEGORIES BY CATEGORY ID
    // ============================================
    public function getSubCategories($categoryId)
    {
        try {
            $subCategories = DB::table('problem_sub_category')
                ->where('problem_category_id', $categoryId)
                ->select('id', 'problem_category_id', 'sub_category_name', 'input_type')
                ->get();

            Log::info('Sub-categories loaded:', [
                'category_id' => $categoryId,
                'count' => $subCategories->count()
            ]);

            return response()->json($subCategories);
        } catch (\Exception $e) {
            Log::error('Error loading sub-categories: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ============================================
    // GET HARDWARE TYPES
    // ============================================
    public function getHardwareType()
    {
        try {
            $hardwareTypes = DB::table('hardware_type')
                ->select('id', 'hardware_name')
                ->get();

            return response()->json($hardwareTypes);
        } catch (\Exception $e) {
            Log::error('Error loading hardware types: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ============================================
    // GET SOFTWARE TYPES
    // ============================================
    public function getSoftwareType()
    {
        try {
            $softwareTypes = DB::table('software_type')
                ->select('id', 'software_name')
                ->get();

            return response()->json($softwareTypes);
        } catch (\Exception $e) {
            Log::error('Error loading software types: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

            return view('pages.modul.service-request.show', compact(
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

    /**
     * Calculate SLA Status
     */
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

    /**
     * Approve Ticket (AJAX)
     */
    public function approve(Request $request, $ticket_number)
    {
        try {
            $request->validate([
                'validation_notes' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            $ticket = ServiceRequest::where('ticket_number', $ticket_number)->firstOrFail();

            // Check if already validated
            if ($ticket->validation_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah di-validasi sebelumnya'
                ], 400);
            }

            $ticket->update([
                'validation_status' => 'approved',
                'validation_notes' => $request->validation_notes,
                'validated_at' => now(),
                'validated_by' => auth()->id(),
                'ticket_status' => 'Approved'
            ]);

            // Log activity
            ServiceRequestLog::create([
                'service_request_id' => $ticket->id,
                'user_id' => auth()->id(),
                'action_type' => 'Approved',
                'notes' => $request->validation_notes ?? 'Ticket approved',
                'old_status' => 'Pending',
                'new_status' => 'Approved'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil disetujui',
                'ticket' => $ticket
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve Ticket Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Reject Ticket (AJAX)
     */
    public function reject(Request $request, $ticket_number)
    {
        try {
            $request->validate([
                'validation_notes' => 'required|string|max:1000'
            ]);

            DB::beginTransaction();

            $ticket = ServiceRequest::where('ticket_number', $ticket_number)->firstOrFail();

            // Check if already validated
            if ($ticket->validation_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah di-validasi sebelumnya'
                ], 400);
            }

            $ticket->update([
                'validation_status' => 'rejected',
                'validation_notes' => $request->validation_notes,
                'validated_at' => now(),
                'validated_by' => auth()->id(),
                'ticket_status' => 'Rejected'
            ]);

            // Log activity
            ServiceRequestLog::create([
                'service_request_id' => $ticket->id,
                'user_id' => auth()->id(),
                'action_type' => 'Rejected',
                'notes' => $request->validation_notes,
                'old_status' => 'Pending',
                'new_status' => 'Rejected'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil ditolak',
                'ticket' => $ticket
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject Ticket Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Assign Ticket to Technician (AJAX)
     */
    public function assign(Request $request, $ticket_number)
    {
        try {
            $request->validate([
                'assigned_to' => 'required|exists:users,id',
                'notes' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            $ticket = ServiceRequest::where('ticket_number', $ticket_number)->firstOrFail();

            // Check if can be assigned
            if (!in_array($ticket->ticket_status, ['Approved', 'Pending', 'Open'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak dapat di-assign pada status ini'
                ], 400);
            }

            $oldStatus = $ticket->ticket_status;

            $ticket->update([
                'assigned_to' => $request->assigned_to,
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
                'ticket_status' => 'Assigned'
            ]);

            // Get technician info
            $technician = User::find($request->assigned_to);

            // Log activity
            ServiceRequestLog::create([
                'service_request_id' => $ticket->id,
                'user_id' => auth()->id(),
                'action_type' => 'Assigned',
                'notes' => $request->notes ?? 'Assigned to ' . $technician->name . ' ' . $technician->last_name,
                'old_status' => $oldStatus,
                'new_status' => 'Assigned'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil di-assign ke ' . $technician->name,
                'ticket' => $ticket
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assign Ticket Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update Ticket Status (AJAX)
     */
    public function updateStatus(Request $request, $ticket_number)
    {
        try {
            $request->validate([
                'status' => 'required|in:Open,Pending,Approved,Assigned,In Progress,Resolved,Closed,Rejected',
                'notes' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            $ticket = ServiceRequest::where('ticket_number', $ticket_number)->firstOrFail();
            $oldStatus = $ticket->ticket_status;

            $ticket->update([
                'ticket_status' => $request->status
            ]);

            // If closing ticket
            if ($request->status === 'Closed') {
                $ticket->update([
                    'closed_at' => now(),
                    'closed_by' => auth()->id(),
                    'closure_notes' => $request->notes
                ]);
            }

            // Log activity
            ServiceRequestLog::create([
                'service_request_id' => $ticket->id,
                'user_id' => auth()->id(),
                'action_type' => 'Status Updated',
                'notes' => $request->notes ?? 'Status changed from ' . $oldStatus . ' to ' . $request->status,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status tiket berhasil diupdate',
                'ticket' => $ticket
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Status Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printTicket($ticketNumber)
    {
        $ticket = ServiceRequest::with([
            'user',
            'hospitalUnit',
            'problemCategory',
            'problemSubCategory',
            'validator',
            'assignedTechnician',
            'assignedBy',
            'closedBy'
        ])->where('ticket_number', $ticketNumber)->firstOrFail();

        // Generate QR Codes untuk setiap role
        $qrCodes = $this->generateQrCodes($ticket);

        // Hitung SLA
        $slaStatus = $this->calculateSLA($ticket);

        // Timeline
        $timeline = $this->buildTimeline($ticket);

        $pdf = Pdf::loadView('pages.modul.service-request.print', [
            'ticket' => $ticket,
            'qrCodes' => $qrCodes,
            'slaStatus' => $slaStatus,
            'timeline' => $timeline,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("Ticket-{$ticketNumber}.pdf");
    }

    private function generateQrCodes($ticket)
    {
        $qrCodes = [];

        try {
            // ============================================
            // 1. QR CODE REQUESTER (SELALU ADA)
            // ============================================

            // ✅ FIX: Convert timestamp ke string yang konsisten
            $requesterTimestamp = $ticket->created_at->format('Y-m-d H:i:s');

            $requesterData = [
                'ticket_number' => $ticket->ticket_number,
                'document_type' => 'service_request_signature',
                'role' => 'requester',
                'name' => $ticket->requester_name,
                'user_id' => $ticket->user_id,
                'action' => 'created',
                'timestamp' => $requesterTimestamp,
                'verification_code' => hash('sha256', 'requester-' . $ticket->ticket_number . $ticket->user_id . $requesterTimestamp),
            ];

            $encodedRequester = base64_encode(json_encode($requesterData));
            $requesterVerifyUrl = route('verify.qr', ['data' => $encodedRequester]);
            $qrCodes['requester'] = $this->createQrCode($requesterVerifyUrl);

            // ============================================
            // 2. QR CODE VALIDATOR
            // ============================================
            if ($ticket->validated_by && $ticket->validated_at) {

                // ✅ FIX: Convert timestamp
                $validatorTimestamp = $ticket->validated_at->format('Y-m-d H:i:s');

                $validatorData = [
                    'ticket_number' => $ticket->ticket_number,
                    'document_type' => 'service_request_signature',
                    'role' => 'validator',
                    'name' => optional($ticket->validator)->name,
                    'user_id' => $ticket->validated_by,
                    'action' => 'validated',
                    'status' => $ticket->validation_status,
                    'timestamp' => $validatorTimestamp,
                    'verification_code' => hash('sha256', 'validator-' . $ticket->ticket_number . $ticket->validated_by . $validatorTimestamp),
                ];

                $encodedValidator = base64_encode(json_encode($validatorData));
                $validatorVerifyUrl = route('verify.qr', ['data' => $encodedValidator]);
                $qrCodes['validator'] = $this->createQrCode($validatorVerifyUrl);
            } else {
                // Placeholder
                $placeholderData = [
                    'ticket_number' => $ticket->ticket_number,
                    'document_type' => 'service_request_signature',
                    'role' => 'validator',
                    'status' => 'pending_validation',
                    'message' => 'Awaiting admin validation',
                    'created_at' => $ticket->created_at->format('Y-m-d H:i:s')
                ];

                $encodedPlaceholder = base64_encode(json_encode($placeholderData));
                $placeholderVerifyUrl = route('verify.qr', ['data' => $encodedPlaceholder]);
                $qrCodes['validator'] = $this->createQrCode($placeholderVerifyUrl, 'L');
            }

            // ============================================
            // 3. QR CODE TECHNICIAN
            // ============================================
            if ($ticket->assigned_to) {
                if (in_array($ticket->ticket_status, ['Resolved', 'Closed'])) {

                    $resolvedAt = $ticket->closed_at ?? $ticket->updated_at;

                    // ✅ FIX: Convert timestamp
                    $technicianTimestamp = $resolvedAt->format('Y-m-d H:i:s');

                    $technicianData = [
                        'ticket_number' => $ticket->ticket_number,
                        'document_type' => 'service_request_signature',
                        'role' => 'technician',
                        'name' => optional($ticket->assignedTechnician)->name,
                        'user_id' => $ticket->assigned_to,
                        'action' => 'resolved',
                        'status' => $ticket->ticket_status,
                        'assigned_at' => $ticket->assigned_at ? $ticket->assigned_at->format('Y-m-d H:i:s') : null,
                        'resolved_at' => $technicianTimestamp,
                        'verification_code' => hash('sha256', 'technician-' . $ticket->ticket_number . $ticket->assigned_to . $technicianTimestamp),
                    ];

                    $encodedTechnician = base64_encode(json_encode($technicianData));
                    $technicianVerifyUrl = route('verify.qr', ['data' => $encodedTechnician]);
                    $qrCodes['technician'] = $this->createQrCode($technicianVerifyUrl);
                } else {
                    // In progress
                    $placeholderData = [
                        'ticket_number' => $ticket->ticket_number,
                        'document_type' => 'service_request_signature',
                        'role' => 'technician',
                        'name' => optional($ticket->assignedTechnician)->name,
                        'status' => 'in_progress',
                        'current_status' => $ticket->ticket_status,
                        'assigned_at' => $ticket->assigned_at ? $ticket->assigned_at->format('Y-m-d H:i:s') : null,
                        'message' => 'Work in progress by technician'
                    ];

                    $encodedProgress = base64_encode(json_encode($placeholderData));
                    $progressVerifyUrl = route('verify.qr', ['data' => $encodedProgress]);
                    $qrCodes['technician'] = $this->createQrCode($progressVerifyUrl, 'L');
                }
            } else {
                // Unassigned
                $placeholderData = [
                    'ticket_number' => $ticket->ticket_number,
                    'document_type' => 'service_request_signature',
                    'role' => 'technician',
                    'status' => 'unassigned',
                    'message' => 'Awaiting technician assignment',
                    'created_at' => $ticket->created_at->format('Y-m-d H:i:s')
                ];

                $encodedUnassigned = base64_encode(json_encode($placeholderData));
                $unassignedVerifyUrl = route('verify.qr', ['data' => $encodedUnassigned]);
                $qrCodes['technician'] = $this->createQrCode($unassignedVerifyUrl, 'L');
            }
        } catch (\Exception $e) {
            Log::error('QR Code Generation Failed: ' . $e->getMessage());
            $qrCodes = [
                'requester' => null,
                'validator' => null,
                'technician' => null
            ];
        }

        return $qrCodes;
    }

    /**
     * Helper method untuk create QR code
     */
    private function createQrCode($data, $errorCorrection = 'H')
    {
        try {
            // Set error correction level
            $ecLevel = match ($errorCorrection) {
                'L' => ErrorCorrectionLevel::Low,
                'M' => ErrorCorrectionLevel::Medium,
                'Q' => ErrorCorrectionLevel::Quartile,
                'H' => ErrorCorrectionLevel::High,
                default => ErrorCorrectionLevel::High
            };

            // Create QR code
            $qrCode = QrCode::create($data)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel($ecLevel)
                ->setSize(300)
                ->setMargin(10)
                ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin);

            // Write to PNG
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Return base64
            return base64_encode($result->getString());
        } catch (\Exception $e) {
            Log::error('Individual QR Code Creation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate SLA Status
     */
    private function calculateSLA($ticket)
    {
        if (!$ticket->sla_deadline) {
            return [
                'class' => 'secondary',
                'message' => 'No SLA Set'
            ];
        }

        $now = now();
        $deadline = \Carbon\Carbon::parse($ticket->sla_deadline);

        if ($ticket->ticket_status === 'Closed' || $ticket->ticket_status === 'Resolved') {
            $closedAt = $ticket->closed_at ?? $ticket->updated_at;
            if ($closedAt <= $deadline) {
                return [
                    'class' => 'success',
                    'message' => 'Met SLA'
                ];
            } else {
                return [
                    'class' => 'danger',
                    'message' => 'SLA Breached'
                ];
            }
        }

        $diff = $now->diffInHours($deadline, false);

        if ($diff < 0) {
            return [
                'class' => 'danger',
                'message' => 'Overdue'
            ];
        } elseif ($diff < 2) {
            return [
                'class' => 'warning',
                'message' => 'Critical - ' . abs($diff) . 'h remaining'
            ];
        } else {
            return [
                'class' => 'info',
                'message' => abs($diff) . 'h remaining'
            ];
        }
    }

    /**
     * Build Timeline dari logs
     */
    private function buildTimeline($ticket)
    {
        $timeline = collect();

        // Ticket Created
        $timeline->push([
            'title' => 'Ticket Created',
            'description' => 'Service request dibuat oleh ' . $ticket->requester_name,
            'timestamp' => $ticket->created_at,
            'user' => $ticket->user
        ]);

        // Validation
        if ($ticket->validated_at) {
            $timeline->push([
                'title' => 'Ticket ' . ucfirst($ticket->validation_status),
                'description' => $ticket->validation_notes ?? 'Ticket telah divalidasi',
                'timestamp' => $ticket->validated_at,
                'user' => $ticket->validator
            ]);
        }

        // Assignment
        if ($ticket->assigned_at) {
            $timeline->push([
                'title' => 'Ticket Assigned',
                'description' => 'Ditugaskan ke ' . optional($ticket->assignedTechnician)->name,
                'timestamp' => $ticket->assigned_at,
                'user' => $ticket->assignedBy
            ]);
        }

        // Closed
        if ($ticket->closed_at) {
            $timeline->push([
                'title' => 'Ticket Closed',
                'description' => $ticket->closure_notes ?? 'Ticket telah diselesaikan',
                'timestamp' => $ticket->closed_at,
                'user' => $ticket->closedBy
            ]);
        }

        return $timeline->sortBy('timestamp');
    }

    public function destroy($ticket_number)
    {
        $ticket = ServiceRequest::where('ticket_number', $ticket_number)->firstOrFail();

        $ticket->delete();

        return response()->json([
            'message' => 'Tiket berhasil dihapus',
            'ticket_number' => $ticket_number
        ]);
    }
}
