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
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Create New Plan
                        </a>
                    </div>

                    @if(count($travelPlans ?? []) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($travelPlans as $plan)
                                <div class="bg-white rounded-lg border shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-4">
                                        <h4 class="font-bold text-xl mb-2">{{ $plan->title }}</h4>
                                        <p class="text-gray-600 text-sm mb-2">
                                            {{ \Carbon\Carbon::parse($plan->start_date)->format('M d, Y') }} -
                                            {{ \Carbon\Carbon::parse($plan->end_date)->format('M d, Y') }}
                                        </p>
                                        <div class="flex justify-between items-center mt-4">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs {{ $plan->status === 'completed' ? 'bg-green-100 text-green-800' : ($plan->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $plan->status)) }}
                                            </span>
                                            <a href="{{ route('travel-plans.show', $plan->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900">View Details →</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-600 mb-4">You haven't created any travel plans yet.</p>
                            <a href="{{ route('travel-plans.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Create Your First Plan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>