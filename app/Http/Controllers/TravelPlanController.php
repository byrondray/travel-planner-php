<?php

namespace App\Http\Controllers;

use App\Models\TravelPlan;
use Illuminate\Support\Facades\Auth;

class TravelPlanController extends Controller
{
    public function index()
    {
        $travelPlans = TravelPlan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('dashboard', compact('travelPlans'));
    }

    public function create()
    {
        return view('travel-plans.create');
    }

    public function show(TravelPlan $travelPlan)
    {
        $this->authorize('view', $travelPlan);

        $travelPlan->load(['destinations', 'itineraries.activities']);

        return view('travel-plans.show', compact('travelPlan'));
    }

    public function destroy(TravelPlan $travelPlan)
    {
        $this->authorize('delete', $travelPlan);

        $travelPlan->delete();

        return redirect()->route('travel-plans.index')->with('success', 'Travel plan deleted successfully');
    }

    public function processing(TravelPlan $travelPlan)
    {
        $this->authorize('view', $travelPlan);

        if ($travelPlan->processing_status === 'completed') {
            return redirect()->route('travel-plans.show', $travelPlan->id);
        }

        return view('travel-plans.processing', compact('travelPlan'));
    }

    public function status(TravelPlan $travelPlan)
    {
        $this->authorize('view', $travelPlan);

        return response()->json([
            'processing_status' => $travelPlan->processing_status,
            'processing_error' => $travelPlan->processing_error,
            'processing_started_at' => $travelPlan->processing_started_at,
            'processing_completed_at' => $travelPlan->processing_completed_at,
            'redirect_url' => $travelPlan->processing_status === 'completed' 
                ? route('travel-plans.show', $travelPlan->id) 
                : null,
        ]);
    }
}