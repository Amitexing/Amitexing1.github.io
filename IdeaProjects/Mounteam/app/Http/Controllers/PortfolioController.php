<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    /**
     * Display portfolio page.
     */
    public function index(Request $request): View
    {
        $query = Portfolio::query()->published();

        // Filter by service type if provided
        if ($request->has('service') && $request->service) {
            $query->where('service_type', $request->service);
        }

        // Filter by technology if provided
        if ($request->has('technology') && $request->technology) {
            $query->whereJsonContains('technologies', $request->technology);
        }

        // Search by title or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $portfolioItems = $query->latest()->paginate(12);

        $serviceTypes = Portfolio::distinct('service_type')->pluck('service_type');
        $technologies = Portfolio::whereNotNull('technologies')
            ->get()
            ->pluck('technologies')
            ->flatten()
            ->unique()
            ->values();

        return view('portfolio.index', compact('portfolioItems', 'serviceTypes', 'technologies'));
    }

    /**
     * Display specific portfolio item.
     */
    public function show(Portfolio $project): View
    {
        // Get related projects
        $relatedProjects = Portfolio::where('service_type', $project->service_type)
            ->where('id', '!=', $project->id)
            ->published()
            ->latest()
            ->take(3)
            ->get();

        return view('portfolio.show', compact('project', 'relatedProjects'));
    }
}
