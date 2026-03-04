@props([
    'menuItems' => [],
])

<aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 h-screen fixed flex flex-col pb-4">
    <!-- Logo & Workspace Section -->
    <div class="px-4 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-400 text-sm font-bold text-black shadow-lg">
                C$
            </div>
            <div class="flex-1">
                <h1 class="text-sm font-bold text-gray-900 dark:text-white">Casher</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Expense Tracker</p>
            </div>
        </div>

        <!-- Workspace Switcher -->
        @php
            $userTeams = auth()->user()->teams()->get();
            if (auth()->user()->ownedTeams()->count() > 0) {
                $userTeams = $userTeams->merge(auth()->user()->ownedTeams())->unique('id');
            }
            $currentTeam = auth()->user()->currentTeam;
        @endphp
        
        @if($userTeams->count() > 1)
            <div x-data="{ open: false }" @click.away="open = false" class="relative">
                <button @click="open = !open" class="w-full px-3 py-2.5 text-left bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Current Workspace</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $currentTeam->name ?? 'No Workspace' }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute left-0 right-0 mt-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl z-50 overflow-hidden"
                     style="display: none;">
                    <div class="py-1 max-h-64 overflow-y-auto custom-scrollbar">
                        @foreach($userTeams as $team)
                            <form method="POST" action="{{ route('workspace.switch', $team->id) }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 transition group {{ auth()->user()->current_team_id === $team->id ? 'bg-amber-50 dark:bg-amber-900/20' : '' }}">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-check text-xs text-amber-500 {{ auth()->user()->current_team_id === $team->id ? 'opacity-100' : 'opacity-0' }}"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $team->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $team->default_currency ?? 'CZK' }}</p>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($currentTeam)
            <div class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Workspace</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $currentTeam->name }}</p>
            </div>
        @endif
    </div>

    <!-- Main Navigation -->
    <div class="flex-1 px-4 py-4 space-y-2 overflow-y-auto custom-scrollbar">
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
                margin: 8px 0;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #4b5563;
            }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #6b7280;
            }
        </style>
        <a href="{{ route('transactions.create') }}" class="block">
            <button class="w-full bg-amber-400 hover:bg-amber-500 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-gray-900 font-semibold py-2.5 px-4 rounded-lg transition shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="text-sm">Add Transaction</span>
            </button>
        </a>

        <nav class="space-y-1 pt-2">
            @foreach($menuItems as $item)
                <a
                    href="{{ $item['route'] }}"
                    class="flex items-center px-3 py-2.5 text-sm rounded-lg transition font-medium
                        {{ $item['active']
                            ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'
                        }}"
                >
                    <i class="{{ $item['icon'] }} mr-3 text-sm w-5"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Footer Section -->
    <div class="px-4 space-y-2 border-t border-gray-200 dark:border-gray-700 pt-4">
        <a href="{{ route('workspace.settings') }}" class="flex items-center px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition">
            <i class="fa-solid fa-cog text-sm mr-3 w-5"></i>
            <span>Settings</span>
        </a>
        
        <a href="{{ route('workspace.join') }}" class="flex items-center px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition">
            <i class="fa-solid fa-link text-sm mr-3 w-5"></i>
            <span>Join Workspace</span>
        </a>

        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium transition">
                    <i class="fa-solid fa-sign-out-alt text-sm mr-3 w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
