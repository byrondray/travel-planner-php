<?php

namespace App\Jobs;

use App\Models\TravelPlan;
use App\Services\TravelPlanGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTravelPlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180;
    public $tries = 2;

    protected $travelPlan;
    protected $requestData;

    public function __construct(TravelPlan $travelPlan, array $requestData)
    {
        $this->travelPlan = $travelPlan;
        $this->requestData = $requestData;
    }

    public function handle(TravelPlanGeneratorService $service): void
    {
        try {
            Log::info('Starting travel plan generation job', ['plan_id' => $this->travelPlan->id]);

            $service->generate($this->travelPlan, $this->requestData);

            Log::info('Travel plan generation completed successfully', ['plan_id' => $this->travelPlan->id]);

        } catch (\Exception $e) {
            Log::error('Travel plan generation failed', [
                'plan_id' => $this->travelPlan->id,
                'error' => $e->getMessage(),
            ]);

            $this->travelPlan->update([
                'processing_status' => 'failed',
                'processing_error' => $e->getMessage(),
                'processing_completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
