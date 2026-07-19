<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Masuk - Kasir Tembakau</title>

    @if (app()->environment('local') || app()->environment('development'))
        @vite(['resources/css/app.css'])
    @endif

    <style>
        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .login-background {
            background:
                radial-gradient(circle at 15% 20%, rgba(198, 139, 89, 0.12), transparent 30%),
                radial-gradient(circle at 85% 80%, rgba(198, 139, 89, 0.10), transparent 30%),
                #f7f5f0;
        }

        .brand-panel {
            background:
                radial-gradient(circle at 80% 15%, rgba(198, 139, 89, 0.18), transparent 25%),
                radial-gradient(circle at 10% 90%, rgba(255, 255, 255, 0.04), transparent 30%),
                #292522;
        }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 36px 36px;
        }
    </style>
</head>

<body class="login-background min-h-screen text-[#292522]">

    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-10">

        <div class="grid w-full max-w-6xl overflow-hidden rounded-[2rem] bg-white shadow-[0_25px_80px_rgba(41,37,34,0.16)] lg:grid-cols-[1fr_0.9fr]">


            {{-- =====================================================
                BRAND PANEL
            ====================================================== --}}
            <section class="brand-panel grid-pattern relative hidden overflow-hidden px-12 py-16 text-white lg:flex lg:flex-col lg:justify-between">

                {{-- DECORATION --}}
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"></div>

                <div class="absolute -right-10 -top-10 h-44 w-44 rounded-full border border-[#C68B59]/30"></div>

                <div class="absolute -bottom-32 -left-32 h-80 w-80 rounded-full border border-white/5"></div>


                {{-- CONTENT --}}
                <div class="relative z-10">

                    {{-- BRAND --}}
                    <div class="flex items-center gap-3">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#C68B59] text-lg font-bold text-white shadow-lg shadow-black/20">
                            KT
                        </div>

                        <div>
                            <p class="text-base font-semibold tracking-wide">
                                Kasir Tembakau
                            </p>

                            <p class="mt-0.5 text-xs text-[#B8AEA5]">
                                Retail Management
                            </p>
                        </div>

                    </div>


                    {{-- HERO --}}
                    <div class="mt-32 max-w-lg">

                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#C68B59]">
                            Simple. Reliable. Controlled.
                        </p>

                        <h1 class="mt-6 text-5xl font-semibold leading-[1.08] tracking-tight">
                            Kelola toko
                            <span class="text-[#C68B59]">
                                tanpa ribet.
                            </span>
                        </h1>

                        <p class="mt-7 max-w-md text-base leading-7 text-[#C9C0B8]">
                            Pantau penjualan, stok, dan aktivitas toko
                            dalam satu sistem kasir yang sederhana dan terkontrol.
                        </p>

                    </div>

                </div>


                {{-- BRAND FOOTER --}}
                <div class="relative z-10 flex items-center justify-between text-xs text-[#8F857C]">

                    <span>
                        Tobacco Retail System
                    </span>

                    <span>
                        v1.0
                    </span>

                </div>

            </section>


            {{-- =====================================================
                LOGIN PANEL
            ====================================================== --}}
            <section class="flex min-h-[680px] items-center bg-[#F7F5F0] px-7 py-12 sm:px-12 lg:px-16">

                <div class="mx-auto w-full max-w-md">


                    {{-- MOBILE BRAND --}}
                    <div class="mb-12 flex items-center gap-3 lg:hidden">

                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#292522] text-sm font-bold text-[#C68B59]">
                            KT
                        </div>

                        <div>
                            <p class="text-sm font-bold text-[#292522]">
                                Kasir Tembakau
                            </p>

                            <p class="text-xs text-[#8F857C]">
                                Retail Management
                            </p>
                        </div>

                    </div>


                    {{-- HEADER --}}
                    <div class="mb-10">

                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#A56F45]">
                            Selamat datang kembali
                        </p>

                        <h2 class="mt-4 text-3xl font-bold tracking-tight text-[#292522] sm:text-4xl">
                            Masuk ke akun
                        </h2>

                        <p class="mt-4 text-sm leading-6 text-[#8A8179]">
                            Akses dashboard dan kelola operasional toko dengan lebih mudah.
                        </p>

                    </div>


                    {{-- ERROR --}}
                    @if ($errors->has('login'))

                        <div class="mb-6 flex gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">

                            <svg
                                class="mt-0.5 h-5 w-5 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3.14h15.64a2 2 0 001.71-3.14l-7.82-13a2 2 0 00-3.42 0z"
                                />
                            </svg>

                            <span>
                                {{ $errors->first('login') }}
                            </span>

                        </div>

                    @endif


                    {{-- FORM --}}
                    <form
                        method="POST"
                        action="{{ route('login.post') }}"
                        class="space-y-6"
                    >

                        @csrf


                        {{-- EMAIL --}}
                        <div>

                            <label
                                for="email"
                                class="mb-2 block text-sm font-semibold text-[#292522]"
                            >
                                Email
                            </label>

                            <div class="relative">

                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#A3978D]">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        />
                                    </svg>

                                </div>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    placeholder="nama@email.com"
                                    class="w-full rounded-2xl border border-[#DED8D0] bg-white px-4 py-3.5 pl-12 text-sm text-[#292522] shadow-sm outline-none transition placeholder:text-[#A3978D] hover:border-[#C8BFB5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                                >

                            </div>

                        </div>


                        {{-- PASSWORD --}}
            
                        <div>

                            <label
                                for="password"
                                class="mb-2 block text-sm font-semibold text-[#292522]"
                            >
                                Password
                            </label>

                            <div class="relative">

                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#A3978D]">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M7 10V8a5 5 0 0110 0v2m-9 0h8a2 2 0 012 2v7a2 2 0 01-2 2H8a2 2 0 01-2-2v-7a2 2 0 012-2z"
                                        />
                                    </svg>

                                </div>

                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    placeholder="Masukkan password"
                                    class="w-full rounded-2xl border border-[#DED8D0] bg-white px-4 py-3.5 pl-12 pr-12 text-sm text-[#292522] shadow-sm outline-none transition placeholder:text-[#A3978D] hover:border-[#C8BFB5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                                >

                                {{-- TOGGLE PASSWORD --}}
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#A3978D] transition hover:text-[#C68B59]"
                                    aria-label="Tampilkan password"
                                >

                                    {{-- EYE --}}
                                    <svg
                                        id="eyeIcon"
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6-9.75-6-9.75-6z"
                                        />

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                            stroke-width="1.8"
                                        />
                                    </svg>

                                    {{-- EYE OFF --}}
                                    <svg
                                        id="eyeOffIcon"
                                        class="hidden h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M3 3l18 18M10.58 10.58a2 2 0 002.84 2.84M9.88 4.24A10.7 10.7 0 0112 4c6 0 9.75 6 9.75 6a18.4 18.4 0 01-3.13 3.83M6.61 6.61C3.9 8.36 2.25 12 2.25 12s3.75 6 9.75 6a10.7 10.7 0 004.12-.82"
                                        />
                                    </svg>

                                </button>

                            </div>

                        </div>


                        {{-- REMEMBER --}}
                        <div class="flex items-center justify-between">

                            <label class="inline-flex cursor-pointer items-center gap-3 text-sm text-[#6B625B]">

                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-[#C8BFB5] text-[#C68B59] focus:ring-[#C68B59]"
                                >

                                <span>
                                    Ingat saya
                                </span>

                            </label>

                        </div>


                        {{-- BUTTON --}}
                        <button
                            type="submit"
                            class="group flex w-full items-center justify-center gap-3 rounded-2xl bg-[#292522] px-4 py-4 text-sm font-bold text-white shadow-lg shadow-[#292522]/20 transition hover:bg-[#3B3530] hover:shadow-xl hover:shadow-[#292522]/25 active:scale-[0.99]"
                        >

                            <span>
                                Masuk ke Dashboard
                            </span>

                            <svg
                                class="h-5 w-5 transition-transform group-hover:translate-x-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 5l7 7m0 0l-7 7m7-7H4"
                                />
                            </svg>

                        </button>

                    </form>


                    {{-- FOOTER --}}
                    <div class="mt-12 border-t border-[#DED8D0] pt-6">

                        <p class="text-center text-xs text-[#A3978D]">
                            Sistem kasir untuk operasional toko yang lebih rapi.
                        </p>

                    </div>

                </div>

            </section>

        </div>

    </div>

    <script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    togglePassword.addEventListener('click', function () {
        const isPassword = password.type === 'password';

        password.type = isPassword ? 'text' : 'password';

        eyeIcon.classList.toggle('hidden', isPassword);
        eyeOffIcon.classList.toggle('hidden', !isPassword);

        togglePassword.setAttribute(
            'aria-label',
            isPassword ? 'Sembunyikan password' : 'Tampilkan password'
        );
    });
</script>

</body>

</html>