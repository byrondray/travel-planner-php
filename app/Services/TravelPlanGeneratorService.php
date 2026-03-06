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
    protected $client;

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
        $duration = $start->diff($end)->days;

        $prompt = $this->buildPrompt(
            $requestData['destination'],
            $duration,
            $requestData['budget'] ?? null,
            $requestData['preferences'] ?? []
        );

        Log::info('Calling OpenAI API for travel plan', ['plan_id' => $travelPlan->id]);

        $response = $this->client->chat()->create([
            'model' => 'gpt-4-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a travel planning assistant. Create detailed travel itineraries with activities, accommodations, transportation, and meal recommendations.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $aiResponse = json_decode($response->choices[0]->message->content, true);

        if (!is_array($aiResponse)) {
            throw new \Exception('Invalid response format from OpenAI. Expected JSON object.');
        }

        Log::info('OpenAI response received, saving to database', ['plan_id' => $travelPlan->id]);

        $this->saveToDatabase($travelPlan, $aiResponse, $prompt);

        $travelPlan->update([
            'processing_status' => 'completed',
            'processing_completed_at' => now(),
        ]);
    }

    public function buildPrompt(string $destination, int $duration, ?float $budget = null, array $preferences = []): string
    {
        $budgetText = $budget ? "with a budget of approximately $budget USD" : "with no specific budget constraints";
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
            "{\n" .
            "    \"description\": \"Overall description of the trip\",\n" .
            "    \"destinations\": [\n" .
            "        {\n" .
            "            \"name\": \"City/Location name\",\n" .
            "            \"country\": \"Country\",\n" .
            "            \"description\": \"Brief description\",\n" .
            "            \"arrival_date\": \"YYYY-MM-DD\",\n" .
            "            \"departure_date\": \"YYYY-MM-DD\"\n" .
            "        }\n" .
            "    ],\n" .
            "    \"itineraries\": [\n" .
            "        {\n" .
            "            \"day\": 1,\n" .
            "            \"date\": \"YYYY-MM-DD\",\n" .
            "            \"title\": \"Day 1: Title\",\n" .
            "            \"description\": \"Overview of the day\",\n" .
            "            \"destination\": \"City/Location name\",\n" .
            "            \"activities\": [\n" .
            "                {\n" .
            "                    \"title\": \"Activity name\",\n" .
            "                    \"description\": \"Activity description\",\n" .
            "                    \"start_time\": \"HH:MM\",\n" .
            "                    \"end_time\": \"HH:MM\",\n" .
            "                    \"location\": \"Location name\",\n" .
            "                    \"type\": \"sightseeing|food|transportation|accommodation|entertainment|shopping|other\",\n" .
            "                    \"cost\": 0.00\n" .
            "                }\n" .
            "            ],\n" .
            "            \"transportation\": {\n" .
            "                \"method\": \"Method of transport\",\n" .
            "                \"details\": \"Details about the transport\",\n" .
            "                \"cost\": 0.00\n" .
            "            },\n" .
            "            \"accommodation\": {\n" .
            "                \"name\": \"Accommodation name\",\n" .
            "                \"description\": \"Description\",\n" .
            "                \"address\": \"Address\",\n" .
            "                \"cost\": 0.00\n" .
            "            },\n" .
            "            \"meals\": [\n" .
            "                {\n" .
            "                    \"type\": \"breakfast|lunch|dinner\",\n" .
            "                    \"venue\": \"Restaurant name\",\n" .
            "                    \"description\": \"Description\",\n" .
            "                    \"cost\": 0.00\n" .
            "                }\n" .
            "            ]\n" .
            "        }\n" .
            "    ],\n" .
            "    \"total_estimated_cost\": 0.00\n" .
            "}";
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
                foreach ($aiResponse['itineraries'] as $position => $itinData) {
                    $destination = null;
                    if (isset($itinData['destination'])) {
                        $destination = Destination::where('travel_plan_id', $travelPlan->id)
                            ->where('name', $itinData['destination'])
                            ->first();
                    }

                    $itinerary = Itinerary::create([
                        'travel_plan_id' => $travelPlan->id,
                        'destination_id' => $destination?->id,
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
