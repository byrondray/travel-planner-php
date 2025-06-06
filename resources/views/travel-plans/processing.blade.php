@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-3xl mx-auto">
            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-12 text-white">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold mb-2">Creating Your Perfect Journey</h1>
                        <p class="text-xl text-blue-100">{{ $travelPlan->title }}</p>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-8 sm:p-12">
                    <!-- Loading Section -->
                    <div class="space-y-8" id="loading-section">
                        <!-- Animated Icon -->
                        <div class="flex justify-center">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full border-4 border-gray-200"></div>
                                <div class="absolute top-0 left-0 w-24 h-24 rounded-full border-4 border-transparent border-t-blue-600 animate-spin"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-3">
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500 ease-out" 
                                     style="width: 0%" 
                                     id="progress-bar">
                                    <div class="h-full bg-white/30 animate-pulse"></div>
                                </div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500">
                                <span id="progress-percent">0%</span>
                                <span id="time-estimate">Est. 2 minutes</span>
                            </div>
                        </div>

                        <!-- Status Messages -->
                        <div class="text-center space-y-4">
                            <p class="text-lg font-medium text-gray-800" id="status-message">
                                ✨ Analyzing your travel preferences...
                            </p>
                            <div class="space-y-2 text-sm text-gray-600" id="status-details">
                                <p class="transition-opacity duration-500 opacity-0" data-step="1">
                                    🗺️ Researching destinations and attractions
                                </p>
                                <p class="transition-opacity duration-500 opacity-0" data-step="2">
                                    🏨 Finding the best accommodations
                                </p>
                                <p class="transition-opacity duration-500 opacity-0" data-step="3">
                                    🍴 Discovering local cuisine and restaurants
                                </p>
                                <p class="transition-opacity duration-500 opacity-0" data-step="4">
                                    📅 Creating your personalized itinerary
                                </p>
                            </div>
                        </div>

                        <!-- Tips While Waiting -->
                        <div class="mt-8 p-6 bg-blue-50 rounded-xl">
                            <h3 class="text-sm font-semibold text-blue-900 mb-2">Did you know?</h3>
                            <p class="text-sm text-blue-700" id="travel-tip">
                                Our AI analyzes thousands of travel experiences to create the perfect itinerary tailored just for you!
                            </p>
                        </div>
                    </div>

                    <!-- Error Section -->
                    <div class="hidden" id="error-section">
                        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-red-900">Unable to Generate Travel Plan</h3>
                                    <p class="text-red-700 mt-1" id="error-message">An unexpected error occurred.</p>
                                    <div class="mt-4 space-x-3">
                                        <a href="{{ route('travel-plans.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                            Try Again
                                        </a>
                                        <a href="{{ route('dashboard') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-white text-red-600 text-sm font-medium rounded-lg border-2 border-red-600 hover:bg-red-50 transition-colors">
                                            Back to Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Success Section (hidden by default) -->
                    <div class="hidden" id="success-section">
                        <div class="text-center space-y-6">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">Your Travel Plan is Ready!</h3>
                                <p class="text-gray-600 mt-2">Get ready for an amazing adventure</p>
                            </div>
                            <div class="inline-flex items-center space-x-1 text-sm text-gray-500">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Redirecting...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
                    <div class="text-center">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Travel tips to rotate
const travelTips = [
    "Our AI analyzes thousands of travel experiences to create the perfect itinerary tailored just for you!",
    "We're checking real-time data for the best local experiences and hidden gems at your destination.",
    "Your personalized travel plan will include restaurant recommendations based on your preferences.",
    "We're optimizing your daily schedules to minimize travel time and maximize enjoyment!",
    "Our system considers weather patterns and seasonal events for your travel dates."
];

let currentTip = 0;
let progress = 0;
let stepProgress = 0;

// Rotate tips
function rotateTips() {
    currentTip = (currentTip + 1) % travelTips.length;
    const tipElement = document.getElementById('travel-tip');
    tipElement.style.opacity = '0';
    setTimeout(() => {
        tipElement.textContent = travelTips[currentTip];
        tipElement.style.opacity = '1';
    }, 300);
}

// Show status steps progressively
function showNextStep() {
    stepProgress++;
    if (stepProgress <= 4) {
        const step = document.querySelector(`[data-step="${stepProgress}"]`);
        if (step) {
            step.classList.remove('opacity-0');
            step.classList.add('opacity-100');
        }
    }
}

// Update progress bar
function updateProgress() {
    if (progress < 90) {
        progress += Math.random() * 15;
        progress = Math.min(progress, 90);
        document.getElementById('progress-bar').style.width = progress + '%';
        document.getElementById('progress-percent').textContent = Math.round(progress) + '%';
    }
}

// Update status messages
const statusMessages = [
    "✨ Analyzing your travel preferences...",
    "🌍 Exploring destination possibilities...",
    "📍 Mapping out the perfect route...",
    "🎯 Personalizing your experience...",
    "🔄 Finalizing your itinerary..."
];

let currentStatus = 0;
function updateStatusMessage() {
    currentStatus = (currentStatus + 1) % statusMessages.length;
    document.getElementById('status-message').textContent = statusMessages[currentStatus];
}

// Start animations
setInterval(rotateTips, 5000);
setInterval(showNextStep, 3000);
setInterval(updateProgress, 2000);
setInterval(updateStatusMessage, 4000);

// Show first step immediately
showNextStep();

// Start processing immediately when page loads
function startProcessing() {
    fetch(`{{ route('travel-plans.process', $travelPlan->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.processing_status === 'completed') {
            // Show success state
            document.getElementById('loading-section').classList.add('hidden');
            document.getElementById('success-section').classList.remove('hidden');
            document.getElementById('progress-bar').style.width = '100%';
            document.getElementById('progress-percent').textContent = '100%';
            
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 2000);
        } else if (!data.success || data.processing_status === 'failed') {
            document.getElementById('loading-section').classList.add('hidden');
            document.getElementById('error-section').classList.remove('hidden');
            document.getElementById('error-message').textContent = 
                data.error || 'An unexpected error occurred while generating your travel plan.';
        }
    })
    .catch(error => {
        console.error('Error starting processing:', error);
        document.getElementById('loading-section').classList.add('hidden');
        document.getElementById('error-section').classList.remove('hidden');
        document.getElementById('error-message').textContent = 
            'An unexpected error occurred while generating your travel plan.';
    });
}

// Check status (fallback for cases where processing is already started)
function checkStatus() {
    fetch(`{{ route('travel-plans.status', $travelPlan->id) }}`)
        .then(response => response.json())
        .then(data => {
            if (data.processing_status === 'completed') {
                // Show success state
                document.getElementById('loading-section').classList.add('hidden');
                document.getElementById('success-section').classList.remove('hidden');
                document.getElementById('progress-bar').style.width = '100%';
                document.getElementById('progress-percent').textContent = '100%';
                
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 2000);
                
            } else if (data.processing_status === 'failed') {
                document.getElementById('loading-section').classList.add('hidden');
                document.getElementById('error-section').classList.remove('hidden');
                document.getElementById('error-message').textContent = 
                    data.processing_error || 'An unexpected error occurred while generating your travel plan.';
            }
        })
        .catch(error => {
            console.error('Error checking status:', error);
        });
}

// Start processing immediately if status is 'pending'
@if($travelPlan->processing_status === 'pending')
    startProcessing();
@elseif($travelPlan->processing_status === 'processing')
    // Check status every 3 seconds if already processing
    const statusInterval = setInterval(checkStatus, 3000);
    checkStatus(); // Initial check
@endif

// Clean up interval when page unloads
window.addEventListener('beforeunload', () => {
    clearInterval(statusInterval);
});

// Add smooth transitions
document.getElementById('travel-tip').style.transition = 'opacity 0.3s ease-in-out';
</script>

<style>
    /* Add subtle animation to the progress bar */
    @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: 200px 0; }
    }
    
    #progress-bar > div {
        background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
    }
</style>
@endsection