<?php

namespace App\Http\Controllers;

use App\Models\TravelPlan;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'nullable|numeric',
            'preferences' => 'nullable|array',
        ]);

        $start = new \DateTime($validated['start_date']);
        $end = new \DateTime($validated['end_date']);
        $duration = $start->diff($end)->days;

        $prompt = $this->buildPrompt(
            $validated['destination'],
            $duration,
            $validated['budget'] ?? null,
            $validated['preferences'] ?? []
        );

        try {
            $response = $this->client->chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a travel planning assistant. Create detailed travel itineraries with activities, accommodations, transportation, and meal recommendations.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $aiResponse = json_decode($response->choices[0]->message->content, true);

            return $this->saveToDatabase(
                $validated['title'],
                $validated['start_date'],
                $validated['end_date'],
                $validated['budget'] ?? null,
                $prompt,
                $aiResponse,
                $validated['preferences'] ?? []
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate travel plan: ' . $e->getMessage());
        }
    }

    private function buildPrompt($destination, $duration, $budget = null, $preferences = [])
    {
        $budgetText = $budget ? "with a budget of approximately $budget USD" : "with no specific budget constraints";
        $preferencesText = '';

        if (!empty($preferences)) {
            $preferencesText = "The traveler has the following preferences: ";
            foreach ($preferences as $key => $value) {
                $preferencesText .= "$key: $value, ";
            }
            $preferencesText = rtrim($preferencesText, ', ') . ".";
        }

        return <<<PROMPT
        Create a detailed {$duration}-day travel plan for {$destination} {$budgetText}. {$preferencesText}

        Return your response as a JSON object with the following structure:
        {
            "description": "Overall description of the trip",
            "destinations": [
                {
                    "name": "City/Location name",
                    "country": "Country",
                    "description": "Brief description",
                    "arrival_date": "YYYY-MM-DD",
                    "departure_date": "YYYY-MM-DD"
                }
            ],
            "itineraries": [
                {
                    "day": 1,
                    "date": "YYYY-MM-DD",
                    "title": "Day 1: Title",
                    "description": "Overview of the day",
                    "destination": "City/Location name",
                    "activities": [
                        {
                            "title": "Activity name",
                            "description": "Activity description",
                            "start_time": "HH:MM",
                            "end_time": "HH:MM",
                            "location": "Location name",
                            "type": "sightseeing|food|transportation|accommodation|entertainment|shopping|other",
                            "cost": 0.00
                        }
                    ],
                    "transportation": {
                        "method": "Method of transport",
                        "details": "Details about the transport",
                        "cost": 0.00
                    },
                    "accommodation": {
                        "name": "Accommodation name",
                        "description": "Description",
                        "address": "Address",
                        "cost": 0.00
                    },
                    "meals": [
                        {
                            "type": "breakfast|lunch|dinner",
                            "venue": "Restaurant name",
                            "description": "Description",
                            "cost": 0.00
                        }
                    ]
                }
            ],
            "total_estimated_cost": 0.00
        }
    PROMPT;
    }

    private function saveToDatabase($title, $startDate, $endDate, $budget, $prompt, $aiResponse, $preferences)
    {
        \DB::beginTransaction();

        try {
            $travelPlan = TravelPlan::create([
                'user_id' => Auth::id(),
                'title' => $title,
                'description' => $aiResponse['description'] ?? null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'budget' => $budget,
                'status' => 'planned',
                'preferences' => json_encode($preferences),
                'ai_prompt_used' => $prompt,
                'ai_response' => json_encode($aiResponse)
            ]);

            if (isset($aiResponse['destinations']) && is_array($aiResponse['destinations'])) {
                foreach ($aiResponse['destinations'] as $position => $destData) {
                    Destination::create([
                        'travel_plan_id' => $travelPlan->id,
                        'name' => $destData['name'],
                        'description' => $destData['description'] ?? null,
                        'country' => $destData['country'] ?? null,
                        'city' => $destData['name'],
                        'arrival_date' => $destData['arrival_date'] ?? null,
                        'departure_date' => $destData['departure_date'] ?? null,
                        'position' => $position
                    ]);
                }
            }

            if (isset($aiResponse['itineraries']) && is_array($aiResponse['itineraries'])) {
                foreach ($aiResponse['itineraries'] as $position => $itinData) {
                    $destination = null;
                    if (isset($itinData['destination'])) {
                        $destination = Destination::where('travel_plan_id', $travelPlan->id)
                            ->where('name', $itinData['destination'])
                            ->first();
                    }

                    $itinerary = Itinerary::create([
                        'travel_plan_id' => $travelPlan->id,
                        'destination_id' => $destination ? $destination->id : null,
                        'title' => $itinData['title'] ?? 'Day ' . ($position + 1),
                        'date' => $itinData['date'] ?? date('Y-m-d', strtotime($startDate . ' + ' . $position . ' days')),
                        'description' => $itinData['description'] ?? null,
                        'transportation' => isset($itinData['transportation']) ? json_encode($itinData['transportation']) : null,
                        'accommodations' => isset($itinData['accommodation']) ? json_encode($itinData['accommodation']) : null,
                        'meals' => isset($itinData['meals']) ? json_encode($itinData['meals']) : null,
                        'position' => $position
                    ]);

                    if (isset($itinData['activities']) && is_array($itinData['activities'])) {
                        foreach ($itinData['activities'] as $actPosition => $actData) {
                            Activity::create([
                                'itinerary_id' => $itinerary->id,
                                'title' => $actData['title'],
                                'description' => $actData['description'] ?? null,
                                'start_time' => $actData['start_time'] ?? null,
                                'end_time' => $actData['end_time'] ?? null,
                                'location' => $actData['location'] ?? null,
                                'cost' => $actData['cost'] ?? null,
                                'type' => $actData['type'] ?? 'other',
                                'position' => $actPosition
                            ]);
                        }
                    }
                }
            }

            \DB::commit();

            return redirect()->route('travel-plans.show', $travelPlan->id)
                ->with('success', 'Travel plan generated successfully');
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->with('error', 'Failed to save travel plan: ' . $e->getMessage());
        }
    }
}