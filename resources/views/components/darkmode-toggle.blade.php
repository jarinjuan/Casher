@props([])

<div x-data="{
    dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && true),
    toggle() {
        this.dark = !this.dark;
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', this.dark);
    },
    init() {
        document.documentElement.classList.toggle('dark', this.dark);
    }
}">
    <button @click="toggle()" class="group relative inline-flex h-8 w-16 items-center rounded-full border border-white/10 p-0.5 transition-colors hover:border-white/20 bg-white/5">
        <span class="flex h-7 w-7 transform items-center justify-center rounded-full text-gray-400 transition-transform duration-300 ease-in-out"
              :class="dark ? 'translate-x-8' : 'translate-x-0'">
            <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </span>
    </button>
</div>
