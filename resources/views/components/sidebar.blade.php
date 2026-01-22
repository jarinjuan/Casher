@props([
    'menuItems' => [],
])

<aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 h-screen fixed flex flex-col justify-between pb-4">
    <div class="h-16 flex items-center px-6">
        <span class="text-2xl font-bold text-indigo-600">Casher</span>
    </div>

    <nav class="flex-1 px-4 space-y-2 mt-4">
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

    <div class="px-4">
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
                <p class="text-xs text-gray-500 dark:text-gray-400">Member</p>
            </div>
        </div>
    </a>
</div>

</aside>
