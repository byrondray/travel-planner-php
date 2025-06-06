<?php

namespace App\Http\Controllers;

use App\Models\TravelPlan;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenAI;

class OpenAIController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function generateTravelPlan(Request $request)
    {
        try {
            Log::info('Starting travel plan generation', ['request' => $request->all()]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'destination' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'budget' => 'nullable|numeric',
                'preferences' => 'nullable|array',
            ]);

            Log::info('Validation passed', ['destination' => $validated['destination']]);

            // Create travel plan record immediately with pending status
            $travelPlan = TravelPlan::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'budget' => $validated['budget'],
                'status' => 'draft',
                'processing_status' => 'pending',
                'preferences' => json_encode($validated['preferences'] ?? [])
            ]);

            // Dispatch the job to generate the travel plan
            \App\Jobs\GenerateTravelPlan::dispatch($travelPlan, $validated);

            Log::info('Travel plan generation job dispatched', ['plan_id' => $travelPlan->id]);

            return redirect()->route('travel-plans.processing', $travelPlan->id)
                ->with('success', 'Your travel plan is being generated! This may take up to 2 minutes.');

        } catch (\Exception $e) {
            Log::error('Exception in generateTravelPlan', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }



}