<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Analysis') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <p class="t-secondary">{{ __("Data analysis of expenses of user " ) }}{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
