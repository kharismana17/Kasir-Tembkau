@extends('layouts.cashier')

@section('title', 'Struk Pembayaran')

@section('content')
  @php
    $paymentName = $transaction->paymentMethod?->name ?? '-';
    $cashPaid = (float) ($transaction->paid_amount ?? 0);
    $change = (float) ($transaction->change_amount ?? 0);
  @endphp

  <div class="space-y-6 print:space-y-2">
    <section class="mx-auto max-w-2xl rounded-[28px] border border-[#E7E1D9] bg-white p-6 shadow-sm print:shadow-none print:border-0 print:p-0">
      <div class="text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">
          <span class="text-lg font-bold">KT</span>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-[#292522]">Kasir Tembakau</h2>
        <p class="mt-1 text-sm text-[#6B4F3A]">Alamat Toko</p>
      </div>

      <div class="mt-6 grid gap-3 text-sm text-[#292522] sm:grid-cols-2">
        <div class="rounded-2xl bg-[#FAF9F6] p-4">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">No Invoice</p>
          <p class="mt-1 font-bold">{{ $transaction->invoice_no }}</p>
        </div>
        <div class="rounded-2xl bg-[#FAF9F6] p-4">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Tanggal</p>
          <p class="mt-1 font-bold">{{ $transaction->created_at->format('d M Y H:i') }}</p>
        </div>
        <div class="rounded-2xl bg-[#FAF9F6] p-4">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Kasir</p>
          <p class="mt-1 font-bold">{{ $transaction->user?->name ?? '-' }}</p>
        </div>
        <div class="rounded-2xl bg-[#FAF9F6] p-4">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Metode Pembayaran</p>
          <p class="mt-1 font-bold">{{ $paymentName }}</p>
        </div>
      </div>

      <div class="mt-6 rounded-[24px] border border-[#E7E1D9]">
        <div class="grid grid-cols-[1.5fr_0.8fr_0.9fr_1fr] gap-3 border-b border-[#E7E1D9] bg-[#FBF9F6] px-4 py-3 text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
          <span>Produk</span>
          <span>Qty</span>
          <span>Harga</span>
          <span>Subtotal</span>
        </div>

        @foreach ($transaction->items as $item)
          <div class="grid grid-cols-[1.5fr_0.8fr_0.9fr_1fr] gap-3 border-b border-[#EEEAE2] px-4 py-3 text-sm text-[#292522]">
            <span class="font-semibold">{{ $item->product?->name ?? 'Produk' }}</span>
            <span>{{ $item->qty }}</span>
            <span>
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($item->price, 0, ',', '.') }}</span>
              </span>
            </span>
            <span class="font-bold text-[#8A5B1E]">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
              </span>
            </span>
          </div>
        @endforeach
      </div>

      <div class="mt-6 space-y-3 text-sm text-[#292522]">
        <div class="flex items-center justify-between">
          <span class="text-[#6B4F3A]">Subtotal</span>
          <span class="font-bold">
            <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
              <span class="mr-1">Rp</span>
              <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </span>
          </span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[#6B4F3A]">Diskon</span>
          <span class="font-bold">
            <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
              <span class="mr-1">Rp</span>
              <span>{{ number_format($transaction->discount, 0, ',', '.') }}</span>
            </span>
          </span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[#6B4F3A]">Pajak</span>
          <span class="font-bold">
            <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
              <span class="mr-1">Rp</span>
              <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </span>
          </span>
        </div>
        <div class="flex items-center justify-between rounded-2xl bg-[#F4EFE6] px-4 py-3">
          <span class="font-bold text-[#8A5B1E]">Grand Total</span>
          <span class="font-bold text-[#292522]">
            <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
              <span class="mr-1">Rp</span>
              <span>{{ number_format($transaction->total, 0, ',', '.') }}</span>
            </span>
          </span>
        </div>

        @if (strtolower($paymentName) === 'tunai')
          <div class="flex items-center justify-between">
            <span class="text-[#6B4F3A]">Uang Dibayar</span>
            <span class="font-bold">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($cashPaid, 0, ',', '.') }}</span>
              </span>
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-[#6B4F3A]">Kembalian</span>
            <span class="font-bold">
              <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                <span class="mr-1">Rp</span>
                <span>{{ number_format($change, 0, ',', '.') }}</span>
              </span>
            </span>
          </div>
        @endif
      </div>

      <div class="mt-6 border-t border-[#E7E1D9] pt-4 text-center text-sm text-[#6B4F3A]">
        Terima kasih telah berbelanja.
      </div>

      <div class="mt-6 space-y-3 no-print">
        <button type="button" onclick="window.print()" class="w-full rounded-2xl bg-[#292522] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#302F2A]">
          Cetak Struk
        </button>
        <a href="{{ route('pos.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-[#F4EFE6] px-4 py-3 text-sm font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">
          Kembali ke POS
        </a>
      </div>
    </section>
  </div>

  <style>
    @media print {
      aside,
      header,
      .no-print,
      #cashierOverlay {
        display: none !important;
      }

      body {
        background: #ffffff !important;
      }

      .print\:shadow-none {
        box-shadow: none !important;
      }

      .print\:border-0 {
        border: 0 !important;
      }

      .print\:p-0 {
        padding: 0 !important;
      }
    }
  </style>
@endsection
