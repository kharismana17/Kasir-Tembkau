@extends('layouts.admin')

@section('title', 'Dashboard - Kasir Tembakau')

@section('content')
  <div class="space-y-8">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">Dashboard</p>
          <h1 class="mt-2 text-3xl font-semibold text-slate-900">Halo, {{ Auth::user()->name }}</h1>
          <p class="mt-2 text-slate-500">Berikut ringkasan performa toko hari ini.</p>
        </div>
      </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-4">
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Total Produk</p>
        <p class="mt-5 text-4xl font-semibold text-slate-900">{{ $totalProducts }}</p>
        <p class="mt-3 text-sm text-slate-500">Produk aktif yang tersedia di katalog.</p>
      </article>
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Transaksi Hari Ini</p>
        <p class="mt-5 text-4xl font-semibold text-slate-900">{{ $todayTransactions }}</p>
        <p class="mt-3 text-sm text-slate-500">Jumlah transaksi yang dicatat hari ini.</p>
      </article>
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Penjualan Hari Ini</p>
        <p class="mt-5 text-4xl font-semibold text-slate-900">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
        <p class="mt-3 text-sm text-slate-500">Total pendapatan dari transaksi hari ini.</p>
      </article>
      <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Stok Menipis</p>
        <p class="mt-5 text-4xl font-semibold text-slate-900">{{ $lowStockCount }}</p>
        <p class="mt-3 text-sm text-slate-500">Produk dengan stok kurang atau sama dengan batas minimal.</p>
      </article>
    </div>

    <div class="grid gap-5 xl:grid-cols-2">
      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">Transaksi Terbaru</h2>
            <p class="mt-1 text-sm text-slate-500">Daftar transaksi terbaru yang baru saja dibuat.</p>
          </div>
        </div>

        <div class="mt-6 space-y-4">
          @forelse($recentTransactions as $transaction)
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
              <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm font-semibold text-slate-900">{{ $transaction->invoice_no }}</p>
                  <p class="text-sm text-slate-500">{{ $transaction->user?->name ?? 'Kasir' }} · {{ $transaction->created_at->format('d M Y H:i') }}</p>
                </div>
                <p class="text-sm font-semibold text-emerald-700">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
              </div>
              <div class="mt-3 flex flex-wrap gap-3 text-sm text-slate-600">
                <span class="rounded-full bg-white px-3 py-1 border border-slate-200">Metode: {{ $transaction->paymentMethod?->name ?? 'N/A' }}</span>
                <span class="rounded-full bg-white px-3 py-1 border border-slate-200">Status: {{ ucfirst($transaction->status) }}</span>
              </div>
            </div>
          @empty
            <p class="text-sm text-slate-500">Belum ada transaksi terbaru hari ini.</p>
          @endforelse
        </div>
      </section>

      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">Produk Stok Menipis</h2>
            <p class="mt-1 text-sm text-slate-500">Produk yang perlu restock segera.</p>
          </div>
        </div>

        <div class="mt-6 space-y-4">
          @forelse($lowStockProducts as $product)
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                  <p class="text-sm text-slate-500">
                    Stok: {{ $product->stock }} {{ $product->stockUnit() }} / Minimum: {{ $product->stock_min }} {{ $product->stockUnit() }}
                  </p>
                </div>
                <span class="text-sm font-semibold text-rose-700">{{ $product->stockUnit() }}</span>
              </div>
            </div>
          @empty
            <p class="text-sm text-slate-500">Semua produk memiliki stok yang cukup.</p>
          @endforelse
        </div>
      </section>
    </div>
  </div>
@endsection
