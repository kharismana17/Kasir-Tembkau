<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Kasir Tembakau')</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#F7F5F0] text-[#292522]">

    @php
        $currentUser = auth()->user();
    @endphp

    <div class="flex min-h-screen flex-col lg:flex-row">

        <div id="cashierOverlay" class="fixed inset-0 z-30 hidden bg-black/35 lg:hidden" onclick="toggleCashierSidebar()"></div>

        <aside id="cashierSidebar" class="fixed inset-y-0 left-0 z-40 hidden w-80 -translate-x-full transform bg-[#292522] text-white shadow-2xl transition lg:static lg:flex lg:w-80 lg:translate-x-0 lg:flex-col lg:shadow-none">

            <div class="border-b border-white/10 px-5 py-6 lg:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#C68B59] text-sm font-bold text-white shadow-lg shadow-black/20">
                        KT
                    </div>

                    <div>
                        <p class="text-base font-semibold tracking-wide text-white">
                            Kasir Tembakau
                        </p>
                        <p class="mt-0.5 text-xs text-[#E7E1D9]">
                            Point of Sale
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex-1 px-4 py-6">
                <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                    <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#C8B5A5]">
                        Identitas User
                    </p>

                    <div class="mt-3 space-y-3 text-sm text-[#F5F1EA]">
                        <div>
                            <span class="block text-[11px] uppercase tracking-[0.18em] text-[#9E8D7D]">
                                Nama
                            </span>
                            <span class="mt-1 block font-semibold text-white">
                                {{ $currentUser?->name ?? 'Kasir' }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-[11px] uppercase tracking-[0.18em] text-[#9E8D7D]">
                                Role
                            </span>
                            <span class="mt-1 block font-semibold text-white">
                                Kasir
                            </span>
                        </div>
                    </div>
                </div>

                <nav class="mt-6 space-y-2">
                    <a href="{{ route('pos.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('pos.index') ? 'bg-[#C68B59] text-white shadow-lg shadow-black/20' : 'text-[#F3EDE4] hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h18v18H3V3zm4 4h10v3H7V7zm0 6h3v3H7v-3zm5 0h5v3h-5v-3z" />
                        </svg>
                        <span>Kasir / POS</span>
                    </a>

                    <a href="{{ route('pos.transactions.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('pos.transactions.*') ? 'bg-[#C68B59] text-white shadow-lg shadow-black/20' : 'text-[#F3EDE4] hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l2 2 4-4m5-3V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.4-.6l-.7-.7a2 2 0 00-1.4-.6H5a2 2 0 00-2 2v14a2 2 0 002 2h13a2 2 0 002-2v-6" />
                        </svg>
                        <span>Transaksi Saya</span>
                    </a>
                    <a href="{{ route('pos.attendance.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('pos.attendance.*') ? 'bg-[#C68B59] text-white shadow-lg shadow-black/20' : 'text-[#F3EDE4] hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Absensi Saya</span>
                    </a>                </nav>
            </div>

            <div class="border-t border-white/10 px-4 py-4">
                <div class="mb-3 rounded-2xl bg-white/5 p-3 ring-1 ring-white/10">
                    <p class="text-sm font-semibold text-white">
                        {{ $currentUser?->name ?? 'Kasir' }}
                    </p>
                    <p class="mt-1 text-xs text-[#CAB9A5]">
                        Badge: Kasir
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#C68B59] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#B77E49]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l5-5m0 0l-5-5m5 5H9m0-9H6a2 2 0 00-2 2v10a2 2 0 002 2h3" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1">
            <header class="sticky top-0 z-20 border-b border-[#E7E1D9] bg-[#FBF9F6]/90 backdrop-blur">
                <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button"
                                class="inline-flex items-center justify-center rounded-2xl bg-[#292522] p-2.5 text-white lg:hidden"
                                onclick="toggleCashierSidebar()">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">
                                Cashier Workspace
                            </p>
                            <h1 class="mt-1 text-xl font-bold text-[#292522] sm:text-2xl">
                                @yield('title')
                            </h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden rounded-2xl border border-[#E7E1D9] bg-white px-4 py-2 text-sm text-[#6B4F3A] shadow-sm sm:flex sm:items-center sm:gap-3">
                            <span id="cashierDate" class="font-semibold"></span>
                            <span class="text-[#B6A697]">|</span>
                            <span id="cashierClock" class="font-semibold"></span>
                        </div>

                        <div class="hidden items-center gap-3 rounded-2xl border border-[#E7E1D9] bg-white px-4 py-2.5 shadow-sm sm:flex">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F4E9DA] text-sm font-bold text-[#6B4F3A]">
                                {{ strtoupper(substr($currentUser?->name ?? 'K', 0, 1)) }}
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-[#292522]">
                                    {{ $currentUser?->name ?? 'Kasir' }}
                                </p>
                                <p class="text-xs text-[#6B4F3A]">
                                    Badge: Kasir
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-5 rounded-2xl border border-[#D9C19D] bg-[#F4EFE6] p-4 text-sm font-semibold text-[#8A5B1E] shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-2xl border border-[#D9C19D] bg-[#F4EFE6] p-4 text-sm font-semibold text-[#8A5B1E] shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-[#D9C19D] bg-[#FFF7ED] p-4 text-sm text-[#8A5B1E] shadow-sm">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                    @yield('content')
                
            </main>
        </div>
    </div>

    <script>
        function toggleCashierSidebar() {
            const sidebar = document.getElementById('cashierSidebar');
            const overlay = document.getElementById('cashierOverlay');

            if (!sidebar || !overlay) return;

            const hidden = sidebar.classList.contains('hidden');
            sidebar.classList.toggle('hidden', !hidden);
            sidebar.classList.toggle('-translate-x-full', !hidden);
            overlay.classList.toggle('hidden', !hidden);
        }

        function updateCashierClock() {
            const now = new Date();
            const dateEl = document.getElementById('cashierDate');
            const clockEl = document.getElementById('cashierClock');

            if (!dateEl || !clockEl) return;

            const dateText = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            const timeText = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            dateEl.textContent = dateText;
            clockEl.textContent = timeText;
        }

        updateCashierClock();
        setInterval(updateCashierClock, 1000);
    </script>

</body>
</html>
