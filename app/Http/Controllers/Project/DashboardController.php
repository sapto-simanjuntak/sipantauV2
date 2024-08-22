<?php

namespace App\Http\Controllers\Project;

use Carbon\Carbon;
use App\Models\Modul\Task;
use Illuminate\Http\Request;
use App\Models\Modul\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $totalProjects = Project::count();
    //     $completedProjects = Project::whereHas('tasks', function ($query) {
    //         $query->where('status', 'completed');
    //     })->distinct()->count();
    //     $inProgressProjects = Project::whereHas('tasks', function ($query) {
    //         $query->where('status', 'in_progress');
    //     })->distinct()->count();
    //     $notStartedProjects = Project::whereHas('tasks', function ($query) {
    //         $query->where('status', 'not_started');
    //     })->distinct()->count();

    //     $projects = Project::with('tasks')->get();
    //     $monthlyReports = $projects->groupBy(function ($project) {
    //         return Carbon::parse($project->start_date)->format('F Y');
    //     });

    //     return view('pages.modul.project.dashboard', compact(
    //         'totalProjects',
    //         'completedProjects',
    //         'inProgressProjects',
    //         'notStartedProjects',
    //         'monthlyReports'
    //     ));
    //     // $user = Auth::user();

    //     // // Mengambil data untuk dashboard
    //     // $projects = Project::with('tasks')->get();
    //     // $tasks = Task::all();
    //     // $taskCount = $tasks->count();
    //     // $completedTasks = $tasks->where('status', 'completed')->count();
    //     // $inProgressTasks = $tasks->where('status', 'in_progress')->count();
    //     // $notStartedTasks = $tasks->where('status', 'not_started')->count();
    //     // $userProjects = $user->projects;

    //     // return view('pages.modul.project.dashboard', compact('projects', 'taskCount', 'completedTasks', 'inProgressTasks', 'notStartedTasks', 'userProjects'));
    // }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalProjects = Project::count();
        $completedProjects = Project::whereHas('tasks', function ($query) {
            $query->where('status', 'completed');
        })->distinct()->count();
        $inProgressProjects = Project::whereHas('tasks', function ($query) {
            $query->where('status', 'in_progress');
        })->distinct()->count();
        $notStartedProjects = Project::whereHas('tasks', function ($query) {
            $query->where('status', 'not_started');
        })->distinct()->count();

        $completedTasks = Project::withCount(['tasks' => function ($query) {
            $query->where('status', 'completed');
        }])->get()->sum('tasks_count');

        $inProgressTasks = Project::withCount(['tasks' => function ($query) {
            $query->where('status', 'in_progress');
        }])->get()->sum('tasks_count');

        $notStartedTasks = Project::withCount(['tasks' => function ($query) {
            $query->where('status', 'not_started');
        }])->get()->sum('tasks_count');

        $projects = Project::with('tasks')->get();
        $monthlyReports = $projects->groupBy(function ($project) {
            return Carbon::parse($project->start_date)->format('F Y');
        });

        return view('pages.modul.project.dashboard', compact(
            'totalProjects',
            'completedProjects',
            'inProgressProjects',
            'notStartedProjects',
            'monthlyReports',
            'completedTasks',
            'inProgressTasks',
            'notStartedTasks'
        ));
    }


    public function showReports()
    {
        $projects = Project::with('tasks')->get();
        return view('pages.modul.project.reports', compact('projects'));
    }

    public function showMonthlyReport()
    {
        $projects = Project::with('tasks')->get();
        $monthlyReports = $projects->groupBy(function ($project) {
            return Carbon::parse($project->start_date)->format('F Y'); // Mengelompokkan berdasarkan bulan dan tahun
        });

        return view('pages.modul.project.monthly_report', compact('monthlyReports'));
    }
}
