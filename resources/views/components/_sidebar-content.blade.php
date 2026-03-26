{{-- Sidebar content partial (used by both desktop and mobile) --}}

{{-- Logo & Workspace --}}
<div class="px-4 pt-6 pb-4 border-b border-gray-200 dark:border-white/10">
    <div class="flex items-center gap-3 mb-4">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fbbf24] text-sm font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.2)]">
            C$
        </div>
        <div class="flex-1">
            <h1 class="text-sm font-bold t-primary">Casher</h1>
            <p class="text-xs t-muted">{{ __('Expense tracker') }}</p>
        </div>
    </div>

    @if($userTeams->count() > 1)
        <div x-data="{ open: false }" @click.away="open = false" class="relative">
            <button @click="open = !open" class="w-full px-3 py-2.5 text-left bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition group">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-medium t-muted uppercase tracking-widest mb-0.5">{{ __('Current Workspace') }}</p>
                        <p class="text-sm font-semibold t-primary truncate">{{ $currentTeam->name ?? __('No Workspace') }}</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs t-muted transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </div>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute left-0 right-0 mt-2 bg-white dark:bg-[#18181b] border border-gray-200 dark:border-white/10 rounded-lg shadow-2xl z-50 overflow-hidden"
                 style="display: none;">
                <div class="py-1 max-h-64 overflow-y-auto">
                    @foreach($userTeams as $team)
                        <form method="POST" action="{{ route('workspace.switch', $team->id) }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-white/5 transition {{ auth()->user()->current_team_id === $team->id ? 'bg-amber-50 dark:bg-[#fbbf24]/5' : '' }}">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-check text-xs text-[#fbbf24] {{ auth()->user()->current_team_id === $team->id ? 'opacity-100' : 'opacity-0' }}"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium t-primary truncate">{{ $team->name }}</p>
                                        <p class="text-xs t-muted">{{ $team->default_currency ?? 'CZK' }}</p>
                                    </div>
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    @elseif($currentTeam)
        <div class="px-3 py-2.5 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-200 dark:border-white/10">
            <p class="text-[10px] font-medium t-muted uppercase tracking-widest mb-0.5">{{ __('Workspace') }}</p>
            <p class="text-sm font-semibold t-primary">{{ $currentTeam->name }}</p>
        </div>
    @endif
</div>

{{-- Navigation --}}
<div class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
    @can('create', \App\Models\Transaction::class)
        <a href="{{ route('transactions.create') }}" class="block">
            <button class="w-full bg-[#fbbf24] hover:bg-[#f59e0b] text-black font-bold py-2.5 px-4 rounded-lg transition shadow-lg shadow-[#fbbf24]/10 flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="text-sm">{{ __('Add transaction') }}</span>
            </button>
        </a>
    @endcan

    <nav class="space-y-1 pt-2">
        @foreach($menuItems as $item)
            <a
                href="{{ $item['route'] }}"
                class="flex items-center px-3 py-2.5 text-sm rounded-lg transition font-medium
                    {{ $item['active']
                        ? 'bg-amber-50 dark:bg-[#fbbf24]/10 text-[#d97706] dark:text-[#fbbf24]'
                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white'
                    }}"
            >
                <i class="{{ $item['icon'] }} mr-3 text-sm w-5"></i>
                {{ __($item['label']) }}
            </a>
        @endforeach
    </nav>
</div>

{{-- Footer --}}
<div class="px-4 space-y-2 border-t border-gray-200 dark:border-white/10 pt-4">
    <a href="{{ route('workspace.settings') }}" class="flex items-center px-3 py-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition">
        <i class="fa-solid fa-cog text-sm mr-3 w-5"></i>
        <span>{{ __('Settings') }}</span>
    </a>

    <a href="{{ route('workspace.join') }}" class="flex items-center px-3 py-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition">
        <i class="fa-solid fa-link text-sm mr-3 w-5"></i>
        <span>{{ __('Join workspace') }}</span>
    </a>

    <div class="pt-2 border-t border-gray-200 dark:border-white/10">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-white/5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#fbbf24] text-xs font-bold text-black">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold t-primary truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs t-muted truncate">{{ auth()->user()->email }}</p>
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full flex items-center px-3 py-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium transition">
                <i class="fa-solid fa-sign-out-alt text-sm mr-3 w-5"></i>
                <span>{{ __('Log out') }}</span>
            </button>
        </form>
    </div>
</div>
