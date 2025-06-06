<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TravelPlanController;
use App\Http\Controllers\OpenAIController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [TravelPlanController::class, 'index'])->name('dashboard');

    Route::resource('travel-plans', TravelPlanController::class);

    Route::post('/travel-plans/generate', [OpenAIController::class, 'generateTravelPlan'])->name('travel-plans.generate');
    Route::get('/travel-plans/{travelPlan}/processing', [TravelPlanController::class, 'processing'])->name('travel-plans.processing');
    Route::get('/travel-plans/{travelPlan}/status', [TravelPlanController::class, 'status'])->name('travel-plans.status');
    Route::post('/travel-plans/{travelPlan}/process', [OpenAIController::class, 'processOpenAI'])->name('travel-plans.process');
    Route::post('/travel-plans/{travelPlan}/regenerate-section', [OpenAIController::class, 'regeneratePlanSection'])
        ->name('travel-plans.regenerate-section');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';