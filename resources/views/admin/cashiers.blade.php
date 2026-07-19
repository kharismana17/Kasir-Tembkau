@extends('layouts.admin')

@section('title', 'Monitoring Kasir - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

      <div>
        <div class="mb-3 flex items-center gap-3">

          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#292522] text-[#C68B59] shadow-sm">
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
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
          </div>

          <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#A56F45]">
              Management
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
              Monitoring Kasir
            </h1>
          </div>

        </div>

        <p class="text-sm text-[#8A8179]">
          Pantau aktivitas tiga unit kasir secara real-time.
        </p>
      </div>

      <div class="inline-flex w-fit items-center gap-2 rounded-2xl border border-[#E1D5C8] bg-white px-4 py-3 shadow-sm">

        <span class="h-2 w-2 animate-pulse rounded-full bg-[#C68B59]"></span>

        <span class="text-sm font-semibold text-[#6B4F3A]">
          Live Monitoring
        </span>

      </div>

    </div>


    {{-- SUMMARY CARDS --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

      {{-- TOTAL TRANSACTIONS --}}
      <div class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between">

          <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
              Total Transaksi
            </p>

            <p class="mt-4 text-4xl font-bold tracking-tight text-[#292522]">
              {{ $totalTransactions }}
            </p>
          </div>

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F1E6DC] text-[#6B4F3A]">
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
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5h6"
              />
            </svg>
          </div>

        </div>

        <p class="mt-2 text-sm text-[#8A8179]">
          Transaksi hari ini
        </p>

      </div>


      {{-- TOTAL SALES --}}
      <div class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between">

          <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
              Total Penjualan
            </p>

            <p class="mt-4 text-2xl font-bold tracking-tight text-[#6B4F3A]">
              Rp {{ number_format($totalSales, 0, ',', '.') }}
            </p>
          </div>

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F1E6DC] text-[#C68B59]">
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
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>

        </div>

        <p class="mt-2 text-sm text-[#8A8179]">
          Penjualan hari ini
        </p>

      </div>


      {{-- BEST UNIT --}}
      <div class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between">

          <div class="min-w-0">

            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
              Unit Terbaik
            </p>

            <p class="mt-4 truncate text-2xl font-bold text-[#292522]">
              {{ $bestUnitName }}
            </p>

            <p class="mt-2 text-sm font-semibold text-[#6B4F3A]">
              Rp {{ number_format($bestUnitSales, 0, ',', '.') }}
            </p>

          </div>

          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#292522] text-[#C68B59]">
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
                d="M12 3l2.09 4.26L19 7.27l-3.5 3.41.83 4.82L12 13.25l-4.33 2.25.83-4.82L5 7.27l4.91-.01L12 3z"
              />
            </svg>
          </div>

        </div>

        <p class="mt-2 text-sm text-[#8A8179]">
          Penjualan tertinggi
        </p>

      </div>


      {{-- TOP CASHIER --}}
      <div class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between">

          <div class="min-w-0">

            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
              Kasir Teraktif
            </p>

            <p class="mt-4 truncate text-2xl font-bold text-[#292522]">
              {{ $topCashierName }}
            </p>

            <p class="mt-2 text-sm font-semibold text-[#6B4F3A]">
              {{ $topCashierTxCount }} transaksi
            </p>

          </div>

          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F1E6DC] text-[#6B4F3A]">
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
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
          </div>

        </div>

        <p class="mt-2 text-sm text-[#8A8179]">
          Aktivitas transaksi terbanyak
        </p>

      </div>

    </div>


    {{-- CHARTS --}}
    <div class="grid gap-6 xl:grid-cols-3">

      {{-- SALES CHART --}}
      <section class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm xl:col-span-2">

        <div class="mb-6">

          <div class="flex items-center justify-between gap-4">

            <div>
              <h2 class="text-lg font-bold text-[#292522]">
                Grafik Penjualan per Unit
              </h2>

              <p class="mt-1 text-sm text-[#8A8179]">
                Total penjualan hari ini untuk masing-masing unit kasir.
              </p>
            </div>

            <span class="hidden rounded-xl bg-[#F1E6DC] px-3 py-2 text-xs font-bold text-[#6B4F3A] sm:inline-flex">
              SALES
            </span>

          </div>

        </div>

        <div class="h-80">
          <canvas id="salesByUnitChart"></canvas>
        </div>

      </section>


      {{-- TRANSACTIONS CHART --}}
      <section class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="mb-6">

          <div class="flex items-center justify-between gap-4">

            <div>
              <h2 class="text-lg font-bold text-[#292522]">
                Transaksi per Unit
              </h2>

              <p class="mt-1 text-sm text-[#8A8179]">
                Jumlah transaksi hari ini.
              </p>
            </div>

            <span class="hidden rounded-xl bg-[#292522] px-3 py-2 text-xs font-bold text-white sm:inline-flex">
              TX
            </span>

          </div>

        </div>

        <div class="h-80">
          <canvas id="transactionsByUnitChart"></canvas>
        </div>

      </section>

    </div>


    {{-- UNIT SUMMARIES --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">

      @foreach($unitSummaries as $s)

        @php
          $unit = $s['unit'];
        @endphp

        <div class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm transition hover:shadow-md">

          <div class="flex items-start justify-between gap-4">

            <div class="min-w-0">

              <div class="flex items-center gap-2">

                <span class="h-2.5 w-2.5 rounded-full bg-[#C68B59]"></span>

                <p class="text-xs font-bold uppercase tracking-[0.15em] text-[#A3978D]">
                  {{ $unit->name }}
                </p>

              </div>

              <h3 class="mt-3 truncate text-lg font-bold text-[#292522]">
                {{ $s['user'] }}
              </h3>

              <p class="mt-1 text-sm text-[#8A8179]">
                {{ $s['status'] }}
              </p>

            </div>

            <div class="text-right">

              <p class="text-xl font-bold text-[#6B4F3A]">
                Rp {{ number_format($s['tx_sum'], 0, ',', '.') }}
              </p>

              <p class="mt-1 text-sm text-[#8A8179]">
                {{ $s['tx_count'] }} transaksi
              </p>

            </div>

          </div>


          <div class="mt-5 border-t border-[#EEEAE4] pt-4">

            <p class="text-xs font-bold uppercase tracking-[0.15em] text-[#A3978D]">
              Aktivitas Terakhir
            </p>

            <p class="mt-2 font-semibold text-[#292522]">
              {{ $s['last_at'] ? \Carbon\Carbon::parse($s['last_at'])->diffForHumans() : 'Belum ada aktivitas' }}
            </p>

          </div>

        </div>

      @endforeach

    </div>

  </div>
@endsection


@push('scripts')

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>

    const tobaccoBrown = '#C68B59';
    const charcoal = '#292522';
    const mutedCream = '#F1E6DC';

    new Chart(document.getElementById('salesByUnitChart'), {

      type: 'bar',

      data: {

        labels: @json($salesByUnitLabels),

        datasets: [{

          label: 'Penjualan',

          data: @json($salesByUnitData),

          backgroundColor: tobaccoBrown,

          borderColor: charcoal,

          borderWidth: 1,

          borderRadius: 8,

          maxBarThickness: 52,

        }]

      },

      options: {

        responsive: true,

        maintainAspectRatio: false,

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
              color: '#EEEAE4',
            },

            ticks: {

              color: '#8A8179',

              callback: value =>
                'Rp ' + new Intl.NumberFormat('id-ID').format(value),

            }

          }

        },

        plugins: {

          legend: {
            display: false,
          },

        }

      }

    });


    new Chart(document.getElementById('transactionsByUnitChart'), {

      type: 'bar',

      data: {

        labels: @json($salesByUnitLabels),

        datasets: [{

          label: 'Transaksi',

          data: @json($transactionsByUnitData),

          backgroundColor: charcoal,

          borderColor: tobaccoBrown,

          borderWidth: 1,

          borderRadius: 8,

          maxBarThickness: 52,

        }]

      },

      options: {

        responsive: true,

        maintainAspectRatio: false,

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
              color: '#EEEAE4',
            },

            ticks: {
              color: '#8A8179',
            }

          }

        },

        plugins: {

          legend: {
            display: false,
          },

        }

      }

    });

  </script>

@endpush