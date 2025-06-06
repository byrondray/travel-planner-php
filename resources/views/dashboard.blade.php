<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Your Travel Plans</h3>
                        <a href="{{ route('travel-plans.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition-colors">
                            Create New Plan
                        </a>
                    </div>

                    @if(isset($travelPlans) && count($travelPlans) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($travelPlans as $plan)
                                <div class="bg-white rounded-xl border shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-6 h-full flex flex-col">
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-2">
                                                <h4 class="font-bold text-xl">{{ $plan->title }}</h4>
                                                @if(isset($plan->processing_status) && in_array($plan->processing_status, ['pending', 'processing']))
                                                    <div class="flex-shrink-0 ml-2">
                                                        <div class="w-5 h-5 rounded-full border-2 border-transparent border-t-blue-600 animate-spin"></div>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-gray-600 text-sm mb-4">
                                                {{ \Carbon\Carbon::parse($plan->start_date)->format('M d, Y') }} -
                                                {{ \Carbon\Carbon::parse($plan->end_date)->format('M d, Y') }}
                                            </p>
                                        </div>

                                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                            @if(isset($plan->processing_status) && in_array($plan->processing_status, ['pending', 'processing']))
                                                <span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800 font-medium">
                                                    {{ $plan->processing_status === 'pending' ? '⏳ Queued' : '🔄 Generating...' }}
                                                </span>
                                                <a href="{{ route('travel-plans.processing', $plan->id) }}"
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    View Progress →
                                                </a>
                                            @elseif(isset($plan->processing_status) && $plan->processing_status === 'failed')
                                                <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-800 font-medium">
                                                    ❌ Generation Failed
                                                </span>
                                                <a href="{{ route('travel-plans.create') }}"
                                                   class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    Try Again →
                                                </a>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs {{ $plan->status === 'completed' ? 'bg-green-100 text-green-800' : ($plan->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }} font-medium">
                                                    {{ $plan->status === 'completed' ? '✅ Completed' : ucfirst(str_replace('_', ' ', $plan->status)) }}
                                                </span>
                                                <a href="{{ route('travel-plans.show', $plan->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                    View Details →
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Travel Plans Yet</h3>
                            <p class="text-gray-600 mb-6">You haven't created any travel plans yet. Start planning your next adventure!</p>
                            <a href="{{ route('travel-plans.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-medium text-white hover:bg-indigo-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Your First Plan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh page every 15 seconds if there are processing plans
        @if(isset($travelPlans))
            @php $hasProcessing = $travelPlans->where('processing_status', 'processing')->count() > 0; @endphp
            @if($hasProcessing)
                setTimeout(() => {
                    window.location.reload();
                }, 15000);
            @endif
        @endif
    </script>
</x-app-layout>