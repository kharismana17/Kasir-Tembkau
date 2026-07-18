@extends('layouts.admin')

@section('title', 'Laporan Penjualan - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    <div>
      <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
        Laporan
      </p>

      <h1 class="mt-2 text-3xl font-semibold text-slate-900">
        Laporan Penjualan
      </h1>

      <p class="mt-2 text-slate-500">
        Pantau performa penjualan toko berdasarkan periode.
      </p>
    </div>


    {{-- FILTER --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <form method="GET" action="{{ route('admin.reports.sales') }}"
            class="grid gap-4 md:grid-cols-3 md:items-end">

        <div>
          <label for="from"
                 class="mb-2 block text-sm font-medium text-slate-700">
            Dari Tanggal
          </label>

          <input
            type="date"
            id="from"
            name="from"
            value="{{ $from }}"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >
        </div>

        <div>
          <label for="to"
                 class="mb-2 block text-sm font-medium text-slate-700">
            Sampai Tanggal
          </label>

          <input
            type="date"
            id="to"
            name="to"
            value="{{ $to }}"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >
        </div>

        <button
          type="submit"
          class="rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
        >
          Tampilkan Laporan
        </button>

      </form>
    </div>

    {{-- SUMMARY --}}
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">
          Total Transaksi
        </p>

        <p class="mt-5 text-4xl font-semibold text-slate-900">
          {{ $totalTransactions }}
        </p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">
          Total Penjualan
        </p>

        <p class="mt-5 text-3xl font-semibold text-emerald-700">
          Rp {{ number_format($totalSales, 0, ',', '.') }}
        </p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">
          Item Terjual per Satuan
        </p>

        <div class="mt-5 space-y-2">
          @forelse($itemsByUnit as $item)
            <p class="text-2xl font-semibold text-slate-900">
              {{ $item['qty'] }} {{ $item['unit'] }}
            </p>
          @empty
            <p class="text-sm text-slate-500">
              Tidak ada item terjual.
            </p>
          @endforelse
        </div>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">
          Rata-rata Transaksi
        </p>

        <p class="mt-5 text-3xl font-semibold text-slate-900">
          Rp {{ number_format($averageTransaction, 0, ',', '.') }}
        </p>
      </div>

    </div>

    {{-- PROFIT SUMMARY --}}
    <div class="grid gap-5 md:grid-cols-3">

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">
        Total Modal
        </p>

        <p class="mt-5 text-3xl font-semibold text-slate-900">
        Rp {{ number_format($totalCapital, 0, ',', '.') }}
        </p>

        <p class="mt-3 text-sm text-slate-500">
        Estimasi modal produk terjual.
        </p>
    </div>

    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-emerald-700">
        Total Keuntungan
        </p>

        <p class="mt-5 text-3xl font-semibold text-emerald-800">
        Rp {{ number_format($totalProfit, 0, ',', '.') }}
        </p>

        <p class="mt-3 text-sm text-emerald-700">
        Estimasi keuntungan penjualan.
        </p>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">
        Margin Keuntungan
        </p>

        <p class="mt-5 text-3xl font-semibold text-slate-900">
        {{ number_format($profitPercentage, 2, ',', '.') }}%
        </p>

        <p class="mt-3 text-sm text-slate-500">
        Persentase keuntungan terhadap modal.
        </p>
    </div>

    </div>

    {{-- PAYMENT SUMMARY --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

      <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-900">
          Ringkasan Metode Pembayaran
        </h2>

        <p class="mt-1 text-sm text-slate-500">
          Distribusi transaksi berdasarkan metode pembayaran.
        </p>
      </div>

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">

        @forelse($paymentSummary as $method => $summary)

          <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">

            <p class="text-sm font-semibold text-slate-900">
              {{ $method }}
            </p>

            <p class="mt-3 text-2xl font-semibold text-emerald-700">
              Rp {{ number_format($summary['total'], 0, ',', '.') }}
            </p>

            <p class="mt-2 text-sm text-slate-500">
              {{ $summary['count'] }} transaksi
            </p>

          </div>

        @empty

          <p class="text-sm text-slate-500">
            Belum ada data pembayaran pada periode ini.
          </p>

        @endforelse

      </div>

    </div>

    {{-- SALES CHART --}}

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">
                Grafik Penjualan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pergerakan penjualan berdasarkan periode yang dipilih.
            </p>
        </div>

        <div class="h-80">
            <canvas id="salesChart"></canvas>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const salesChart = document.getElementById('salesChart');

            new Chart(salesChart, {
            type: 'line',

            data: {
                labels: @json(
                $salesChart->map(
                    fn ($item) => \Carbon\Carbon::parse($item->date)->format('d M')
                )
                ),

                datasets: [
                {
                    label: 'Penjualan',

                    data: @json(
                    $salesChart->pluck('total')
                    ),

                    borderWidth: 3,

                    tension: 0.4,

                    fill: true,

                    pointRadius: 4
                }
                ]
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                plugins: {
                legend: {
                    display: false
                },

                tooltip: {
                    callbacks: {
                    label: function (context) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                    }
                    }
                }
                },

                scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
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

    {{-- BEST SELLING PRODUCTS --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-900">
        Produk Terlaris
        </h2>

        <p class="mt-1 text-sm text-slate-500">
        Produk dengan jumlah penjualan tertinggi pada periode ini.
        </p>
    </div>

    <div class="space-y-4">

        @forelse($bestSellingProducts as $index => $item)

        <div class="flex items-center gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-sm font-bold text-emerald-800">
            {{ $index + 1 }}
            </div>

            <div class="min-w-0 flex-1">

            <p class="truncate text-sm font-semibold text-slate-900">
                {{ $item['product']?->name ?? 'Produk tidak ditemukan' }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                {{ $item['qty'] }} {{ $item['product']?->unit ?: 'pcs' }} terjual
            </p>

            </div>

            <div class="text-right">

            <p class="text-sm font-semibold text-emerald-700">
                Rp {{ number_format($item['sales'], 0, ',', '.') }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Omzet
            </p>

            </div>

        </div>

        @empty

        <p class="text-sm text-slate-500">
            Belum ada data produk terjual pada periode ini.
        </p>

        @endforelse

    </div>

    </div>

    {{-- TRANSACTIONS --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

      <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-900">
          Detail Penjualan
        </h2>

        <p class="mt-1 text-sm text-slate-500">
          Daftar transaksi pada periode yang dipilih.
        </p>
      </div>

      <div class="overflow-x-auto">

        <table class="w-full text-left text-sm">

          <thead>
            <tr class="border-b border-slate-200 text-slate-500">

              <th class="px-4 py-3 font-medium">
                Invoice
              </th>

              <th class="px-4 py-3 font-medium">
                Waktu
              </th>

              <th class="px-4 py-3 font-medium">
                Kasir
              </th>

              <th class="px-4 py-3 font-medium">
                Pembayaran
              </th>

              <th class="px-4 py-3 text-right font-medium">
                Total
              </th>

              <th class="px-4 py-3 text-right font-medium">
                Aksi
              </th>

            </tr>
          </thead>

          <tbody>

            @forelse($transactions as $transaction)

              <tr class="border-b border-slate-100">

                <td class="px-4 py-4 font-semibold text-slate-900">
                  {{ $transaction->invoice_no }}
                </td>

                <td class="px-4 py-4 text-slate-500">
                  {{ $transaction->created_at->format('d M Y H:i') }}
                </td>

                <td class="px-4 py-4 text-slate-500">
                  {{ $transaction->user?->name ?? 'Kasir' }}
                </td>

                <td class="px-4 py-4 text-slate-500">
                  {{ $transaction->paymentMethod?->name ?? 'N/A' }}
                </td>

                <td class="px-4 py-4 text-right font-semibold text-emerald-700">
                  Rp {{ number_format($transaction->total, 0, ',', '.') }}
                </td>

                <td class="px-4 py-4 text-right">

                  <a
                    href="{{ route('admin.transactions.show', $transaction) }}"
                    class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                  >
                    Detail
                  </a>

                </td>

              </tr>

            @empty

              <tr>

                <td colspan="6"
                    class="px-4 py-8 text-center text-sm text-slate-500">

                  Belum ada transaksi pada periode ini.

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>

    </div>

  </div>

@endsection