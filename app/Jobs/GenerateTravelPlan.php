<?php

namespace App\Jobs;

use App\Models\TravelPlan;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenAI;

class GenerateTravelPlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180; // 3 minutes timeout
    public $tries = 2;

    protected $travelPlan;
    protected $requestData;

    /**
     * Create a new job instance.
     */
    public function __construct(TravelPlan $travelPlan, array $requestData)
    {
        $this->travelPlan = $travelPlan;
        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting travel plan generation job', ['plan_id' => $this->travelPlan->id]);
            
            // Update status to processing
            $this->travelPlan->update([
                'processing_status' => 'processing',
                'processing_started_at' => now()
            ]);

            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $start = new \DateTime($this->requestData['start_date']);
            $end = new \DateTime($this->requestData['end_date']);
            $duration = $start->diff($end)->days;

            $prompt = $this->buildPrompt(
                $this->requestData['destination'],
                $duration,
                $this->requestData['budget'] ?? null,
                $this->requestData['preferences'] ?? []
            );

            Log::info('Calling OpenAI API for travel plan', ['plan_id' => $this->travelPlan->id]);

            $response = $client->chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a travel planning assistant. Create detailed travel itineraries with activities, accommodations, transportation, and meal recommendations.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $aiResponse = json_decode($response->choices[0]->message->content, true);

            if (!is_array($aiResponse)) {
                throw new \Exception("Invalid response format from OpenAI. Expected JSON array.");
            }

            Log::info('OpenAI response received, saving to database', ['plan_id' => $this->travelPlan->id]);

            $this->saveToDatabase($aiResponse, $prompt);

            // Update status to completed
            $this->travelPlan->update([
                'processing_status' => 'completed',
                'processing_completed_at' => now()
            ]);

            Log::info('Travel plan generation completed successfully', ['plan_id' => $this->travelPlan->id]);

        } catch (\Exception $e) {
            Log::error('Travel plan generation failed', [
                'plan_id' => $this->travelPlan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->travelPlan->update([
                'processing_status' => 'failed',
                'processing_error' => $e->getMessage(),
                'processing_completed_at' => now()
            ]);

            throw $e;
        }
    }

    private function buildPrompt($destination, $duration, $budget = null, $preferences = [])
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

    private function saveToDatabase($aiResponse, $prompt)
    {
        \DB::beginTransaction();

        try {
            // Update the travel plan with AI response data
            $this->travelPlan->update([
                'description' => $aiResponse['description'] ?? null,
                'ai_prompt_used' => json_encode($prompt),
                'ai_response' => json_encode($aiResponse)
            ]);

            // Save destinations
            if (isset($aiResponse['destinations']) && is_array($aiResponse['destinations'])) {
                foreach ($aiResponse['destinations'] as $position => $destData) {
                    Destination::create([
                        'travel_plan_id' => $this->travelPlan->id,
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

            // Save itineraries and activities
            if (isset($aiResponse['itineraries']) && is_array($aiResponse['itineraries'])) {
                foreach ($aiResponse['itineraries'] as $position => $itinData) {
                    $destination = null;
                    if (isset($itinData['destination'])) {
                        $destination = Destination::where('travel_plan_id', $this->travelPlan->id)
                            ->where('name', $itinData['destination'])
                            ->first();
                    }

                    $itinerary = Itinerary::create([
                        'travel_plan_id' => $this->travelPlan->id,
                        'destination_id' => $destination ? $destination->id : null,
                        'title' => $itinData['title'] ?? 'Day ' . ($position + 1),
                        'date' => $itinData['date'] ?? date('Y-m-d', strtotime($this->travelPlan->start_date . ' + ' . $position . ' days')),
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
            Log::info('Travel plan data saved successfully', ['plan_id' => $this->travelPlan->id]);

        } catch (\Exception $e) {
            \DB::rollback();
            Log::error('Failed to save travel plan data', ['plan_id' => $this->travelPlan->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
