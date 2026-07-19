<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | MitraPOS</title>

        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-slate-50">
        <main class="grid min-h-screen lg:grid-cols-[1fr_420px]">
            {{-- Left: Brand Panel --}}
            <aside class="hidden items-center justify-center bg-[#1E40AF] p-12 lg:flex">
                <div class="max-w-md">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-lg font-bold text-white">M</div>
                        <h1 class="text-2xl font-bold leading-tight text-white">MitraPOS</h1>
                    </div>
                </div>
            </aside>

            {{-- Right: Login Form --}}
            <div class="flex items-center justify-center px-6 py-12">
                <div class="w-full max-w-sm">
                    <div class="mb-8 flex items-center gap-3">
                        <div class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#1E40AF] text-sm font-bold text-white">M</div>
                        <h2 class="text-2xl font-bold text-slate-900">Masuk ke dashboard</h2>
                    </div>

                    @if ($errors->any())
                        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form class="space-y-4" method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <label class="block text-sm font-medium text-slate-700">Email
                            <input
                                class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition-colors placeholder:text-slate-500 focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/10"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="nama@toko.com"
                                required
                                autofocus
                            >
                        </label>

                        <label class="block text-sm font-medium text-slate-700">Password
                            <input
                                class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition-colors placeholder:text-slate-500 focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/10"
                                type="password"
                                name="password"
                                placeholder="Masukkan password"
                                required
                            >
                        </label>

                        <button
                            type="submit"
                            class="mt-2 w-full rounded-lg bg-[#1E40AF] px-4 py-2.5 text-sm font-semibold text-white transition-all hover:bg-[#1d4ed8] active:scale-[0.98]"
                        >
                            Masuk
                        </button>
                    </form>

                    <p class="mt-8 text-center text-xs text-slate-400">MitraPOS &copy; {{ date('Y') }}</p>
                </div>
            </div>
        </main>
    </body>
</html>
