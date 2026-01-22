@extends('dashboard')
@props(['title', 'value', 'trend' => null, 'type' => 'neutral']) 
<div class="bg-slate-800 border-l-4 border-yellow-400 p-6 rounded-xl shadow-lg">
    <div class="flex items-center justify-between"> 
        <div> 
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $title }}</p>
            <h3 class="mt-1 text-2xl font-black text-white">{{ $value }}</h3>
            
            @if($trend) 
                <p class="mt-2 text-sm {{ str_contains($trend, '+') ? 'text-green-400' : 'text-red-400' }}"> 
                    <span class="font-bold">{{ $trend }}</span> 
                    <span class="text-slate-500 text-xs ml-1 font-normal">vs. min. měsíc</span>
                </p> 
            @endif
        </div> 
        <div class="p-3 bg-slate-700/50 rounded-lg text-yellow-400">
            {{ $slot }}
        </div> 
    </div> 
</div> 

<!-- Použití v gridu 
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 bg-slate-900 p-6">
    <x-stats-card title="Celkový zůstatek" value="125 450 Kč" trend="+2.4%" /> 
    <x-stats-card title="Měsíční výdaje" value="18 200 Kč" trend="-12%" />
    <x-stats-card title="Investice" value="45 000 Kč" trend="+5.8%" /> 
    <x-stats-card title="Kurz EUR" value="25.34 Kč" /> 
</div> -->