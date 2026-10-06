<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display user's projects.
     */
    public function index(): View
    {
        $projects = Auth::user()->projects()->latest()->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show project creation form.
     */
    public function create(): View
    {
        $services = Service::active()->get();

        return view('projects.create', compact('services'));
    }

    /**
     * Store new project.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'service_type' => 'required|string|exists:services,slug',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'requirements' => 'nullable|string|max:2000',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $project = Auth::user()->projects()->create([
            'title' => $request->title,
            'description' => $request->description,
            'service_type' => $request->service_type,
            'budget' => $request->budget,
            'deadline' => $request->deadline,
            'requirements' => $request->requirements,
            'contact_phone' => $request->contact_phone ?: Auth::user()->phone,
            'contact_email' => $request->contact_email ?: Auth::user()->email,
            'status' => 'pending'
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Проект успешно создан! Мы свяжемся с вами в ближайшее время.');
    }

    /**
     * Display specific project.
     */
    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        return view('projects.show', compact('project'));
    }

    /**
     * Show project edit form.
     */
    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        $services = Service::active()->get();

        return view('projects.edit', compact('project', 'services'));
    }

    /**
     * Update project.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'service_type' => 'required|string|exists:services,slug',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'requirements' => 'nullable|string|max:2000',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $project->update($request->only([
            'title', 'description', 'service_type', 'budget',
            'deadline', 'requirements', 'contact_phone', 'contact_email'
        ]));

        return redirect()->route('projects.show', $project)
            ->with('success', 'Проект успешно обновлен!');
    }

    /**
     * Delete project.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Проект успешно удален!');
    }

    /**
     * Get project quote via API.
     */
    public function getQuote(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string',
            'project_type' => 'required|string',
            'features' => 'array',
            'timeline' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate estimated price based on service type and features
        $basePrice = $this->calculateBasePrice($request->service_type, $request->project_type);
        $featuresPrice = $this->calculateFeaturesPrice($request->features ?? []);
        $timelineMultiplier = $this->getTimelineMultiplier($request->timeline);

        $estimatedPrice = ($basePrice + $featuresPrice) * $timelineMultiplier;

        return response()->json([
            'success' => true,
            'estimated_price' => $estimatedPrice,
            'breakdown' => [
                'base_price' => $basePrice,
                'features_price' => $featuresPrice,
                'timeline_multiplier' => $timelineMultiplier,
                'total' => $estimatedPrice
            ]
        ]);
    }

    /**
     * Calculate base price for service type.
     */
    private function calculateBasePrice(string $serviceType, string $projectType): int
    {
        $prices = [
            'websites' => [
                'landing' => 25000,
                'corporate' => 50000,
                'ecommerce' => 100000,
                'custom' => 150000
            ],
            'design' => [
                'logo' => 10000,
                'branding' => 30000,
                'ui_ux' => 40000,
                'print' => 15000
            ],
            'promotion' => [
                'seo' => 20000,
                'context' => 15000,
                'smm' => 25000,
                'complex' => 50000
            ]
        ];

        return $prices[$serviceType][$projectType] ?? 30000;
    }

    /**
     * Calculate additional price for features.
     */
    private function calculateFeaturesPrice(array $features): int
    {
        $featurePrices = [
            'admin_panel' => 15000,
            'payment_integration' => 20000,
            'mobile_app' => 50000,
            'api_integration' => 10000,
            'multilingual' => 15000,
            'analytics' => 5000
        ];

        $total = 0;
        foreach ($features as $feature) {
            $total += $featurePrices[$feature] ?? 0;
        }

        return $total;
    }

    /**
     * Get timeline multiplier.
     */
    private function getTimelineMultiplier(string $timeline): float
    {
        $multipliers = [
            'urgent' => 1.5,    // 1-2 weeks
            'fast' => 1.2,      // 3-4 weeks
            'normal' => 1.0,    // 1-2 months
            'flexible' => 0.9   // 3+ months
        ];

        return $multipliers[$timeline] ?? 1.0;
    }
}
