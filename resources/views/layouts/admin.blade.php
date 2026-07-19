<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Admin Kasir Tembakau')</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#F7F5F0] text-[#292522]">

    <div class="flex min-h-screen">

        {{-- =====================================================
            SIDEBAR
        ====================================================== --}}
        <aside class="hidden lg:flex lg:w-72 xl:w-80 flex-col bg-[#292522] text-white">

            {{-- BRAND --}}
            <div class="border-b border-white/10 px-6 py-6">

                <a href="{{ route('admin.dashboard') }}"
                   class="group flex items-center gap-3">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#C68B59] shadow-lg shadow-black/20 transition group-hover:scale-105">
                        <span class="text-sm font-bold text-white">
                            KT
                        </span>
                    </div>

                    <div>
                        <p class="text-base font-semibold tracking-wide text-white">
                            Kasir Tembakau
                        </p>

                        <p class="mt-0.5 text-xs text-[#B8AEA5]">
                            Retail Management
                        </p>
                    </div>

                </a>

            </div>


            {{-- NAVIGATION --}}
            <nav class="flex-1 overflow-y-auto px-4 py-6">

                {{-- OVERVIEW --}}
                <div class="mb-7">

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.25em] text-[#8F857C]">
                        Overview
                    </p>

                    <div class="space-y-1">

                        <a href="{{ route('admin.dashboard') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.dashboard')
                                ? 'bg-[#C68B59] text-white shadow-lg shadow-black/20'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M3 12l9-9 9 9M5 10v10h14V10" />

                            </svg>

                            <span>Dashboard</span>

                        </a>


                        <a href="{{ route('admin.products.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.products.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />

                            </svg>

                            <span>Produk</span>

                        </a>


                        <a href="{{ route('admin.categories.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.categories.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M4 6h16M4 12h16M4 18h16" />

                            </svg>

                            <span>Kategori</span>

                        </a>


                        <a href="{{ route('admin.payment-methods.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.payment-methods.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M3 7h18M5 7v10h14V7M8 11h4" />

                            </svg>

                            <span>Metode Pembayaran</span>

                        </a>

                    </div>

                </div>


                {{-- INVENTORY --}}
                <div class="mb-7">

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.25em] text-[#8F857C]">
                        Inventory
                    </p>

                    <div class="space-y-1">

                        <a href="{{ route('admin.stock.index') }}"
                           class="group flex items-center justify-between rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.stock.index')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <span class="flex items-center gap-3">

                                <svg class="h-5 w-5 shrink-0 opacity-80"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="1.8"
                                          d="M4 6h16v12H4zM8 10h8M8 14h5" />

                                </svg>

                                <span>Stok</span>

                            </span>

                            @if (($lowStockCount ?? 0) > 0)

                                <span class="rounded-full bg-[#B45309] px-2 py-0.5 text-[10px] font-bold text-white">
                                    {{ $lowStockCount }}
                                </span>

                            @endif

                        </a>


                        <a href="{{ route('admin.stock.opname.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.stock.opname.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M9 5h6M9 3h6v4H9zM6 7h12v14H6zM9 12h6M9 16h4" />

                            </svg>

                            <span>Stok Opname</span>

                        </a>


                        <a href="{{ route('admin.stock.movements') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.stock.movements')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M7 7h10M7 12h10M7 17h6M4 7h.01M4 12h.01M4 17h.01" />

                            </svg>

                            <span>Riwayat Stok</span>

                        </a>

                    </div>

                </div>


                {{-- SALES --}}
                <div class="mb-7">

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.25em] text-[#8F857C]">
                        Sales
                    </p>

                    <div class="space-y-1">

                        <a href="{{ route('admin.transactions.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.transactions.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M6 3h12v18H6zM9 7h6M9 11h6M9 15h3" />

                            </svg>

                            <span>Transaksi</span>

                        </a>


                        <a href="{{ route('admin.reports.sales') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.reports.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M4 19V5M4 19h16M8 15v-3M12 15V8M16 15v-6" />

                            </svg>

                            <span>Laporan</span>

                        </a>

                    </div>

                </div>


                {{-- MANAGEMENT --}}
                <div>

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.25em] text-[#8F857C]">
                        Management
                    </p>

                    <div class="space-y-1">

                        <a href="{{ route('admin.cashiers.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.cashiers.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M16 19h5M18.5 16.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM3 19h8M7 16.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />

                            </svg>

                            <span>Monitoring Kasir</span>

                        </a>


                        <a href="{{ route('admin.audit-logs.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition
                           {{ request()->routeIs('admin.audit-logs.*')
                                ? 'bg-white/10 text-white'
                                : 'text-[#C9C0B8] hover:bg-white/10 hover:text-white' }}">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M9 5h6M9 3h6v4H9zM6 7h12v14H6zM9 12h6M9 16h4" />

                            </svg>

                            <span>Audit Log</span>

                        </a>


                        <a href="{{ route('admin.settings.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm text-[#C9C0B8] transition hover:bg-white/10 hover:text-white">

                            <svg class="h-5 w-5 shrink-0 opacity-80"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM19.4 15a1.7 1.7 0 000 2.4l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 00-2.4 0 1.7 1.7 0 00-.5 1.2v.1h-2.6v-.1a1.7 1.7 0 00-2.9-1.2 1.7 1.7 0 00-2.4 0l-.1.1-1.8-1.8.1-.1a1.7 1.7 0 000-2.4 1.7 1.7 0 00-1.2-.5h-.1v-2.6h.1a1.7 1.7 0 001.2-2.9 1.7 1.7 0 000-2.4l-.1-.1 1.8-1.8.1.1a1.7 1.7 0 002.4 0 1.7 1.7 0 00.5-1.2v-.1h2.6v.1a1.7 1.7 0 002.9 1.2 1.7 1.7 0 002.4 0l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 000 2.4 1.7 1.7 0 001.2.5h.1v2.6h-.1a1.7 1.7 0 00-1.2.5z" />

                            </svg>

                            <span>Pengaturan</span>

                        </a>

                    </div>

                </div>

            </nav>


            {{-- ACCOUNT --}}
            <div class="border-t border-white/10 px-6 py-5">

                <p class="mb-3 px-1 text-[10px] font-bold uppercase tracking-[0.25em] text-[#8F857C]">
                    Account
                </p>

                <div class="rounded-2xl bg-white/5 p-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#C68B59] text-sm font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0">

                            <p class="truncate text-sm font-semibold text-white">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="mt-0.5 text-xs text-[#B8AEA5]">
                                {{ ucfirst(Auth::user()->role?->slug ?? 'User') }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </aside>


        {{-- =====================================================
            MAIN CONTENT
        ====================================================== --}}
        <div class="flex-1">


            {{-- HEADER --}}
            <header class="sticky top-0 z-20 border-b border-[#E7E1D9] bg-[#F7F5F0]/95 backdrop-blur-sm">

                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 xl:px-8">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#A3978D]">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </p>

                        <p class="mt-1 text-sm text-[#8A8179]">

                            Selamat datang kembali,

                            <span class="font-semibold text-[#292522]">
                                {{ Auth::user()->name }}
                            </span>

                        </p>

                    </div>


                    <div class="flex items-center gap-3">

                        <span class="hidden rounded-full border border-[#E1D5C8] bg-white px-4 py-2 text-xs font-semibold text-[#6B4F3A] sm:inline-flex">
                            {{ ucfirst(Auth::user()->role?->slug ?? 'User') }}
                        </span>


                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"
                                    class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#C68B59] hover:text-white">

                                Logout

                            </button>

                        </form>

                    </div>

                </div>

            </header>


            {{-- CONTENT --}}
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 xl:px-8">

                @yield('content')

            </main>

        </div>

    </div>


    {{-- PAGE SCRIPTS --}}
    @stack('scripts')

</body>

</html>