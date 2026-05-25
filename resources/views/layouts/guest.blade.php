<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php($appName = config('app.name', 'WeatherDash'))
        <title>{{ $appName === 'Laravel' ? 'WeatherDash' : $appName }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @unless (request()->routeIs('login'))
            <style>
                .auth-panel label {
                    color: #e2e8f0 !important;
                    font-weight: 700;
                }

                .auth-panel input[type="email"],
                .auth-panel input[type="password"],
                .auth-panel input[type="text"] {
                    width: 100%;
                    border: 1px solid #1E2D56;
                    border-radius: 1rem;
                    background: rgba(11, 19, 43, 0.8);
                    color: #fff;
                    padding: 0.75rem 1rem;
                    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.18);
                }

                .auth-panel input[type="email"]:focus,
                .auth-panel input[type="password"]:focus,
                .auth-panel input[type="text"]:focus {
                    border-color: #60a5fa;
                    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.35);
                    outline: none;
                }

                .auth-panel input[type="checkbox"] {
                    border-color: #1E2D56;
                    background: #0B132B;
                    color: #2563eb;
                }

                .auth-panel .text-gray-600,
                .auth-panel .text-gray-700,
                .auth-panel .text-gray-800 {
                    color: #829AB1 !important;
                }

                .auth-panel label.text-gray-700 {
                    color: #e2e8f0 !important;
                }

                .auth-panel a {
                    color: #bfdbfe;
                    font-weight: 700;
                }

                .auth-panel a:hover {
                    color: #dbeafe;
                }

                .auth-panel button[type="submit"] {
                    border-radius: 1rem;
                    background: #2563eb;
                    color: #fff;
                    padding: 0.75rem 1.25rem;
                    box-shadow: 0 18px 30px rgba(23, 37, 84, 0.35);
                }

                .auth-panel button[type="submit"]:hover {
                    background: #3b82f6;
                }
            </style>
        @endunless
    </head>
    <body class="font-sans text-slate-100 antialiased bg-[#0B132B]">
        <div class="min-h-screen weatherdash-gradient">
            <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="relative hidden overflow-hidden border-r border-white/10 px-12 py-10 lg:flex lg:flex-col lg:justify-between">
                    <a href="/" class="inline-flex items-center gap-3 text-2xl font-extrabold tracking-tight text-white">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl border border-blue-300/30 bg-blue-500/15 text-2xl shadow-lg shadow-blue-950/30">
                            <svg class="h-7 w-7 text-blue-200" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6.5 17.5H17a4 4 0 0 0 .8-7.92 6 6 0 0 0-11.36-1.6A4.75 4.75 0 0 0 6.5 17.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 21h8M11 4V2.5M4.5 8.5 3.3 7.3M19.5 8.5l1.2-1.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        Weather<span class="text-blue-400">Dash</span>
                    </a>

                    <div class="max-w-xl">
                        <p class="text-sm font-bold uppercase tracking-[0.28em] text-teal-200/80">Clima en tiempo real</p>
                        <h1 class="mt-5 text-5xl font-extrabold leading-tight tracking-tight text-white">
                            Consulta tus ciudades sin perder de vista el cielo.
                        </h1>
                        <p class="mt-6 max-w-lg text-lg font-medium leading-8 text-slate-300">
                            Guarda lugares, compara condiciones y revisa el pronostico desde un panel rapido y limpio.
                        </p>
                    </div>

                    <div class="grid max-w-xl grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/20 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-wider text-[#829AB1]">Hoy</p>
                            <p class="mt-2 text-3xl font-light text-white">24<span class="text-base text-blue-200">C</span></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/20 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-wider text-[#829AB1]">Humedad</p>
                            <p class="mt-2 text-3xl font-light text-white">68<span class="text-base text-blue-200">%</span></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-black/20 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-wider text-[#829AB1]">Viento</p>
                            <p class="mt-2 text-3xl font-light text-white">12<span class="text-base text-blue-200">km/h</span></p>
                        </div>
                    </div>
                </section>

                <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-12">
                    <div class="w-full max-w-md">
                        <a href="/" class="mb-8 inline-flex items-center gap-3 text-2xl font-extrabold tracking-tight text-white lg:hidden">
                            <span class="grid h-11 w-11 place-items-center rounded-2xl border border-blue-300/30 bg-blue-500/15 text-2xl shadow-lg shadow-blue-950/30">
                                <svg class="h-7 w-7 text-blue-200" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6.5 17.5H17a4 4 0 0 0 .8-7.92 6 6 0 0 0-11.36-1.6A4.75 4.75 0 0 0 6.5 17.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 21h8M11 4V2.5M4.5 8.5 3.3 7.3M19.5 8.5l1.2-1.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            Weather<span class="text-blue-400">Dash</span>
                        </a>

                        <div class="auth-panel w-full overflow-hidden rounded-[28px] border border-white/10 bg-[#15203D]/95 p-6 shadow-2xl shadow-black/40 sm:p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
