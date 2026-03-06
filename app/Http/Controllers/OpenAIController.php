<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateTravelPlanRequest;
use App\Jobs\GenerateTravelPlan;
use App\Models\TravelPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OpenAIController extends Controller
{
    public function generateTravelPlan(GenerateTravelPlanRequest $request)
    {
        $validated = $request->validated();

        Log::info('Starting travel plan generation', ['destination' => $validated['destination']]);

        $travelPlan = TravelPlan::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'budget' => $validated['budget'],
            'status' => 'draft',
            'processing_status' => 'pending',
            'preferences' => $validated['preferences'] ?? [],
        ]);

        GenerateTravelPlan::dispatch($travelPlan, $validated);

        Log::info('Travel plan created and job dispatched', ['plan_id' => $travelPlan->id]);

        return redirect()->route('travel-plans.processing', $travelPlan->id)
            ->with('success', 'Your travel plan is being generated! This may take up to 2 minutes.');
    }
}