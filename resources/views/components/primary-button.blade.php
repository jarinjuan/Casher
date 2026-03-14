<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#fbbf24] border border-transparent rounded-lg font-bold text-xs text-black uppercase tracking-widest hover:bg-[#f59e0b] focus:bg-[#f59e0b] active:bg-[#d97706] focus:outline-none focus:ring-2 focus:ring-[#fbbf24] focus:ring-offset-2 focus:ring-offset-[#18181b] transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
