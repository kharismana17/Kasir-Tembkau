@extends('layouts.admin')

@section('title', 'Laporan Penjualan - Kasir Tembakau')

@section('content')

  <div class="space-y-8">

{{-- HEADER --}}
<div>
  <div class="flex items-center gap-3">

    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#17201c] text-[#d99a3d] shadow-sm">
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
          d="M3 3v18h18M7 16l4-5 3 3 5-7"
        />
      </svg>
    </div>

    <div>
      <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#b47727]">
        Sales Report
      </p>

      <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#17201c] sm:text-3xl">
        Laporan Penjualan
      </h1>
    </div>

  </div>

  <p class="mt-3 text-sm text-slate-500">
    Pantau performa penjualan toko berdasarkan periode.
  </p>
</div>


{{-- FILTER --}}
<div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

  <div class="mb-5">
    <h2 class="text-base font-bold text-[#17201c]">
      Filter Periode
    </h2>

    <p class="mt-1 text-sm text-slate-500">
      Pilih periode untuk melihat laporan penjualan.
    </p>
  </div>

  <form
    method="GET"
    action="{{ route('admin.reports.sales') }}"
    class="grid gap-4 md:grid-cols-3 md:items-end"
  >

    <div>
      <label
        for="from"
        class="mb-2 block text-sm font-semibold text-[#17201c]"
      >
        Dari Tanggal
      </label>

      <input
        type="date"
        id="from"
        name="from"
        value="{{ $from }}"
        class="w-full rounded-2xl border border-[#d8d3c9] bg-[#faf9f6] px-4 py-3 text-sm text-[#17201c] outline-none transition hover:border-[#b8b1a4] focus:border-[#17201c] focus:bg-white focus:ring-4 focus:ring-[#17201c]/10"
      >
    </div>

    <div>
      <label
        for="to"
        class="mb-2 block text-sm font-semibold text-[#17201c]"
      >
        Sampai Tanggal
      </label>

      <input
        type="date"
        id="to"
        name="to"
        value="{{ $to }}"
        class="w-full rounded-2xl border border-[#d8d3c9] bg-[#faf9f6] px-4 py-3 text-sm text-[#17201c] outline-none transition hover:border-[#b8b1a4] focus:border-[#17201c] focus:bg-white focus:ring-4 focus:ring-[#17201c]/10"
      >
    </div>

    <button
      type="submit"
      class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#17201c] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#2a332e] hover:shadow-md"
    >
      <svg
        class="h-4 w-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-8.586L3.293 6.707A1 1 0 013 6V4z"
        />
      </svg>

      Tampilkan Laporan
    </button>

  </form>

</div>


{{-- SUMMARY --}}
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">

  {{-- TOTAL TRANSACTIONS --}}
  <div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <div class="flex items-start justify-between">

      <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
          Total Transaksi
        </p>

        <p class="mt-5 text-4xl font-bold tracking-tight text-[#17201c]">
          {{ $totalTransactions }}
        </p>
      </div>

      <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#f4efe6] text-[#b47727]">
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

    <p class="mt-2 text-sm text-slate-500">
      Total transaksi pada periode terpilih
    </p>

  </div>


  {{-- TOTAL SALES --}}
  <div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <div class="flex items-start justify-between">

      <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
          Total Penjualan
        </p>

        <p class="mt-5 text-3xl font-bold tracking-tight text-[#b47727]">
          Rp {{ number_format($totalSales, 0, ',', '.') }}
        </p>
      </div>

      <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#f4efe6] text-[#b47727]">
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

    <p class="mt-2 text-sm text-slate-500">
      Total omzet penjualan
    </p>

  </div>


  {{-- ITEMS --}}
  <div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <div class="flex items-start justify-between">

      <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
          Item Terjual
        </p>

        <div class="mt-5 space-y-2">

          @forelse($itemsByUnit as $item)

            <p class="text-2xl font-bold text-[#17201c]">
              {{ $item['qty'] }}

              <span class="text-base font-semibold text-[#b47727]">
                {{ $item['unit'] }}
              </span>
            </p>

          @empty

            <p class="text-sm text-slate-500">
              Tidak ada item terjual.
            </p>

          @endforelse

        </div>
      </div>

      <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#f4efe6] text-[#b47727]">
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
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
          />
        </svg>
      </div>

    </div>

  </div>


  {{-- AVERAGE --}}
  <div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <div class="flex items-start justify-between">

      <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
          Rata-rata Transaksi
        </p>

        <p class="mt-5 text-3xl font-bold tracking-tight text-[#17201c]">
          Rp {{ number_format($averageTransaction, 0, ',', '.') }}
        </p>
      </div>

      <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#f4efe6] text-[#b47727]">
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
            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
      </div>

    </div>

    <p class="mt-2 text-sm text-slate-500">
      Nilai rata-rata setiap transaksi
    </p>

  </div>

</div>


{{-- PROFIT SUMMARY --}}
<div class="grid gap-5 md:grid-cols-3">

  {{-- CAPITAL --}}
  <div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
      Total Modal
    </p>

    <p class="mt-5 text-3xl font-bold text-[#17201c]">
      Rp {{ number_format($totalCapital, 0, ',', '.') }}
    </p>

    <p class="mt-3 text-sm text-slate-500">
      Estimasi modal produk terjual.
    </p>

  </div>


  {{-- PROFIT --}}
  <div class="rounded-3xl border border-[#d9c19d] bg-[#f4efe6] p-6 shadow-sm">

    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#8a5b1e]">
      Total Keuntungan
    </p>

    <p class="mt-5 text-3xl font-bold text-[#8a5b1e]">
      Rp {{ number_format($totalProfit, 0, ',', '.') }}
    </p>

    <p class="mt-3 text-sm text-[#9a6b2a]">
      Estimasi keuntungan penjualan.
    </p>

  </div>


  {{-- MARGIN --}}
  <div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
      Margin Keuntungan
    </p>

    <p class="mt-5 text-3xl font-bold text-[#17201c]">
      {{ number_format($profitPercentage, 2, ',', '.') }}%
    </p>

    <p class="mt-3 text-sm text-slate-500">
      Persentase keuntungan terhadap modal.
    </p>

  </div>

</div>


{{-- CASHIER AND UNIT --}}
<div class="grid gap-5 xl:grid-cols-2">

  {{-- CASHIER --}}
  <section class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <div class="mb-6">
      <h2 class="text-lg font-bold text-[#17201c]">
        Laporan Per Kasir
      </h2>

      <p class="mt-1 text-sm text-slate-500">
        Ringkasan penjualan dan aktivitas berdasarkan setiap kasir.
      </p>
    </div>

    <div class="overflow-x-auto">

      <table class="w-full text-left text-sm">

        <thead class="border-b border-[#eeeae2] bg-[#faf9f6]">
          <tr>
            <th class="px-4 py-3 font-bold text-slate-500">Kasir</th>
            <th class="px-4 py-3 font-bold text-slate-500">Unit</th>
            <th class="px-4 py-3 font-bold text-slate-500">Transaksi</th>
            <th class="px-4 py-3 font-bold text-slate-500">Penjualan</th>
            <th class="px-4 py-3 font-bold text-slate-500">Rata-rata</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-[#eeeae2]">

          @forelse($cashierSummary as $cashier)

            <tr class="transition hover:bg-[#faf9f6]">

              <td class="px-4 py-4 font-bold text-[#17201c]">
                {{ $cashier['name'] }}
              </td>

              <td class="px-4 py-4 text-slate-500">
                {{ $cashier['unit'] }}
              </td>

              <td class="px-4 py-4 text-slate-500">
                {{ $cashier['tx_count'] }}
              </td>

              <td class="px-4 py-4 font-semibold text-[#b47727]">
                Rp {{ number_format($cashier['sales'], 0, ',', '.') }}
              </td>

              <td class="px-4 py-4 text-slate-500">
                Rp {{ number_format($cashier['average'], 0, ',', '.') }}
              </td>

            </tr>

          @empty

            <tr>
              <td
                colspan="5"
                class="px-4 py-8 text-center text-sm text-slate-500"
              >
                Belum ada data kasir untuk periode ini.
              </td>
            </tr>

          @endforelse

        </tbody>

      </table>

    </div>

  </section>


  {{-- UNIT --}}
  <section class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

    <div class="mb-6">
      <h2 class="text-lg font-bold text-[#17201c]">
        Laporan Per Unit
      </h2>

      <p class="mt-1 text-sm text-slate-500">
        Ringkasan penjualan berdasarkan unit kasir.
      </p>
    </div>

    <div class="overflow-x-auto">

      <table class="w-full text-left text-sm">

        <thead class="border-b border-[#eeeae2] bg-[#faf9f6]">
          <tr>
            <th class="px-4 py-3 font-bold text-slate-500">Unit</th>
            <th class="px-4 py-3 font-bold text-slate-500">Transaksi</th>
            <th class="px-4 py-3 font-bold text-slate-500">Penjualan</th>
            <th class="px-4 py-3 font-bold text-slate-500">Kasir Aktif</th>
            <th class="px-4 py-3 font-bold text-slate-500">Rata-rata</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-[#eeeae2]">

          @forelse($unitSummary as $unit)

            <tr class="transition hover:bg-[#faf9f6]">

              <td class="px-4 py-4 font-bold text-[#17201c]">
                {{ $unit['unit'] }}
              </td>

              <td class="px-4 py-4 text-slate-500">
                {{ $unit['tx_count'] }}
              </td>

              <td class="px-4 py-4 font-semibold text-[#b47727]">
                Rp {{ number_format($unit['sales'], 0, ',', '.') }}
              </td>

              <td class="px-4 py-4 text-slate-500">
                {{ $unit['cashier_count'] }}
              </td>

              <td class="px-4 py-4 text-slate-500">
                Rp {{ number_format($unit['average'], 0, ',', '.') }}
              </td>

            </tr>

          @empty

            <tr>
              <td
                colspan="5"
                class="px-4 py-8 text-center text-sm text-slate-500"
              >
                Belum ada data unit untuk periode ini.
              </td>
            </tr>

          @endforelse

        </tbody>

      </table>

    </div>

  </section>

</div>


{{-- CASHIER ACTIVITY --}}
<div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

  <div class="mb-6">
    <h2 class="text-lg font-bold text-[#17201c]">
      Detail Aktivitas Kasir
    </h2>

    <p class="mt-1 text-sm text-slate-500">
      Aktivitas terbaru setiap kasir selama periode laporan.
    </p>
  </div>

  <div class="overflow-x-auto">

    <table class="w-full text-left text-sm">

      <thead class="border-b border-[#eeeae2] bg-[#faf9f6]">
        <tr>
          <th class="px-4 py-3 font-bold text-slate-500">Kasir</th>
          <th class="px-4 py-3 font-bold text-slate-500">Unit</th>
          <th class="px-4 py-3 font-bold text-slate-500">Terakhir Aktif</th>
          <th class="px-4 py-3 font-bold text-slate-500">Transaksi</th>
          <th class="px-4 py-3 font-bold text-slate-500">Total Penjualan</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-[#eeeae2]">

        @forelse($cashierActivitySummary as $activity)

          <tr class="transition hover:bg-[#faf9f6]">

            <td class="px-4 py-4 font-bold text-[#17201c]">
              {{ $activity['name'] }}
            </td>

            <td class="px-4 py-4 text-slate-500">
              {{ $activity['unit'] }}
            </td>

            <td class="px-4 py-4 text-slate-500">
              {{ $activity['last_activity'] ? \Carbon\Carbon::parse($activity['last_activity'])->format('d M Y H:i') : '-' }}
            </td>

            <td class="px-4 py-4 text-slate-500">
              {{ $activity['tx_count'] }}
            </td>

            <td class="px-4 py-4 font-semibold text-[#b47727]">
              Rp {{ number_format($activity['sales'], 0, ',', '.') }}
            </td>

          </tr>

        @empty

          <tr>
            <td
              colspan="5"
              class="px-4 py-8 text-center text-sm text-slate-500"
            >
              Belum ada aktivitas kasir pada periode ini.
            </td>
          </tr>

        @endforelse

      </tbody>

    </table>

  </div>

</div>


{{-- PAYMENT SUMMARY --}}
<div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

  <div class="mb-6">
    <h2 class="text-lg font-bold text-[#17201c]">
      Ringkasan Metode Pembayaran
    </h2>

    <p class="mt-1 text-sm text-slate-500">
      Distribusi transaksi berdasarkan metode pembayaran.
    </p>
  </div>

  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">

    @forelse($paymentSummary as $method => $summary)

      <div class="rounded-3xl border border-[#ded9d0] bg-[#faf9f6] p-5">

        <p class="text-sm font-bold text-[#17201c]">
          {{ $method }}
        </p>

        <p class="mt-3 text-2xl font-bold text-[#b47727]">
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
<section class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

  <div class="mb-6">
    <h2 class="text-lg font-bold text-[#17201c]">
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


{{-- BEST SELLING PRODUCTS --}}
<div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

  <div class="mb-6">
    <h2 class="text-lg font-bold text-[#17201c]">
      Produk Terlaris
    </h2>

    <p class="mt-1 text-sm text-slate-500">
      Produk dengan jumlah penjualan tertinggi pada periode ini.
    </p>
  </div>

  <div class="space-y-4">

    @forelse($bestSellingProducts as $index => $item)

      <div class="flex items-center gap-4 rounded-3xl border border-[#ded9d0] bg-[#faf9f6] p-4">

        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#f4efe6] text-sm font-bold text-[#b47727]">
          {{ $index + 1 }}
        </div>

        <div class="min-w-0 flex-1">

          <p class="truncate text-sm font-bold text-[#17201c]">
            {{ $item['product']?->name ?? 'Produk tidak ditemukan' }}
          </p>

          <p class="mt-1 text-sm text-slate-500">
            {{ $item['qty'] }} {{ $item['product']?->unit ?: 'pcs' }} terjual
          </p>

        </div>

        <div class="text-right">

          <p class="text-sm font-bold text-[#b47727]">
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
<div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm">

  <div class="mb-6">
    <h2 class="text-lg font-bold text-[#17201c]">
      Detail Penjualan
    </h2>

    <p class="mt-1 text-sm text-slate-500">
      Daftar transaksi pada periode yang dipilih.
    </p>
  </div>

  <div class="overflow-x-auto">

    <table class="w-full text-left text-sm">

      <thead class="border-b border-[#eeeae2] bg-[#faf9f6]">
        <tr>

          <th class="px-4 py-3 font-bold text-slate-500">
            Invoice
          </th>

          <th class="px-4 py-3 font-bold text-slate-500">
            Waktu
          </th>

          <th class="px-4 py-3 font-bold text-slate-500">
            Kasir
          </th>

          <th class="px-4 py-3 font-bold text-slate-500">
            Pembayaran
          </th>

          <th class="px-4 py-3 text-right font-bold text-slate-500">
            Total
          </th>

          <th class="px-4 py-3 text-right font-bold text-slate-500">
            Aksi
          </th>

        </tr>
      </thead>

      <tbody class="divide-y divide-[#eeeae2]">

        @forelse($transactions as $transaction)

          <tr class="transition hover:bg-[#faf9f6]">

            <td class="px-4 py-4 font-bold text-[#17201c]">
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

            <td class="px-4 py-4 text-right font-bold text-[#b47727]">
              Rp {{ number_format($transaction->total, 0, ',', '.') }}
            </td>

            <td class="px-4 py-4 text-right">

              <a
                href="{{ route('admin.transactions.show', $transaction) }}"
                class="inline-flex rounded-xl bg-[#f4efe6] px-3 py-2 text-xs font-bold text-[#8a5b1e] transition hover:bg-[#e8dfd0]"
              >
                Detail
              </a>

            </td>

          </tr>

        @empty

          <tr>

            <td
              colspan="6"
              class="px-4 py-8 text-center text-sm text-slate-500"
            >
              Belum ada transaksi pada periode ini.
            </td>

          </tr>

        @endforelse

      </tbody>

    </table>

  </div>

</div>
```

  </div>
@endsection

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

            borderColor: '#b47727',

            backgroundColor: 'rgba(180, 119, 39, 0.12)',

            borderWidth: 3,

            tension: 0.4,

            fill: true,

            pointRadius: 4,

            pointBackgroundColor: '#17201c',

            pointBorderColor: '#f4efe6',

            pointBorderWidth: 2
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
          x: {
            grid: {
              display: false
            },

            ticks: {
              color: '#64748b'
            }
          },

          y: {
            beginAtZero: true,

            grid: {
              color: '#eeeae2'
            },

            ticks: {
              color: '#64748b',

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
