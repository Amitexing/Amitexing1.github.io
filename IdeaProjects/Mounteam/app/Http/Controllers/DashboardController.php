<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Project;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display user dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        // User statistics
        $userStats = [
            'total_projects' => $user->projects()->count(),
            'active_projects' => $user->projects()->whereIn('status', ['pending', 'in_progress'])->count(),
            'completed_projects' => $user->projects()->where('status', 'completed')->count(),
            'total_spent' => $user->projects()->where('status', 'completed')->sum('final_price') ?? 0
        ];

        // Recent projects
        $recentProjects = $user->projects()->latest()->take(5)->get();

        // Project status distribution
        $projectsByStatus = $user->projects()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard.index', compact('userStats', 'recentProjects', 'projectsByStatus'));
    }

    /**
     * Display admin dashboard.
     */
    public function admin(): View
    {
        $this->authorize('admin-access');

        // Overall statistics
        $stats = [
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('status', ['pending', 'in_progress'])->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'total_revenue' => Project::where('status', 'completed')->sum('final_price') ?? 0,
            'portfolio_items' => Portfolio::count()
        ];

        // Recent projects
        $recentProjects = Project::with('user')->latest()->take(10)->get();

        // Recent users
        $recentUsers = User::latest()->take(10)->get();

        // Projects by service type
        $projectsByService = Project::selectRaw('service_type, COUNT(*) as count')
            ->groupBy('service_type')
            ->pluck('count', 'service_type')
            ->toArray();

        // Monthly revenue chart data
        $monthlyRevenue = Project::where('status', 'completed')
            ->whereYear('completed_at', now()->year)
            ->selectRaw('MONTH(completed_at) as month, SUM(final_price) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Fill missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyRevenue[$i])) {
                $monthlyRevenue[$i] = 0;
            }
        }
        ksort($monthlyRevenue);

        return view('dashboard.admin', compact(
            'stats',
            'recentProjects',
            'recentUsers',
            'projectsByService',
            'monthlyRevenue'
        ));
    }
}
