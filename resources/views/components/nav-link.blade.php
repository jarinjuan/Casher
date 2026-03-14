@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#fbbf24] text-sm font-medium leading-5 t-primary focus:outline-none focus:border-[#fbbf24] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 t-secondary hover:text-[#fbbf24] hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
