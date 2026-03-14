<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-lg font-bold text-xs text-red-400 uppercase tracking-widest hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-[#18181b] transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
