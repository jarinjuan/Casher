@props([
    'menuItems' => [],
])

@php
    $userTeams = auth()->user()->teams()->get();
    if (auth()->user()->ownedTeams()->count() > 0) {
        $userTeams = $userTeams->merge(auth()->user()->ownedTeams())->unique('id');
    }
    $currentTeam = auth()->user()->currentTeam;
@endphp

{{-- Překrytí pro mobil --}}
<div id="mobile-sidebar" class="hidden lg:hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('mobile-sidebar').classList.add('hidden')"></div>
    <aside class="relative w-72 h-full bg-white dark:bg-[#18181b] border-r border-gray-200 dark:border-white/10 flex flex-col pb-4 overflow-y-auto">
        @include('components._sidebar-content', ['menuItems' => $menuItems, 'userTeams' => $userTeams, 'currentTeam' => $currentTeam])
    </aside>
</div>

{{-- Postranní panel pro desktop --}}
<aside class="hidden lg:flex w-64 bg-white dark:bg-[#18181b] border-r border-gray-200 dark:border-white/10 h-screen fixed flex-col pb-4 z-40">
    @include('components._sidebar-content', ['menuItems' => $menuItems, 'userTeams' => $userTeams, 'currentTeam' => $currentTeam])
</aside>
