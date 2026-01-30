@props([
    'menuItems' => [],
])

<aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 h-screen fixed flex flex-col justify-between pb-4">
    <div class="h-16 flex items-center px-6">
        <span class="text-2xl font-bold text-indigo-600">Casher</span>
    </div>

    <div class="flex-1 px-4 space-y-2 mt-4 flex flex-col">
        <!-- Workspace Switcher -->
        @php
            $userTeams = auth()->user()->teams()->get();
            if (auth()->user()->ownedTeams()->count() > 0) {
                $userTeams = $userTeams->merge(auth()->user()->ownedTeams());
            }
        @endphp
        
        @if($userTeams->count() > 1)
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 uppercase">Workspaces</label>
                <div class="relative group">
                    <button class="w-full px-3 py-2 text-left text-sm font-medium bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center justify-between">
                        <span class="truncate">{{ auth()->user()->currentTeam->name ?? 'No Workspace' }}</span>
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    
                    <div class="hidden group-hover:block absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-50">
                        @foreach($userTeams as $team)
                            <form method="POST" action="{{ route('workspace.switch', $team->id) }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition {{ auth()->user()->current_team_id === $team->id ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-check text-xs {{ auth()->user()->current_team_id === $team->id ? 'opacity-100' : 'opacity-0' }}"></i>
                                        {{ $team->name }}
                                    </span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <a href="{{ route('transactions.create') }}" class="block">
            <button class="w-full bg-amber-400 hover:bg-amber-500 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-gray-900 font-semibold py-2 px-4 rounded-lg transition mb-4 flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Add transaction
            </button>
        </a>

        <nav class="flex-1 space-y-2">
            @foreach($menuItems as $item)
                <a
                    href="{{ $item['route'] }}"
                    class="flex items-center px-4 py-3 rounded-lg transition
                        {{ $item['active']
                            ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400'
                        }}"
                >
                    <i class="{{ $item['icon'] }} mr-3"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="px-4">
    <div class="space-y-2 mb-4">
        <a href="{{ route('workspace.settings') }}" class="block">
            <button class="w-full text-left px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-cog"></i>
                Workspace Settings
            </button>
        </a>
        <a href="{{ route('workspace.join') }}" class="block">
            <button class="w-full text-left px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-link"></i>
                Join Workspace
            </button>
        </a>
    </div>
    <a href="{{ route('profile.edit') }}" class="block">
        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition">
            <img
                src="{{ auth()->user()->avatar_url }}"
                alt="User"
                class="w-10 h-10 rounded-full"
            >
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->currentTeam->name ?? 'No Workspace' }}</p>
            </div>
        </div>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium transition flex items-center gap-2">
            <i class="fa-solid fa-sign-out-alt"></i>
            Logout
        </button>
    </form>
</div>

</aside>
