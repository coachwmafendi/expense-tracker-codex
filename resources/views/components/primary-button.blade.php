<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-lg border border-transparent bg-[#c26b50] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-[#a85a43] focus:outline-none focus:ring-2 focus:ring-[#c26b50] focus:ring-offset-2 focus:ring-offset-[#fffaf2] active:bg-[#8f4b38] dark:focus:ring-offset-[#211d18]']) }}>
    {{ $slot }}
</button>
