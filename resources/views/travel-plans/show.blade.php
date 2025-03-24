<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $travelPlan->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('travel-plans.edit', $travelPlan->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Edit Plan
                </a>
                <form method="POST" action="{{ route('travel-plans.destroy', $travelPlan->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                        onclick="return confirm('Are you sure you want to delete this travel plan?')">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Travel Plan Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Overview</h3>
                            <p class="text-gray-600 mb-4">{{ $travelPlan->description }}</p>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <span class="block text-sm font-medium text-gray-700">Dates</span>
                                    <span class="block text-gray-900">
                                        {{ \Carbon\Carbon::parse($travelPlan->start_date)->format('M d, Y') }} -
                                        {{ \Carbon\Carbon::parse($travelPlan->end_date)->format('M d, Y') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-700">Status</span>
                                    <span
                                        class="inline-flex px-2 py-1 rounded-full text-xs {{ $travelPlan->status === 'completed' ? 'bg-green-100 text-green-800' : ($travelPlan->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $travelPlan->status)) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-700">Budget</span>
                                    <span class="block text-gray-900">
                                        {{ $travelPlan->budget ? '$' . number_format($travelPlan->budget, 2) . ' ' . $travelPlan->currency : 'Not specified' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Destinations</h3>
                            <ul class="divide-y divide-gray-200">
                                @forelse($travelPlan->destinations as $destination)
                                    <li class="py-2">
                                        <p class="font-medium">{{ $destination->name }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($destination->arrival_date)->format('M d') }} -
                                            {{ \Carbon\Carbon::parse($destination->departure_date)->format('M d') }}
                                        </p>
                                    </li>
                                @empty
                                    <li class="py-2 text-gray-500">No destinations specified</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itinerary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Itinerary</h3>

                    @forelse($travelPlan->itineraries->sortBy('date') as $itinerary)
                                    <div class="mb-8 border-b pb-6">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-sm font-medium">
                                                {{ \Carbon\Carbon::parse($itinerary->date)->format('D, M d, Y') }}
                                            </div>
                                            @if($itinerary->destination)
                                                <div class="ml-2 text-gray-500">{{ $itinerary->destination->name }}</div>
                                            @endif
                                        </div>

                                        <h4 class="text-xl font-bold mb-2">{{ $itinerary->title }}</h4>
                                        <p class="text-gray-600 mb-4">{{ $itinerary->description }}</p>

                                        <!-- Activities -->
                                        <div class="mb-4">
                                            <h5 class="font-medium text-gray-700 mb-2">Activities</h5>
                                            <div class="space-y-3">
                                                @forelse($itinerary->activities->sortBy('start_time') as $activity)
                                                    <div class="bg-white rounded-lg border p-3 shadow-sm">
                                                        <div class="flex justify-between">
                                                            <h6 class="font-medium">{{ $activity->title }}</h6>
                                                            <span class="text-sm text-gray-600">
                                                                {{ $activity->start_time ? \Carbon\Carbon::parse($activity->start_time)->format('g:i A') : '' }}
                                                                {{ ($activity->start_time && $activity->end_time) ? '-' : '' }}
                                                                {{ $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('g:i A') : '' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-gray-600 mt-1">{{ $activity->description }}</p>
                                                        <div class="mt-2 flex justify-between">
                                                            <span
                                                                class="text-xs bg-gray-100 px-2 py-1 rounded">{{ ucfirst($activity->type) }}</span>
                                                            @if($activity->cost)
                                                                <span class="text-sm">${{ number_format($activity->cost, 2) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-gray-500">No activities scheduled</p>
                                                @endforelse
                                            </div>
                                        </div>

                                        <!-- Accommodations -->
                                        @if($itinerary->accommodations)
                                                            <div class="mb-4">
                                                                <h5 class="font-medium text-gray-700 mb-2">Accommodation</h5>
                                                                @php
                                                                    $accommodation = json_decode($itinerary->accommodations);
                                                                @endphp
                                                                @if($accommodation)
                                                                    <div class="bg-white rounded-lg border p-3 shadow-sm">
                                                                        <h6 class="font-medium">{{ $accommodation->name ?? 'Accommodation' }}</h6>
                                                                        <p class="text-sm text-gray-600 mt-1">{{ $accommodation->description ?? '' }}</p>
                                                                        @if(isset($accommodation->address))
                                                                            <p class="text-sm text-gray-600 mt-1">{{ $accommodation->address }}</p>
                                                                        @endif
                                                                        @if(isset($accommodation->cost))
                                                                            <div class="mt-2 flex justify-end">
                                                                                <span class="text-sm">${{ number_format($accommodation->cost, 2) }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <p class="text-gray-500">No accommodation details</p>
                                                                @endif
                                                            </div>
                                        @endif

                                        <!-- Meals -->
                                        @if($itinerary->meals)
                                                            <div>
                                                                <h5 class="font-medium text-gray-700 mb-2">Meals</h5>
                                                                @php
                                                                    $meals = json_decode($itinerary->meals);
                                                                @endphp
                                                                @if(is_array($meals) && count($meals) > 0)
                                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                                        @foreach($meals as $meal)
                                                                            <div class="bg-white rounded-lg border p-3 shadow-sm">
                                                                                <div class="flex justify-between">
                                                                                    <h6 class="font-medium">{{ ucfirst($meal->type ?? 'Meal') }}</h6>
                                                                                </div>
                                                                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $meal->venue ?? '' }}</p>
                                                                                <p class="text-sm text-gray-600 mt-1">{{ $meal->description ?? '' }}</p>
                                                                                @if(isset($meal->cost))
                                                                                    <div class="mt-2 flex justify-end">
                                                                                        <span class="text-sm">${{ number_format($meal->cost, 2) }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <p class="text-gray-500">No meal details</p>
                                                                @endif
                                                            </div>
                                        @endif
                                    </div>
                    @empty
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-600">No itinerary details available yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>