@extends('layouts.admin')

@section('title', 'Dashboard - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
          Overview
        </p>

        <h1 class="mt-2 text-xl font-semibold tracking-tight text-[#292522] sm:text-2xl lg:text-3xl">
          Halo, {{ Auth::user()->name }} 👋
        </h1>

        <p class="mt-2 text-sm text-[#8A8179]">
          Pantau performa toko dan aktivitas penjualan hari ini.
        </p>
      </div>

      <div class="rounded-2xl border border-[#E1D5C8] bg-white px-4 py-3 text-sm font-medium text-[#6B4F3A] shadow-sm">
        {{ now()->translatedFormat('l, d F Y') }}
      </div>
    </div>


    {{-- SUMMARY --}}
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

      {{-- SALES --}}
      <article class="relative overflow-hidden rounded-3xl bg-[#292522] p-6 text-white shadow-lg shadow-black/10 xl:col-span-1">
        <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-[#C68B59]/20"></div>

        <p class="relative text-xs font-semibold uppercase tracking-[0.18em] text-[#B8AEA5]">
          Penjualan Hari Ini
        </p>

        <p class="relative mt-5 text-2xl font-semibold">
          Rp {{ number_format($todaySales, 0, ',', '.') }}
        </p>

        <p class="relative mt-2 text-xs text-[#B8AEA5]">
          Omzet transaksi valid
        </p>
      </article>


      {{-- TRANSACTIONS --}}
      <article class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#A3978D]">
          Transaksi Hari Ini
        </p>

        <p class="mt-5 text-3xl font-semibold text-[#292522]">
          {{ $todayTransactions }}
        </p>

        <p class="mt-2 text-sm text-[#8A8179]">
          Transaksi valid hari ini
        </p>
      </article>


      {{-- PRODUCTS --}}
      <article class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#A3978D]">
          Total Produk
        </p>

        <p class="mt-5 text-3xl font-semibold text-[#292522]">
          {{ $totalProducts }}
        </p>

        <p class="mt-2 text-sm text-[#8A8179]">
          Produk aktif di katalog
        </p>
      </article>


      {{-- WEEK --}}
      <article class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#A3978D]">
          Pendapatan Minggu Ini
        </p>

        <p class="mt-5 text-xl font-semibold text-[#6B4F3A]">
          Rp {{ number_format($weeklySales, 0, ',', '.') }}
        </p>

        <p class="mt-2 text-sm text-[#8A8179]">
          Omzet minggu berjalan
        </p>
      </article>


      {{-- MONTH --}}
      <article class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#A3978D]">
          Pendapatan Bulan Ini
        </p>

        <p class="mt-5 text-xl font-semibold text-[#6B4F3A]">
          Rp {{ number_format($monthlySales, 0, ',', '.') }}
        </p>

        <p class="mt-2 text-sm text-[#8A8179]">
          Omzet bulan berjalan
        </p>
      </article>

    </div>


    {{-- CHART + BEST SELLING --}}
    <div class="grid gap-6 grid-cols-1 lg:grid-cols-3">

      {{-- CHART --}}
      <section class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm xl:col-span-2">

        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-base font-semibold text-[#292522]">
              Penjualan 7 Hari Terakhir
            </h2>

            <p class="mt-1 text-sm text-[#8A8179]">
              Pergerakan omzet berdasarkan transaksi valid.
            </p>
          </div>

          <span class="rounded-full bg-[#F3E8DE] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">
            7 Hari
          </span>
        </div>

        <div class="mt-6 h-80">
          <canvas id="salesChart"></canvas>
        </div>

      </section>


      {{-- BEST SELLING --}}
      <section class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-base font-semibold text-[#292522]">
              Produk Terlaris
            </h2>

            <p class="mt-1 text-sm text-[#8A8179]">
              Berdasarkan jumlah terjual.
            </p>
          </div>

          <span class="text-xs font-semibold text-[#A3978D]">
            TOP 5
          </span>
        </div>

        <div class="mt-5 space-y-3">

          @forelse($bestSellingProducts as $index => $item)

            <div class="flex items-center gap-3 rounded-2xl border border-[#EEE8E1] bg-[#FAF8F5] p-3">

              <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#292522] text-xs font-semibold text-white">
                {{ $index + 1 }}
              </div>

              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-[#292522]">
                  {{ $item->product?->name ?? 'Produk tidak ditemukan' }}
                </p>

                <p class="mt-1 text-xs text-[#8A8179]">
                  {{ $item->total_qty }} {{ $item->product?->stockUnit() ?? 'pcs' }} terjual
                </p>
              </div>

              <div class="text-right">
                <p class="text-xs font-semibold text-[#6B4F3A]">
                  Rp {{ number_format($item->total_sales, 0, ',', '.') }}
                </p>

                <p class="mt-1 text-[10px] text-[#A3978D]">
                  omzet
                </p>
              </div>

            </div>

          @empty

            <div class="rounded-2xl border border-dashed border-[#E1D5C8] p-6 text-center">
              <p class="text-sm text-[#8A8179]">
                Belum ada data produk terjual.
              </p>
            </div>

          @endforelse

        </div>

      </section>

    </div>


    {{-- TRANSACTIONS + LOW STOCK --}}
    <div class="grid gap-6 grid-cols-1 lg:grid-cols-2">

      {{-- RECENT TRANSACTIONS --}}
      <section class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-base font-semibold text-[#292522]">
              Transaksi Terbaru
            </h2>

            <p class="mt-1 text-sm text-[#8A8179]">
              Aktivitas transaksi terbaru.
            </p>
          </div>

          <span class="rounded-full bg-[#F3E8DE] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">
            LIVE
          </span>
        </div>

        <div class="mt-5 space-y-3">

          @forelse($recentTransactions as $transaction)

            <div class="rounded-2xl border border-[#EEE8E1] p-4 transition hover:border-[#C68B59] hover:bg-[#FAF8F5]">

              <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">
                  <p class="truncate text-sm font-semibold text-[#292522]">
                    {{ $transaction->invoice_no }}
                  </p>

                  <p class="mt-1 text-xs text-[#8A8179]">
                    {{ $transaction->user?->name ?? 'Kasir' }}
                    ·
                    {{ $transaction->created_at->format('d M Y H:i') }}
                  </p>
                </div>

                <p class="shrink-0 text-sm font-semibold text-[#6B4F3A]">
                  Rp {{ number_format($transaction->total, 0, ',', '.') }}
                </p>

              </div>

              <div class="mt-3 flex flex-wrap gap-2">

                <span class="rounded-full bg-[#F5F1EC] px-3 py-1 text-[11px] font-medium text-[#6B625B]">
                  {{ $transaction->paymentMethod?->name ?? 'N/A' }}
                </span>

                <span class="rounded-full bg-[#292522] px-3 py-1 text-[11px] font-medium text-white">
                  {{ ucfirst($transaction->status) }}
                </span>

              </div>

            </div>

          @empty

            <div class="rounded-2xl border border-dashed border-[#E1D5C8] p-6 text-center">
              <p class="text-sm text-[#8A8179]">
                Belum ada transaksi terbaru hari ini.
              </p>
            </div>

          @endforelse

        </div>

      </section>


      {{-- LOW STOCK --}}
      <section class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-base font-semibold text-[#292522]">
              Produk Stok Menipis
            </h2>

            <p class="mt-1 text-sm text-[#8A8179]">
              Produk yang membutuhkan perhatian.
            </p>
          </div>

          <span class="rounded-full bg-[#FEF2F2] px-3 py-1.5 text-xs font-semibold text-[#B91C1C]">
            {{ $lowStockProducts->count() }} PRODUK
          </span>
        </div>

        <div class="mt-5 space-y-3">

          @forelse($lowStockProducts as $product)

            <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#F3D5D5] bg-[#FEF8F8] p-4">

              <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-[#292522]">
                  {{ $product->name }}
                </p>

                <p class="mt-1 text-xs text-[#8A8179]">
                  Minimum {{ $product->stock_min }} {{ $product->stockUnit() }}
                </p>
              </div>

              <div class="shrink-0 text-right">
                <p class="text-sm font-semibold text-[#B91C1C]">
                  {{ $product->stock }}
                </p>

                <p class="text-[10px] font-medium uppercase text-[#DC6B6B]">
                  {{ $product->stockUnit() }}
                </p>
              </div>

            </div>

          @empty

            <div class="rounded-2xl border border-dashed border-[#E1D5C8] p-6 text-center">
              <p class="text-sm text-[#8A8179]">
                Semua produk memiliki stok yang cukup.
              </p>
            </div>

          @endforelse

        </div>

      </section>

    </div>

  </div>
@endsection


@push('scripts')

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    const salesChartElement = document.getElementById('salesChart');

    new Chart(salesChartElement, {
      type: 'line',

      data: {
        labels: @json($salesChart->pluck('label')),

        datasets: [{
          label: 'Penjualan',

          data: @json($salesChart->pluck('total')),

          borderWidth: 3,

          tension: 0.4,

          fill: true,

          pointRadius: 3,

          pointHoverRadius: 6,

          backgroundColor: 'rgba(198, 139, 89, 0.10)',

          borderColor: 'rgba(198, 139, 89, 1)',

          pointBackgroundColor: 'rgba(198, 139, 89, 1)',

          pointBorderWidth: 2,

          pointBorderColor: '#ffffff',
        }],
      },

      options: {
        responsive: true,

        maintainAspectRatio: false,

        interaction: {
          intersect: false,
          mode: 'index',
        },

        plugins: {
          legend: {
            display: false,
          },

          tooltip: {
            displayColors: false,

            callbacks: {
              label: function (context) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
              }
            }
          }
        },

        scales: {
          x: {
            grid: {
              display: false,
            },

            ticks: {
              color: '#8A8179',
            }
          },

          y: {
            beginAtZero: true,

            grid: {
              color: 'rgba(164, 151, 141, 0.15)',
            },

            ticks: {
              color: '#8A8179',

              callback: function (value) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
              }
            }
          }
        }
      }
    });
  </script>

@endpush