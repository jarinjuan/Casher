@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#fbbf24] text-start text-base font-medium text-[#d97706] dark:text-[#fbbf24] bg-amber-50 dark:bg-[#fbbf24]/5 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium t-secondary hover:text-[#fbbf24] hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
