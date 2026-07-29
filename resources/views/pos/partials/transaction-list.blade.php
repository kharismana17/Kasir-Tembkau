@forelse($transactions as $transaction)
  <article class="overflow-hidden rounded-[28px] border border-[#E7E1D9] bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
    <div class="flex flex-col gap-4 border-b border-[#EEEAE2] bg-[#FAF9F6] px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l2 2 4-4m5-3V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.4-.6l-.7-.7a2 2 0 00-2 2v14a2 2 0 002 2h13a2 2 0 002-2v-6" />
          </svg>
        </div>

        <div>
          <p class="text-base font-bold text-[#292522]">
            {{ $transaction->invoice_no }}
          </p>
          <p class="mt-1 text-sm text-[#6B4F3A]">
            {{ $transaction->created_at->format('d M Y H:i') }}
          </p>
        </div>
      </div>

      <div class="flex flex-col items-start sm:items-end">
          <p class="whitespace-nowrap text-lg font-bold text-[#8A5B1E]">
              Rp {{ number_format($transaction->total, 0, ',', '.') }}
          </p>

          <span class="mt-2 inline-flex rounded-full bg-[#F4EFE6] px-3 py-1 text-[11px] font-bold text-[#8A5B1E]">
              {{ ucfirst($transaction->status) }}
          </span>
      </div>
    </div>

    <div class="px-5 py-5 sm:px-6">
      <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-[#FAF9F6] p-3">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
            Payment Method
          </p>
          <p class="mt-2 text-sm font-semibold text-[#292522]">
            {{ $transaction->paymentMethod?->name ?? '-' }}
          </p>
        </div>

        <div class="rounded-2xl bg-[#FAF9F6] p-3">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
            Subtotal
          </p>
          <p class="mt-2 text-sm font-semibold text-[#292522]">
            Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
          </p>
        </div>

        <div class="rounded-2xl bg-[#FAF9F6] p-3">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
            Pajak
          </p>
          <p class="mt-2 text-sm font-semibold text-[#292522]">
            Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}
          </p>
        </div>

        <div class="rounded-2xl bg-[#FAF9F6] p-3">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
            Pembulatan
          </p>
          <p class="mt-2 text-sm font-semibold text-[#292522]">
            Rp {{ number_format($transaction->rounding_amount, 0, ',', '.') }}
          </p>
        </div>
      </div>

      <div class="mb-4 flex items-center justify-between">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
            Detail Transaksi
          </p>
          <p class="mt-1 text-sm text-[#6B4F3A]">
            Produk yang dibeli
          </p>
        </div>
        <span class="rounded-2xl bg-[#292522] px-3 py-2 text-[11px] font-bold text-white">
          {{ $transaction->items->count() }} ITEM
        </span>
      </div>

      <div class="divide-y divide-[#EEEAE2] rounded-2xl border border-[#EEEAE2]">
        @foreach($transaction->items as $item)
          <div class="flex items-center justify-between gap-4 px-4 py-4">
            <div class="flex min-w-0 items-center gap-3">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#F4EFE6] text-sm font-bold text-[#B47727]">
                {{ strtoupper(substr($item->product?->name ?? 'P', 0, 1)) }}
              </div>

              <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#292522]">
                  {{ $item->product?->name ?? 'Produk' }}
                </p>
                <p class="mt-1 text-xs text-[#6B4F3A]">
                  {{ $item->qty }} {{ $item->product?->unit ?? '' }}
                </p>
              </div>
            </div>

            <p class="shrink-0 text-sm font-bold text-[#8A5B1E]">
              Rp {{ number_format($item->subtotal, 0, ',', '.') }}
            </p>
          </div>
        @endforeach
      </div>
    </div>

    <div class="flex flex-col gap-3 border-t border-[#EEEAE2] bg-[#FAF9F6] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
      <p class="text-[10px] font-medium uppercase tracking-widest text-[#6B4F3A]">
          Grand Total
      </p>
      <p class="flex items-center justify-end gap-1 text-xs font-semibold text-[#292522]">
          <span>Rp</span>
          <span>{{ number_format($transaction->total, 0, ',', '.') }}</span>
      </p>
    </div>
  </article>
@empty
  <div class="rounded-[28px] border border-[#E7E1D9] bg-white px-6 py-16 text-center shadow-sm">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">
      <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l2 2 4-4m5-3V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.4-.6l-.7-.7a2 2 0 00-2 2v14a2 2 0 002 2h13a2 2 0 002-2v-6" />
      </svg>
    </div>

    <h3 class="mt-5 text-lg font-bold text-[#292522]">
      Belum Ada Transaksi
    </h3>
    <p class="mt-2 text-sm text-[#6B4F3A]">
      Anda belum memiliki transaksi penjualan.
    </p>
  </div>
@endforelse
