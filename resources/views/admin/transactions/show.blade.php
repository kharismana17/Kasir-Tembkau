@extends('layouts.admin')

@section('title', 'Detail Transaksi - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

      <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-[#A3978D]">
          Sales / Transaction
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-[#292522]">
          Detail Transaksi
        </h1>

        <p class="mt-2 text-sm text-[#8A8179]">
          Informasi lengkap transaksi penjualan yang tercatat.
        </p>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row">

        <a
          href="{{ route('admin.transactions.index') }}"
          class="inline-flex items-center justify-center rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#F7F5F0]"
        >
          ← Kembali
        </a>

        <a
          href="{{ route('admin.transactions.receipt', $transaction) }}"
          target="_blank"
          class="inline-flex items-center justify-center rounded-xl bg-[#C68B59] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#C68B59]/20 transition hover:bg-[#6B4F3A]"
        >
          Cetak Struk
        </a>

      </div>

    </div>


    {{-- =====================================================
        TRANSACTION SUMMARY
    ====================================================== --}}
    <div class="grid gap-4 xl:grid-cols-3">

      {{-- INVOICE --}}
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between gap-4">

          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
              Invoice
            </p>

            <p class="mt-4 text-xl font-semibold tracking-tight text-[#292522]">
              {{ $transaction->invoice_no }}
            </p>
          </div>

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F7F5F0] text-[#C68B59]">
            #
          </div>

        </div>

      </div>


      {{-- KASIR --}}
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between gap-4">

          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
              Kasir
            </p>

            <p class="mt-4 text-xl font-semibold tracking-tight text-[#292522]">
              {{ $transaction->user?->name ?? '-' }}
            </p>
          </div>

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F7F5F0] text-[#C68B59]">
            ◌
          </div>

        </div>

      </div>


      {{-- TANGGAL --}}
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between gap-4">

          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
              Waktu Transaksi
            </p>

            <p class="mt-4 text-xl font-semibold tracking-tight text-[#292522]">
              {{ $transaction->created_at->format('d M Y H:i') }}
            </p>
          </div>

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F7F5F0] text-[#C68B59]">
            ◷
          </div>

        </div>

      </div>

    </div>


    {{-- =====================================================
        ITEM TRANSACTION
    ====================================================== --}}
    <section class="overflow-hidden rounded-2xl border border-[#E7E1D9] bg-white shadow-sm">

      <div class="flex flex-col gap-2 border-b border-[#E7E1D9] px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

        <div>
          <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
            Transaction Items
          </p>

          <h2 class="mt-2 text-lg font-semibold text-[#292522]">
            Item Transaksi
          </h2>

          <p class="mt-1 text-sm text-[#8A8179]">
            Daftar produk yang dibeli dalam transaksi ini.
          </p>
        </div>

        <span class="w-fit rounded-full bg-[#F7F5F0] px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-[#6B4F3A]">
          {{ $transaction->items->count() }} Item
        </span>

      </div>


      <div class="overflow-x-auto">

        <table class="w-full min-w-[850px] text-left text-sm">

          <thead class="border-b border-[#E7E1D9] bg-[#F7F5F0]">

            <tr>

              <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider text-[#8A8179]">
                Produk
              </th>

              <th class="px-6 py-4 text-center text-[11px] font-semibold uppercase tracking-wider text-[#8A8179]">
                Qty
              </th>

              <th class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-wider text-[#8A8179]">
                Harga
              </th>

              <th class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-wider text-[#8A8179]">
                Diskon
              </th>

              <th class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-wider text-[#8A8179]">
                Subtotal
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-[#F0ECE7]">

            @forelse ($transaction->items as $item)

              <tr class="transition hover:bg-[#FDFCFB]">

                <td class="px-6 py-5">

                  <p class="font-semibold text-[#292522]">
                    {{ $item->product?->name ?? 'Produk tidak ditemukan' }}
                  </p>

                  @if ($item->product?->sku)

                    <p class="mt-1 text-xs text-[#A3978D]">
                      SKU: {{ $item->product->sku }}
                    </p>

                  @endif

                </td>


                <td class="px-6 py-5 text-center">

                  <span class="inline-flex rounded-lg bg-[#F7F5F0] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">
                    {{ $item->qty }}{{ $item->product?->unit ? ' '.$item->product->unit : '' }}
                  </span>

                </td>


                <td class="px-6 py-5 text-right text-[#6B625B]">
                  <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                    <span class="mr-1">Rp</span>
                    <span>{{ number_format($item->price, 0, ',', '.') }}</span>
                  </span>
                </td>


                <td class="px-6 py-5 text-right text-[#6B625B]">
                  <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                    <span class="mr-1">Rp</span>
                    <span>{{ number_format($item->discount, 0, ',', '.') }}</span>
                  </span>
                </td>


                <td class="px-6 py-5 text-right font-semibold text-[#6B4F3A]">
                  <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                    <span class="mr-1">Rp</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                  </span>
                </td>

              </tr>

            @empty

              <tr>

                <td
                  colspan="5"
                  class="px-6 py-12 text-center text-sm text-[#8A8179]"
                >
                  Tidak ada item transaksi.
                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>

    </section>


    {{-- =====================================================
        PAYMENT + SUMMARY
    ====================================================== --}}
    <div class="grid gap-5 xl:grid-cols-2">


      {{-- PEMBAYARAN --}}
      <section class="rounded-2xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-center gap-3">

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F7F5F0] text-[#C68B59]">
            ◈
          </div>

          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
              Payment
            </p>

            <h2 class="mt-1 text-lg font-semibold text-[#292522]">
              Pembayaran
            </h2>
          </div>

        </div>


        <div class="mt-7 space-y-5">

          <div class="flex items-center justify-between gap-4 text-sm">

            <span class="text-[#8A8179]">
              Metode Pembayaran
            </span>

            <span class="font-semibold text-[#292522]">
              {{ $transaction->paymentMethod?->name ?? '-' }}
            </span>

          </div>


          <div class="flex items-center justify-between gap-4 text-sm">

            <span class="text-[#8A8179]">
              Uang Dibayar
            </span>

            <span class="font-semibold text-[#292522]">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
              </span>
            </span>

          </div>


          <div class="flex items-center justify-between gap-4 border-t border-[#E7E1D9] pt-5 text-sm">

            <span class="text-[#8A8179]">
              Kembalian
            </span>

            <span class="font-semibold text-[#3F6B4A]">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
              </span>
            </span>

          </div>

        </div>

      </section>


      {{-- RINGKASAN --}}
      <section class="rounded-2xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

        <div class="flex items-center gap-3">

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F7F5F0] text-[#C68B59]">
            ◒
          </div>

          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
              Summary
            </p>

            <h2 class="mt-1 text-lg font-semibold text-[#292522]">
              Ringkasan Transaksi
            </h2>
          </div>

        </div>


        <div class="mt-7 space-y-5">

          <div class="flex items-center justify-between gap-4 text-sm">

            <span class="text-[#8A8179]">
              Subtotal
            </span>

            <span class="font-semibold text-[#292522]">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
              </span>
            </span>

          </div>


          <div class="flex items-center justify-between gap-4 text-sm">

            <span class="text-[#8A8179]">
              Diskon
            </span>

            <span class="font-semibold text-[#292522]">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($transaction->discount, 0, ',', '.') }}</span>
              </span>
            </span>

          </div>


          <div class="flex items-end justify-between gap-4 border-t border-[#E7E1D9] pt-5">

            <span class="font-semibold text-[#292522]">
              Total
            </span>

            <span class="text-2xl font-bold tracking-tight text-[#6B4F3A]">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($transaction->total, 0, ',', '.') }}</span>
              </span>
            </span>

          </div>

        </div>

      </section>

    </div>

  </div>
@endsection