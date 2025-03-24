<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Travel Plan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('travel-plans.generate') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-label for="title" value="Trip Title" />
                            <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')"
                                required autofocus />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="destination" value="Destination" />
                                <x-input id="destination" class="block mt-1 w-full" type="text" name="destination"
                                    :value="old('destination')" required placeholder="e.g. Paris, France" />
                            </div>

                            <div>
                                <x-label for="budget" value="Budget (USD)" />
                                <x-input id="budget" class="block mt-1 w-full" type="number" name="budget"
                                    :value="old('budget')" placeholder="e.g. 2000" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="start_date" value="Start Date" />
                                <x-input id="start_date" class="block mt-1 w-full" type="date" name="start_date"
                                    :value="old('start_date')" required />
                            </div>

                            <div>
                                <x-label for="end_date" value="End Date" />
                                <x-input id="end_date" class="block mt-1 w-full" type="date" name="end_date"
                                    :value="old('end_date')" required />
                            </div>
                        </div>

                        <div>
                            <x-label for="preferences" value="Travel Preferences" />
                            <div class="mt-1 space-y-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="accommodation_luxury" name="preferences[accommodation]"
                                            value="luxury" type="radio"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="accommodation_luxury" class="font-medium text-gray-700">Luxury
                                            accommodation</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="accommodation_mid" name="preferences[accommodation]"
                                            value="mid-range" type="radio"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                            checked>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="accommodation_mid" class="font-medium text-gray-700">Mid-range
                                            accommodation</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="accommodation_budget" name="preferences[accommodation]"
                                            value="budget" type="radio"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="accommodation_budget" class="font-medium text-gray-700">Budget
                                            accommodation</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-label value="Interests" />
                            <div class="mt-1 grid grid-cols-2 md:grid-cols-3 gap-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="interest_history" name="preferences[interests][]" value="history"
                                            type="checkbox"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="interest_history" class="font-medium text-gray-700">History</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="interest_art" name="preferences[interests][]" value="art"
                                            type="checkbox"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="interest_art" class="font-medium text-gray-700">Art</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="interest_food" name="preferences[interests][]" value="food"
                                            type="checkbox"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="interest_food" class="font-medium text-gray-700">Food</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="interest_nature" name="preferences[interests][]" value="nature"
                                            type="checkbox"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="interest_nature" class="font-medium text-gray-700">Nature</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="interest_adventure" name="preferences[interests][]" value="adventure"
                                            type="checkbox"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="interest_adventure"
                                            class="font-medium text-gray-700">Adventure</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="interest_relaxation" name="preferences[interests][]"
                                            value="relaxation" type="checkbox"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="interest_relaxation"
                                            class="font-medium text-gray-700">Relaxation</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-label for="additional_notes" value="Additional Notes" />
                            <textarea id="additional_notes" name="preferences[additional_notes]" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full border border-gray-300 rounded-md"
                                placeholder="Any specific requests or information you'd like to include..."></textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Generate Travel Plan') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>