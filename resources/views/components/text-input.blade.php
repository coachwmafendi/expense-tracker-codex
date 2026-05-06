@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-[#d8cbb9] bg-white text-zinc-900 shadow-sm placeholder:text-zinc-400 focus:border-[#c26b50] focus:ring-[#c26b50] dark:border-[#4a3d32] dark:bg-[#1b1713] dark:text-zinc-100 dark:placeholder:text-zinc-500']) }}>
