<?php

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Akses\RoleController;
use App\Http\Controllers\Akses\UserController;
use App\Http\Controllers\Project\TaskController;
use App\Http\Controllers\Support\AjaxController;
use App\Http\Controllers\Project\CommentController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Akses\PermissionController;
use App\Http\Controllers\Project\FormulirController;
use App\Http\Controllers\Project\DashboardController;
use App\Http\Controllers\Support\UserTicketController;
use App\Http\Controllers\Support\ServiceRequestController;
use App\Http\Controllers\Support\SignatureVerificationController;
use App\Http\Controllers\Support\TechnicianController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/', function () {
    return view('auth/login');
});

Route::group(
    ['middleware' => ['auth', 'role:superadmin|admin|teknisi|user']],
    function () {
        Route::resource('data', DataController::class);
        Route::delete('/data/{id}', [DataController::class, 'delete'])->name('data.delete');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports', [DashboardController::class, 'showReports'])->name('reports');
        Route::get('/reports/monthly', [DashboardController::class, 'showMonthlyReport'])->name('reports.monthly');
        Route::get('formulir/{id}/view-task', [FormulirController::class, 'giveTask'])->name('formulir.view-task');
        Route::resource('formulir', FormulirController::class);
    }
);

Route::group(
    ['middleware' => ['auth', 'role:superadmin|admin|user']],
    function () {
        Route::resource('data', DataController::class);
        Route::delete('/data/{id}', [DataController::class, 'delete'])->name('data.delete');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports', [DashboardController::class, 'showReports'])->name('reports');
        Route::get('/reports/monthly', [DashboardController::class, 'showMonthlyReport'])->name('reports.monthly');
        Route::resource('projects', ProjectController::class);
        Route::delete('/projects/{id}', [ProjectController::class, 'delete'])->name('projects.delete');
        Route::get('project/{id}/give-task', [ProjectController::class, 'giveTask'])->name('project.give-task');
        Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('register', [AuthController::class, 'register']);
        Route::resource('tasks', TaskController::class);
        Route::resource('comments', CommentController::class);
    }
);

Route::group(
    ['middleware' => ['auth', 'role:superadmin']],
    function () {
        Route::resource('permission', PermissionController::class);
        Route::delete('/permission/{id}', [PermissionController::class, 'delete'])->name('permission.delete');
        Route::resource('role', RoleController::class);
        Route::delete('/role/{id}', [RoleController::class, 'delete'])->name('role.delete');
        Route::resource('user', UserController::class);
        Route::get('user/{id}/roles', [App\Http\Controllers\Akses\UserController::class, 'getUserRoles'])->name('user.roles');
        Route::get('role/{roleId}/give-permissions', [App\Http\Controllers\Akses\RoleController::class, 'addPermissionToRole']);
        Route::put('role/{roleId}/give-permissions', [App\Http\Controllers\Akses\RoleController::class, 'givePermissionToRole']);
        Route::get('get-roles', [App\Http\Controllers\Global\SelectController::class, 'selectRoles']);
        Route::get('get-user', [App\Http\Controllers\Global\SelectController::class, 'selectUser']);
        Route::post('/projects/add-pic', [ProjectController::class, 'addPic'])->name('projects.addPic');
        Route::post('/projects/delete-pic', [ProjectController::class, 'deletePic'])->name('projects.deletePic');
        Route::post('/projects/add-validasi', [ProjectController::class, 'addValidasi'])->name('projects.addValidasi');
        Route::post('/projects/set-status-project', [ProjectController::class, 'setStatusproject'])->name('projects.setStatusproject');
    }
);

Route::group(['middleware' => ['role:superadmin|admin|unit|user']], function () {
    Route::get('user/{user}', [UserController::class, 'show'])->name('user.show');
    Route::post('profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
});

Route::get('/ticket-request/create', function () {
    if (auth()->user()->hasRole(['user'])) {
        return view('pages.modul.user-ticket.create'); // Mobile
    }
    return view('pages.modul.service-request.create'); // Desktop
})->name('service.create');


Route::prefix('service-request')->middleware(['auth'])->group(function () {
    Route::get('/', [ServiceRequestController::class, 'index'])->name('service.index');
    Route::post('/', [ServiceRequestController::class, 'store'])->name('service.store');

    Route::get('/ticket/{ticket_number}', [ServiceRequestController::class, 'show'])->name('service.show');
    Route::get('/{ticket_number}/edit', [ServiceRequestController::class, 'edit'])->name('service.edit');
    Route::post('/{ticket_number}/update', [ServiceRequestController::class, 'update'])->name('service.update');
    Route::delete('/ticket/{ticket_number}', [ServiceRequestController::class, 'destroy'])->name('service.destroy');

    Route::get('/ticket/{ticket_number}/print', [ServiceRequestController::class, 'printTicket'])->name('service.print');
    Route::post('/ticket/{ticket_number}/approve', [ServiceRequestController::class, 'approve'])->name('service.approve');
    Route::post('/ticket/{ticket_number}/reject', [ServiceRequestController::class, 'reject'])->name('service.reject');
    Route::post('/ticket/{ticket_number}/assign', [ServiceRequestController::class, 'assign'])->name('service.assign');
    Route::post('/ticket/{ticket_number}/update-status', [ServiceRequestController::class, 'updateStatus'])->name('service.updateStatus');
});

// ============================================
// AJAX ROUTES - Harus di luar prefix service-request!
// ============================================
Route::prefix('ajax')->middleware(['auth'])->group(function () {
    // ✅ Gunakan AjaxController untuk semua AJAX
    Route::get('/hospital-units', [AjaxController::class, 'getHospitalUnits'])->name('ajax.hospital-units');
    Route::get('/problem-categories', [AjaxController::class, 'getProblemCategories'])->name('ajax.problem-categories');
    Route::get('/sub-categories/{categoryId}', [AjaxController::class, 'getSubCategories'])->name('ajax.sub-categories');
    Route::get('/ticket-statistics', [AjaxController::class, 'getTicketStatistics'])->name('ajax.ticket-statistics');
});

Route::prefix('verify')->name('verify.')->group(function () {
    Route::get('/', [SignatureVerificationController::class, 'index'])->name('index');
    Route::get('/qr/{data}', [SignatureVerificationController::class, 'verifyFromQr'])->name('qr');
    Route::post('/manual', [SignatureVerificationController::class, 'verifyManual'])->name('manual');
    Route::get('/history/{ticketNumber}', [SignatureVerificationController::class, 'history'])->name('history');
});

Route::group(['middleware' => ['role:superadmin|admin|user']], function () {
    Route::get('/ticket-user', [UserTicketController::class, 'index'])->name('ticket.index');
    Route::get('/stats', [UserTicketController::class, 'getStats'])->name('stats');
    Route::get('/search', [UserTicketController::class, 'search'])->name('search');
    Route::get('/tickets', [UserTicketController::class, 'getTickets'])->name('tickets');
    Route::get('/ticket-mobile/{ticket_number}', [UserTicketController::class, 'show'])->name('ticket.show');
    Route::get('/ticket-mobile/{ticket_number}/edit', [UserTicketController::class, 'edit'])->name('ticket.edit');
    Route::post('/ticket-mobile/{ticket_number}/update', [UserTicketController::class, 'update'])->name('ticket.update');
});

// Technician Routes (Mobile)
Route::middleware(['auth', 'role:technician|teknisi'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/tickets', [TechnicianController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket_number}', [TechnicianController::class, 'show'])->name('ticket.show');
    Route::post('/tickets/{ticket_number}/update-status', [TechnicianController::class, 'updateStatus'])->name('ticket.update-status');
    Route::post('/tickets/{ticket_number}/add-note', [TechnicianController::class, 'addNote'])->name('ticket.add-note');
    Route::get('/stats', [TechnicianController::class, 'getStats'])->name('stats');
});
