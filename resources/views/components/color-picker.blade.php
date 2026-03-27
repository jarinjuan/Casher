@props([
    'name' => 'color',
    'value' => '#fbbf24',
])

<div x-data="{ 
    selected: '{{ old($name, $value) }}',
    colors: [
        '#fbbf24', '#f59e0b', '#f97316', '#f43f5e', 
        '#ec4899', '#8b5cf6', '#6366f1', '#3b82f6', 
        '#0ea5e9', '#10b981', '#22c55e', '#6b7280'
    ]
}" class="flex flex-col gap-2">
    <input type="hidden" name="{{ $name }}" :value="selected">
    
    <div class="flex flex-wrap items-center gap-2 p-3 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 shadow-inner">
        <template x-for="color in colors" :key="color">
            <button 
                type="button"
                @click="selected = color"
                :class="selected === color ? 'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-[#09090b] ring-indigo-500 scale-110 shadow-lg' : 'hover:scale-105 opacity-80 hover:opacity-100 transition-transform'"
                class="w-7 h-7 rounded-lg transition-all duration-200 cursor-pointer border border-black/5 dark:border-white/10 flex items-center justify-center overflow-hidden"
                :style="'background-color: ' + color"
                :title="color"
            >
                <i class="fa-solid fa-check text-[10px] text-white drop-shadow-sm" x-show="selected === color"></i>
            </button>
        </template>
        
        {{-- Advanced Picker --}}
        <div class="relative group">
            <input 
                type="color" 
                x-model="selected"
                class="absolute inset-0 w-7 h-7 opacity-0 cursor-pointer z-10"
            />
            <button 
                type="button"
                :class="!colors.includes(selected) ? 'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-[#09090b] ring-indigo-500 scale-110' : ''"
                class="w-7 h-7 rounded-lg bg-white dark:bg-white/10 border border-gray-300 dark:border-white/20 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-white/20 transition-all duration-200"
                title="{{ __('Custom color') }}"
            >
                <i class="fa-solid fa-plus text-[10px] t-muted" x-show="colors.includes(selected)"></i>
                <i class="fa-solid fa-check text-[10px] t-primary" x-show="!colors.includes(selected)"></i>
            </button>
        </div>

        {{-- Selected Color Display --}}
        <div class="ml-auto flex items-center gap-2 py-1 px-2.5 bg-white dark:bg-white/10 rounded-lg border border-gray-200 dark:border-white/10 shadow-sm">
            <div class="w-3.5 h-3.5 rounded-full border border-black/5 dark:border-white/20 shadow-sm" :style="'background-color: ' + selected"></div>
            <span class="text-[10px] font-mono font-bold t-primary uppercase tracking-tighter" x-text="selected"></span>
        </div>
    </div>
</div>
