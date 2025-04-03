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
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="travel-form-container">
                        <form method="POST" action="{{ route('travel-plans.generate') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="title" class="block font-medium text-sm text-gray-700">Trip Title</label>
                                <input id="title"
                                    class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full"
                                    type="text" name="title" value="{{ old('title') }}" required autofocus>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="destination"
                                        class="block font-medium text-sm text-gray-700">Destination</label>
                                    <input id="destination"
                                        class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full"
                                        type="text" name="destination" value="{{ old('destination') }}" required
                                        placeholder="e.g. Paris, France">
                                </div>

                                <div>
                                    <label for="budget" class="block font-medium text-sm text-gray-700">Budget
                                        (USD)</label>
                                    <input id="budget"
                                        class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full"
                                        type="number" name="budget" value="{{ old('budget') }}" placeholder="e.g. 2000">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block font-medium text-sm text-gray-700">Start
                                        Date</label>
                                    <input id="start_date"
                                        class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full"
                                        type="date" name="start_date" value="{{ old('start_date') }}" required>
                                </div>

                                <div>
                                    <label for="end_date" class="block font-medium text-sm text-gray-700">End
                                        Date</label>
                                    <input id="end_date"
                                        class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full"
                                        type="date" name="end_date" value="{{ old('end_date') }}" required>
                                </div>
                            </div>

                            <div>
                                <label for="preferences" class="block font-medium text-sm text-gray-700">Travel
                                    Preferences</label>
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
                                <label class="block font-medium text-sm text-gray-700">Interests</label>
                                <div class="mt-1 grid grid-cols-2 md:grid-cols-3 gap-2">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="interest_history" name="preferences[interests][]" value="history"
                                                type="checkbox"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="interest_history"
                                                class="font-medium text-gray-700">History</label>
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
                                            <label for="interest_nature"
                                                class="font-medium text-gray-700">Nature</label>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="interest_adventure" name="preferences[interests][]"
                                                value="adventure" type="checkbox"
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
                                <label for="additional_notes" class="block font-medium text-sm text-gray-700">Additional
                                    Notes</label>
                                <textarea id="additional_notes" name="preferences[additional_notes]" rows="3"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full border border-gray-300 rounded-md"
                                    placeholder="Any specific requests or information you'd like to include..."></textarea>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4">
                                    {{ __('Generate Travel Plan') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-overlay"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-5 rounded-lg shadow-lg text-center">
            <svg class="animate-spin mx-auto h-10 w-10 text-indigo-600 mb-3" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <p class="text-gray-700 font-medium">Generating your travel plan...</p>
            <p class="text-gray-500 text-sm mt-2">This may take up to a minute.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const loadingOverlay = document.getElementById('loading-overlay');

            loadingOverlay.classList.add('hidden');

            form.addEventListener('submit', function (e) {
                loadingOverlay.classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>