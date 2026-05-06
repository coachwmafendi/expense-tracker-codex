<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-lg border border-[#d8cbb9] bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-zinc-700 shadow-sm transition duration-150 ease-in-out hover:bg-[#fbf5ec] focus:outline-none focus:ring-2 focus:ring-[#c26b50] focus:ring-offset-2 disabled:opacity-25 dark:border-[#4a3d32] dark:bg-[#1b1713] dark:text-zinc-200 dark:hover:bg-[#2a241e]']) }}>
    {{ $slot }}
</button>
