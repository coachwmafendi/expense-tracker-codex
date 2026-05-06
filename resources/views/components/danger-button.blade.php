<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-lg border border-transparent bg-rose-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-rose-500 active:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-[#211d18]']) }}>
    {{ $slot }}
</button>
