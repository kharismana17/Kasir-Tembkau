@extends('layouts.admin')

@section('title', 'Detail Transaksi - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
          Transaksi
        </p>

        <h1 class="mt-2 text-3xl font-semibold text-slate-900">
          Detail Transaksi
        </h1>

        <p class="mt-2 text-slate-500">
          Informasi lengkap transaksi penjualan.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <a
          href="{{ route('admin.transactions.index') }}"
          class="inline-flex items-center justify-center rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
        >
          ← Kembali
        </a>

        <a
          href="{{ route('admin.transactions.receipt', $transaction) }}"
          target="_blank"
          class="inline-flex items-center justify-center rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
        >
          Cetak Struk
        </a>
      </div>
    </div>

    {{-- INFORMASI TRANSAKSI --}}
    <div class="grid gap-5 xl:grid-cols-3">

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">
          Invoice
        </p>

        <p class="mt-4 text-xl font-semibold text-slate-900">
          {{ $transaction->invoice_no }}
        </p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">
          Kasir
        </p>

        <p class="mt-4 text-xl font-semibold text-slate-900">
          {{ $transaction->user?->name ?? '-' }}
        </p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">
          Tanggal
        </p>

        <p class="mt-4 text-xl font-semibold text-slate-900">
          {{ $transaction->created_at->format('d M Y H:i') }}
        </p>
      </div>

    </div>

    {{-- ITEM TRANSAKSI --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

      <div class="border-b border-slate-200 px-6 py-5">
        <h2 class="text-lg font-semibold text-slate-900">
          Item Transaksi
        </h2>

        <p class="mt-1 text-sm text-slate-500">
          Daftar produk yang dibeli dalam transaksi ini.
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">

          <thead class="border-b border-slate-200 bg-slate-50">
            <tr>
              <th class="px-6 py-4 font-semibold text-slate-700">
                Produk
              </th>

              <th class="px-6 py-4 text-center font-semibold text-slate-700">
                Qty
              </th>

              <th class="px-6 py-4 text-right font-semibold text-slate-700">
                Harga
              </th>

              <th class="px-6 py-4 text-right font-semibold text-slate-700">
                Diskon
              </th>

              <th class="px-6 py-4 text-right font-semibold text-slate-700">
                Subtotal
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">

            @forelse ($transaction->items as $item)

              <tr class="hover:bg-slate-50">

                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">
                    {{ $item->product?->name ?? 'Produk tidak ditemukan' }}
                  </p>

                  @if ($item->product?->sku)
                    <p class="mt-1 text-xs text-slate-500">
                      SKU: {{ $item->product->sku }}
                    </p>
                  @endif
                </td>

                <td class="px-6 py-4 text-center text-slate-600">
                  {{ $item->qty }}{{ $item->product?->unit ? ' '.$item->product->unit : '' }}
                </td>

                <td class="px-6 py-4 text-right text-slate-600">
                  Rp {{ number_format($item->price, 0, ',', '.') }}
                </td>

                <td class="px-6 py-4 text-right text-slate-600">
                  Rp {{ number_format($item->discount, 0, ',', '.') }}
                </td>

                <td class="px-6 py-4 text-right font-semibold text-emerald-700">
                  Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>

              </tr>

            @empty

              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                  Tidak ada item transaksi.
                </td>
              </tr>

            @endforelse

          </tbody>

        </table>
      </div>

    </div>

    {{-- RINGKASAN --}}
    <div class="grid gap-5 xl:grid-cols-2">

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-slate-900">
          Pembayaran
        </h2>

        <div class="mt-6 space-y-4">

          <div class="flex items-center justify-between text-sm">
            <span class="text-slate-500">Metode Pembayaran</span>
            <span class="font-semibold text-slate-900">
              {{ $transaction->paymentMethod?->name ?? '-' }}
            </span>
          </div>

          <div class="flex items-center justify-between text-sm">
            <span class="text-slate-500">Uang Dibayar</span>
            <span class="font-semibold text-slate-900">
              Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}
            </span>
          </div>

          <div class="flex items-center justify-between border-t border-slate-200 pt-4 text-sm">
            <span class="text-slate-500">Kembalian</span>
            <span class="font-semibold text-emerald-700">
              Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}
            </span>
          </div>

        </div>

      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-slate-900">
          Ringkasan Transaksi
        </h2>

        <div class="mt-6 space-y-4">

          <div class="flex items-center justify-between text-sm">
            <span class="text-slate-500">Subtotal</span>
            <span class="font-semibold text-slate-900">
              Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
            </span>
          </div>

          <div class="flex items-center justify-between text-sm">
            <span class="text-slate-500">Diskon</span>
            <span class="font-semibold text-slate-900">
              Rp {{ number_format($transaction->discount, 0, ',', '.') }}
            </span>
          </div>

          <div class="flex items-center justify-between border-t border-slate-200 pt-4">
            <span class="font-semibold text-slate-900">
              Total
            </span>

            <span class="text-2xl font-semibold text-emerald-700">
              Rp {{ number_format($transaction->total, 0, ',', '.') }}
            </span>
          </div>

        </div>

      </div>

    </div>

  </div>
@endsection