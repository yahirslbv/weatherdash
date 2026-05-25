<x-guest-layout>
    <div class="mb-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-teal-300/20 bg-teal-400/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-teal-200">
            <span class="h-2 w-2 rounded-full bg-teal-300"></span>
            Acceso seguro
        </div>

        <h2 class="text-3xl font-extrabold tracking-tight text-white">Inicia sesion</h2>
        <p class="mt-2 text-sm font-medium leading-6 text-[#829AB1]">
            Entra a tu panel para revisar tus lugares guardados y el pronostico.
        </p>
    </div>

    <x-auth-session-status class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-200" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-bold text-slate-200">Correo electronico</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#829AB1]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4.5 7.5 12 12.75l7.5-5.25M6 18h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <input id="email" class="block w-full rounded-2xl border border-[#1E2D56] bg-[#0B132B]/80 py-3 pl-12 pr-4 text-white shadow-inner shadow-black/20 placeholder:text-[#829AB1] focus:border-blue-400 focus:ring-2 focus:ring-blue-500/40" type="email" name="email" value="{{ old('email') }}" placeholder="tu@correo.com" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm font-semibold text-red-300" />
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-bold text-slate-200">Contrasena</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#829AB1]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M7 10V8a5 5 0 0 1 10 0v2M6.5 21h11a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-11a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 15v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <input id="password" class="block w-full rounded-2xl border border-[#1E2D56] bg-[#0B132B]/80 py-3 pl-12 pr-4 text-white shadow-inner shadow-black/20 placeholder:text-[#829AB1] focus:border-blue-400 focus:ring-2 focus:ring-blue-500/40" type="password" name="password" placeholder="Tu clave de acceso" required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm font-semibold text-red-300" />
        </div>

        <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 font-semibold text-[#829AB1]">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-[#1E2D56] bg-[#0B132B] text-blue-500 focus:ring-blue-500 focus:ring-offset-[#15203D]" name="remember">
                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="font-bold text-blue-300 transition hover:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-[#15203D]" href="{{ route('password.request') }}">
                    Olvide mi contrasena
                </a>
            @endif
        </div>

        <button type="submit" class="group inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3.5 text-sm font-extrabold uppercase tracking-wider text-white shadow-lg shadow-blue-950/40 transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:ring-offset-[#15203D] active:scale-[0.99]">
            Entrar al dashboard
            <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        @if (Route::has('register'))
            <p class="pt-2 text-center text-sm font-semibold text-[#829AB1]">
                No tienes cuenta?
                <a href="{{ route('register') }}" class="text-blue-300 transition hover:text-blue-200">Crear cuenta</a>
            </p>
        @endif
    </form>
</x-guest-layout>
