@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#1E2D56] bg-[#0B132B]/80 text-white placeholder:text-[#829AB1] focus:border-blue-400 focus:ring-blue-500/40 rounded-xl shadow-inner shadow-black/20']) }}>
