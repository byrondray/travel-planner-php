<?php

namespace App\Services;

use App\Models\TravelPlan;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenAI;

class TravelPlanGeneratorService
{
    private const RESPONSE_SCHEMA = <<<'JSON'
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
    JSON;

    protected \OpenAI\Client $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.openai.api_key'));
    }

    public function generate(TravelPlan $travelPlan, array $requestData): void
    {
        $travelPlan->update([
            'processing_status' => 'processing',
            'processing_started_at' => now(),
        ]);

        $start = new \DateTime($requestData['start_date']);
        $end = new \DateTime($requestData['end_date']);
        $duration = $start->diff($end)->days + 1;

        $prompt = $this->buildPrompt(
            $requestData['destination'],
            $duration,
            $requestData['budget'] ?? null,
            $requestData['currency'] ?? 'USD',
            $requestData['preferences'] ?? []
        );

        Log::info('Calling OpenAI API for travel plan', ['plan_id' => $travelPlan->id]);

        $response = $this->client->chat()->create([
            'model' => config('services.openai.model'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are a travel planning assistant. Create detailed travel itineraries with activities, accommodations, transportation, and meal recommendations.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $rawContent = $response->choices[0]->message->content;
        $aiResponse = json_decode($rawContent, true);

        if (!is_array($aiResponse)) {
            Log::error('Failed to decode OpenAI response as JSON', [
                'plan_id' => $travelPlan->id,
                'json_error' => json_last_error_msg(),
                'raw_response' => $rawContent,
            ]);

            throw new \Exception('Invalid response format from OpenAI. Expected JSON object.');
        }

        Log::info('OpenAI response received, saving to database', ['plan_id' => $travelPlan->id]);

        DB::transaction(function () use ($travelPlan, $aiResponse, $prompt) {
            $this->saveToDatabase($travelPlan, $aiResponse, $prompt);

            $travelPlan->update([
                'processing_status' => 'completed',
                'processing_completed_at' => now(),
            ]);
        });
    }

    public function buildPrompt(string $destination, int $duration, ?float $budget = null, string $currency = 'USD', array $preferences = []): string
    {
        $budgetText = $budget ? "with a budget of approximately {$budget} {$currency}" : "with no specific budget constraints";
        $preferencesText = '';

        if (!empty($preferences)) {
            $preferencesText = "The traveler has the following preferences: ";
            foreach ($preferences as $key => $value) {
                if (is_array($value)) {
                    $preferencesText .= "$key: " . implode(', ', $value) . ", ";
                } else {
                    $preferencesText .= "$key: $value, ";
                }
            }
            $preferencesText = rtrim($preferencesText, ', ') . ".";
        }

        return "Create a detailed {$duration}-day travel plan for {$destination} {$budgetText}. {$preferencesText}\n\n" .
            "Return your response as a JSON object with the following structure:\n" .
            self::RESPONSE_SCHEMA;
    }

    public function saveToDatabase(TravelPlan $travelPlan, array $aiResponse, string $prompt): void
    {
        DB::beginTransaction();

        try {
            $travelPlan->update([
                'description' => $aiResponse['description'] ?? null,
                'ai_prompt_used' => $prompt,
                'ai_response' => $aiResponse,
            ]);

            if (isset($aiResponse['destinations']) && is_array($aiResponse['destinations'])) {
                foreach ($aiResponse['destinations'] as $position => $destData) {
                    if (empty($destData['name'])) {
                        continue;
                    }

                    Destination::create([
                        'travel_plan_id' => $travelPlan->id,
                        'name' => $destData['name'],
                        'description' => $destData['description'] ?? null,
                        'country' => $destData['country'] ?? null,
                        'city' => $destData['name'],
                        'arrival_date' => $destData['arrival_date'] ?? null,
                        'departure_date' => $destData['departure_date'] ?? null,
                        'position' => $position,
                    ]);
                }
            }

            if (isset($aiResponse['itineraries']) && is_array($aiResponse['itineraries'])) {
                $destinationIdsByName = $travelPlan->destinations()->pluck('id', 'name');

                foreach ($aiResponse['itineraries'] as $position => $itinData) {
                    $destinationName = $itinData['destination'] ?? null;
                    $destinationId = $destinationName ? ($destinationIdsByName[$destinationName] ?? null) : null;

                    $itinerary = Itinerary::create([
                        'travel_plan_id' => $travelPlan->id,
                        'destination_id' => $destinationId,
                        'title' => $itinData['title'] ?? 'Day ' . ($position + 1),
                        'date' => $itinData['date'] ?? date('Y-m-d', strtotime($travelPlan->start_date . ' + ' . $position . ' days')),
                        'description' => $itinData['description'] ?? null,
                        'transportation' => $itinData['transportation'] ?? null,
                        'accommodations' => $itinData['accommodation'] ?? null,
                        'meals' => $itinData['meals'] ?? null,
                        'position' => $position,
                    ]);

                    if (isset($itinData['activities']) && is_array($itinData['activities'])) {
                        foreach ($itinData['activities'] as $actPosition => $actData) {
                            if (empty($actData['title'])) {
                                continue;
                            }

                            Activity::create([
                                'itinerary_id' => $itinerary->id,
                                'title' => $actData['title'],
                                'description' => $actData['description'] ?? null,
                                'start_time' => $actData['start_time'] ?? null,
                                'end_time' => $actData['end_time'] ?? null,
                                'location' => $actData['location'] ?? null,
                                'cost' => $actData['cost'] ?? null,
                                'type' => $actData['type'] ?? 'other',
                                'position' => $actPosition,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            Log::info('Travel plan data saved successfully', ['plan_id' => $travelPlan->id]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to save travel plan data', ['plan_id' => $travelPlan->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
