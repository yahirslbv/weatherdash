<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[#0B132B] border border-[#1E2D56] rounded-xl font-bold text-xs text-slate-200 uppercase tracking-widest shadow-sm hover:bg-[#1E2D56] focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-[#15203D] disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
