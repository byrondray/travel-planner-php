<?php

namespace App\Http\Controllers;

use App\Models\TravelPlan;
use App\Models\Destination;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TravelPlanController extends Controller
{
    public function index()
    {
        $travelPlans = TravelPlan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('travelPlans'));
    }

    public function create()
    {
        return view('travel-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'nullable|numeric',
            'currency' => 'nullable|string|size:3',
            'preferences' => 'nullable|array',
        ]);

        $travelPlan = new TravelPlan();
        $travelPlan->user_id = Auth::id();
        $travelPlan->title = $validated['title'];
        $travelPlan->description = $validated['description'] ?? null;
        $travelPlan->start_date = $validated['start_date'];
        $travelPlan->end_date = $validated['end_date'];
        $travelPlan->budget = $validated['budget'] ?? null;
        $travelPlan->currency = $validated['currency'] ?? 'USD';
        $travelPlan->status = 'draft';
        $travelPlan->preferences = $validated['preferences'] ? json_encode($validated['preferences']) : null;
        $travelPlan->save();

        return redirect()->route('travel-plans.show', $travelPlan->id)->with('success', 'Travel plan created successfully');
    }

    public function show(string $id)
    {
        $travelPlan = TravelPlan::with(['destinations', 'itineraries.activities'])->findOrFail($id);

        if ($travelPlan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('travel-plans.show', compact('travelPlan'));
    }

    public function edit(string $id)
    {
        $travelPlan = TravelPlan::findOrFail($id);

        if ($travelPlan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('travel-plans.edit', compact('travelPlan'));
    }

    public function update(Request $request, string $id)
    {
        $travelPlan = TravelPlan::findOrFail($id);

        if ($travelPlan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'nullable|numeric',
            'currency' => 'nullable|string|size:3',
            'status' => 'nullable|string|in:draft,planned,in_progress,completed,cancelled',
            'preferences' => 'nullable|array',
        ]);

        $travelPlan->title = $validated['title'];
        $travelPlan->description = $validated['description'] ?? $travelPlan->description;
        $travelPlan->start_date = $validated['start_date'];
        $travelPlan->end_date = $validated['end_date'];
        $travelPlan->budget = $validated['budget'] ?? $travelPlan->budget;
        $travelPlan->currency = $validated['currency'] ?? $travelPlan->currency;
        $travelPlan->status = $validated['status'] ?? $travelPlan->status;
        $travelPlan->preferences = $validated['preferences'] ? json_encode($validated['preferences']) : $travelPlan->preferences;
        $travelPlan->save();

        return redirect()->route('travel-plans.show', $travelPlan->id)->with('success', 'Travel plan updated successfully');
    }

    public function destroy(string $id)
    {
        $travelPlan = TravelPlan::findOrFail($id);

        if ($travelPlan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $travelPlan->delete();

        return redirect()->route('travel-plans.index')->with('success', 'Travel plan deleted successfully');
    }

    public function processing(TravelPlan $travelPlan)
    {
        if ($travelPlan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // If already completed, redirect to show page
        if ($travelPlan->processing_status === 'completed') {
            return redirect()->route('travel-plans.show', $travelPlan->id);
        }

        return view('travel-plans.processing', compact('travelPlan'));
    }

    public function status(TravelPlan $travelPlan)
    {
        if ($travelPlan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'processing_status' => $travelPlan->processing_status,
            'processing_error' => $travelPlan->processing_error,
            'processing_started_at' => $travelPlan->processing_started_at,
            'processing_completed_at' => $travelPlan->processing_completed_at,
            'redirect_url' => $travelPlan->processing_status === 'completed' 
                ? route('travel-plans.show', $travelPlan->id) 
                : null
        ]);
    }
}