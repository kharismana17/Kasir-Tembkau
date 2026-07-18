<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Admin Kasir Tembakau')</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="hidden lg:flex lg:w-72 xl:w-80 flex-col border-r border-slate-200 bg-white p-6">

            <div class="mb-10">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-3 text-emerald-800">

                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-xl font-semibold">
                        KT
                    </span>

                    <div>
                        <p class="text-base font-semibold">
                            Kasir Tembakau
                        </p>

                        <p class="text-sm text-slate-500">
                            Panel Admin
                        </p>
                    </div>

                </a>
            </div>

            <nav class="space-y-2 text-sm font-medium text-slate-700">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 rounded-2xl bg-emerald-100 px-4 py-3 text-emerald-900 shadow-sm shadow-emerald-100">
                    Dashboard
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Produk
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Kategori
                </a>

                <a href="{{ route('admin.payment-methods.index') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Metode Pembayaran
                </a>

                <a href="{{ route('admin.stock.index') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Stok
                </a>

                <a href="{{ route('admin.stock.movements') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Riwayat Stok
                </a>

                <a href="{{ route('admin.transactions.index') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Transaksi
                </a>

                <a href="{{ route('admin.reports.sales') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Laporan
                </a>

                <a href="#"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 hover:bg-slate-100">
                    Pengaturan
                </a>

            </nav>

            {{-- ACCOUNT --}}
            <div class="mt-auto border-t border-slate-200 pt-8">

                <p class="mb-2 text-xs uppercase tracking-[0.25em] text-slate-500">
                    Akun
                </p>

                <div class="rounded-3xl bg-slate-50 p-4">

                    <p class="text-sm font-semibold text-slate-900">
                        {{ Auth::user()->name }}
                    </p>

                    <p class="text-sm text-slate-500">
                        {{ ucfirst(Auth::user()->role?->slug ?? 'User') }}
                    </p>

                </div>

            </div>

        </aside>


        {{-- MAIN --}}
        <div class="flex-1">

            {{-- HEADER --}}
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur-sm">

                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 xl:px-8">

                    <div>
                        <p class="text-sm text-slate-500">
                            Selamat datang kembali,

                            <span class="font-semibold text-slate-900">
                                {{ Auth::user()->name }}
                            </span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3">

                        <span class="rounded-full bg-emerald-100 px-3 py-2 text-sm font-medium text-emerald-800">
                            {{ ucfirst(Auth::user()->role?->slug ?? 'User') }}
                        </span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                    class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                                Logout
                            </button>
                        </form>

                    </div>

                </div>

            </header>


            {{-- CONTENT --}}
            <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 xl:px-8">

                @yield('content')

            </main>

        </div>

    </div>


    {{-- PAGE SCRIPTS --}}
    @stack('scripts')

</body>

</html>