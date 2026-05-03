<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | MitraPOS pemilik</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_20%_20%,#dbeafe_0%,#F8F9FA_40%,#dbeafe_100%)]">
        <main class="mx-auto grid min-h-screen w-full max-w-6xl place-items-center px-4 py-8 lg:px-8">
            <section class="w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-white/95 shadow-[0_24px_70px_-30px_rgba(15,23,42,0.35)] backdrop-blur lg:grid lg:grid-cols-[1.08fr_0.92fr]">
                <aside class="relative hidden overflow-hidden bg-gradient-to-br from-[#1E40AF] via-[#3B82F6] to-[#1E40AF] p-9 text-white lg:flex lg:flex-col">
                    <div class="absolute -left-20 -top-20 h-52 w-52 rounded-full bg-white/20 blur-2xl"></div>
                    <div class="absolute -bottom-16 -right-10 h-44 w-44 rounded-full bg-blue-900/20 blur-xl"></div>

                    <div class="relative z-10">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-100">MitraPOS pemilik Portal</p>
                        <h1 class="mt-4 max-w-md font-['Poppins'] text-4xl font-semibold leading-tight">Kelola operasional toko dalam satu command center.</h1>
                        <p class="mt-4 max-w-md text-sm text-blue-50/90">Pantau transaksi, approval barang masuk, dan kendali user dari dashboard pemilik yang lebih cepat dan fokus data.</p>
                    </div>

                    <div class="relative z-10 mt-10 grid gap-3">
                        <article class="rounded-xl border border-white/25 bg-white/10 px-4 py-3 backdrop-blur">
                            <p class="text-xs uppercase tracking-wide text-blue-100">Insight Hari Ini</p>
                            <p class="mt-1 text-2xl font-semibold">{{ $trxToday ?? 0 }} transaksi</p>
                        </article>
                        <article class="rounded-xl border border-white/25 bg-white/10 px-4 py-3 backdrop-blur">
                            <p class="text-xs uppercase tracking-wide text-blue-100">Approval Pending</p>
                            <p class="mt-1 text-2xl font-semibold">{{ $pendingApproval ?? 0 }} dokumen</p>
                        </article>
                    </div>
                </aside>

                <div class="p-6 sm:p-8 lg:p-10">
                    <div class="mb-7">
                        <div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#1E40AF] text-base font-extrabold text-white shadow-lg shadow-blue-200">M</div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sign In</p>
                        <h2 class="mt-2 font-['Poppins'] text-3xl font-semibold text-slate-800">Masuk ke Dashboard pemilik</h2>
                        <p class="mt-2 text-sm text-slate-600">Masuk menggunakan akun yang sudah tersedia dari seeder.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form class="space-y-4" method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <label class="block text-sm font-medium text-slate-700">Email
                            <input
                                class="field mt-1 !h-11 !w-full"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="pemilik@mitrapos.id"
                                autofocus
                                autocomplete="email"
                                required
                            >
                        </label>

                        <label class="block text-sm font-medium text-slate-700">Password
                            <input
                                class="field mt-1 !h-11 !w-full"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required
                            >
                        </label>

                        <div class="flex items-center justify-between gap-3 pt-1">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#1E40AF] focus:ring-[#3B82F6]">
                                Ingat saya
                            </label>
                            <a href="#" class="text-sm font-medium text-slate-500 hover:text-slate-700">Butuh bantuan?</a>
                        </div>

                        <button type="submit" class="btn w-full justify-center rounded-lg border-[#1E40AF] bg-[#1E40AF] py-2.5 text-white hover:bg-[#3B82F6]">Masuk Dashboard</button>
                    </form>

                </div>
            </section>
        </main>
    </body>
</html>


