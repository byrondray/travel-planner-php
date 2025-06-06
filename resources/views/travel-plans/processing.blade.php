@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Generating Your Travel Plan</h1>
                <p class="text-lg text-gray-600">{{ $travelPlan->title }}</p>
            </div>

            <!-- Loading Animation -->
            <div class="text-center mb-8" id="loading-section">
                <div class="relative">
                    <!-- Animated Globe -->
                    <div class="inline-block animate-spin rounded-full h-20 w-20 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
                    
                    <!-- Animated Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                        <div class="bg-blue-600 h-2 rounded-full animate-pulse" style="width: 0%" id="progress-bar"></div>
                    </div>
                    
                    <!-- Status Messages -->
                    <div class="space-y-2">
                        <p class="text-gray-700 font-medium" id="status-message">
                            Preparing your personalized travel experience...
                        </p>
                        <p class="text-sm text-gray-500" id="sub-message">
                            This may take up to 2 minutes
                        </p>
                    </div>
                </div>
            </div>

            <!-- Processing Steps -->
            <div class="space-y-4 mb-8">
                <div class="flex items-center space-x-3" id="step-1">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-gray-700">Analyzing your preferences</span>
                </div>
                
                <div class="flex items-center space-x-3" id="step-2">
                    <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                    </div>
                    <span class="text-gray-500">Researching destinations and activities</span>
                </div>
                
                <div class="flex items-center space-x-3" id="step-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <span class="text-gray-500">Creating your personalized itinerary</span>
                </div>
                
                <div class="flex items-center space-x-3" id="step-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <span class="text-gray-500">Finalizing recommendations</span>
                </div>
            </div>

            <!-- Error Section (hidden by default) -->
            <div class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6" id="error-section">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-red-900">Generation Failed</h3>
                        <p class="text-red-700 mt-1" id="error-message"></p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('travel-plans.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        Try Again
                    </a>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
let progress = 0;
const maxSteps = 4;
const statusMessages = [
    "Analyzing your preferences...",
    "Researching destinations and activities...",
    "Creating your personalized itinerary...",
    "Finalizing recommendations...",
    "Almost ready!"
];

function updateProgress() {
    const progressBar = document.getElementById('progress-bar');
    const statusMessage = document.getElementById('status-message');
    
    if (progress < 90) {
        progress += Math.random() * 15 + 5; // Random increment between 5-20
        if (progress > 90) progress = 90;
        
        progressBar.style.width = progress + '%';
        
        const stepIndex = Math.floor(progress / 25);
        if (stepIndex < statusMessages.length) {
            statusMessage.textContent = statusMessages[stepIndex];
            updateStepUI(stepIndex + 1);
        }
    }
}

function updateStepUI(step) {
    if (step > currentStep) {
        // Mark previous step as complete
        const prevStep = document.getElementById(`step-${currentStep}`);
        const prevCircle = prevStep.querySelector('div');
        const prevText = prevStep.querySelector('span');
        
        prevCircle.className = 'flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center';
        prevCircle.innerHTML = '<svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
        prevText.className = 'text-gray-700';
        
        // Update current step
        if (step <= maxSteps) {
            const currentStepEl = document.getElementById(`step-${step}`);
            const currentCircle = currentStepEl.querySelector('div');
            const currentText = currentStepEl.querySelector('span');
            
            currentCircle.className = 'flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center';
            currentCircle.innerHTML = '<div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>';
            currentText.className = 'text-gray-700 font-medium';
            
            currentStep = step;
        }
    }
}

function checkStatus() {
    fetch(`{{ route('travel-plans.status', $travelPlan->id) }}`)
        .then(response => response.json())
        .then(data => {
            if (data.processing_status === 'completed') {
                // Complete the progress bar
                document.getElementById('progress-bar').style.width = '100%';
                document.getElementById('status-message').textContent = 'Complete! Redirecting...';
                
                // Mark all steps as complete
                for (let i = 1; i <= maxSteps; i++) {
                    const step = document.getElementById(`step-${i}`);
                    const circle = step.querySelector('div');
                    const text = step.querySelector('span');
                    
                    circle.className = 'flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center';
                    circle.innerHTML = '<svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    text.className = 'text-gray-700';
                }
                
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1500);
                
            } else if (data.processing_status === 'failed') {
                document.getElementById('loading-section').classList.add('hidden');
                document.getElementById('error-section').classList.remove('hidden');
                document.getElementById('error-message').textContent = data.processing_error || 'An unexpected error occurred.';
            }
        })
        .catch(error => {
            console.error('Error checking status:', error);
        });
}

// Start progress animation
const progressInterval = setInterval(updateProgress, 1000);

// Check status every 3 seconds
const statusInterval = setInterval(checkStatus, 3000);

// Initial status check
checkStatus();

// Clean up intervals when page unloads
window.addEventListener('beforeunload', () => {
    clearInterval(progressInterval);
    clearInterval(statusInterval);
});
</script>
@endsection 