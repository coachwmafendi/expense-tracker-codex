@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg bg-[#fff8ef] px-3 py-2 text-start text-base font-medium text-[#9a4f39] shadow-sm shadow-black/20 transition duration-150 ease-in-out focus:outline-none'
            : 'block w-full rounded-lg px-3 py-2 text-start text-base font-medium text-[#f7eadb] transition duration-150 ease-in-out hover:bg-white/10 hover:text-white focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
