@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-50 dark:bg-white/5 border-gray-300 dark:border-white/10 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#fbbf24] focus:ring-[#fbbf24] rounded-lg shadow-sm transition-colors']) }}>
