@props(['title', 'value', 'trend' => null, 'type' => 'neutral'])
<div class="card p-6 border-l-4 border-[#fbbf24]">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold t-muted uppercase tracking-widest">{{ $title }}</p>
            <h3 class="mt-1 text-2xl font-black t-primary">{{ $value }}</h3>

            @if($trend)
                <p class="mt-2 text-sm {{ str_contains($trend, '+') ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                    <span class="font-bold">{{ $trend }}</span>
                    <span class="t-muted text-xs ml-1 font-normal">vs. last month</span>
                </p>
            @endif
        </div>
        <div class="p-3 bg-gray-100 dark:bg-white/5 rounded-lg text-[#fbbf24]">
            {{ $slot }}
        </div>
    </div>
</div>