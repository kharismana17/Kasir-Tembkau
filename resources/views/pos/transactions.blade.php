@extends('layouts.admin')

@section('title', 'Transaksi Saya')

@section('content')
  <div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

      <div>
        <div class="flex items-center gap-3">

          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#1B1B18] text-[#D99A3D] shadow-sm">
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
                d="M9 14l2 2 4-4m5-3V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.4-.6l-.7-.7a2 2 0 00-1.4-.6H5a2 2 0 00-2 2v14a2 2 0 002 2h13a2 2 0 002-2v-6"
              />
            </svg>
          </div>

          <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#B47727]">
              Transaction History
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#17201C]">
              Transaksi Saya
            </h1>
          </div>

        </div>

        <p class="mt-3 text-sm text-slate-500">
          Daftar transaksi penjualan yang dibuat oleh Anda.
        </p>
      </div>

      <div class="inline-flex w-fit items-center gap-2 rounded-2xl border border-[#DED9D0] bg-white px-4 py-3 shadow-sm">

        <div class="h-2 w-2 rounded-full bg-[#D99A3D]"></div>

        <span class="text-sm font-semibold text-slate-600">
          Aktivitas Kasir
        </span>

      </div>

    </div>


    {{-- TRANSACTIONS --}}
    <div class="space-y-4">

      @forelse($transactions as $transaction)

        <div class="overflow-hidden rounded-3xl border border-[#DED9D0] bg-white shadow-sm transition hover:shadow-md">

          {{-- TRANSACTION HEADER --}}
          <div class="flex flex-col gap-4 border-b border-[#EEEAE2] bg-[#FAF9F6] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">

            <div>
              <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EFE6] text-[#B47727]">

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
                      d="M9 14l2 2 4-4m5-3V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.4-.6l-.7-.7a2 2 0 00-1.4-.6H5a2 2 0 00-2 2v14a2 2 0 002 2h13a2 2 0 002-2v-6"
                    />
                  </svg>

                </div>

                <div>

                  <p class="font-bold text-[#17201C]">
                    {{ $transaction->invoice_no }}
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    {{ $transaction->created_at->format('d M Y H:i') }}
                  </p>

                </div>

              </div>
            </div>


            <div class="sm:text-right">

              <p class="text-xl font-bold text-[#8A5B1E]">
                Rp {{ number_format($transaction->total, 0, ',', '.') }}
              </p>

              <span class="mt-2 inline-flex rounded-xl bg-[#F4EFE6] px-3 py-1 text-xs font-bold text-[#8A5B1E]">
                {{ ucfirst($transaction->status) }}
              </span>

            </div>

          </div>


          {{-- ITEMS --}}
          <div class="px-5 py-5 sm:px-6">

            <div class="mb-4 flex items-center justify-between">

              <div>

                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
                  Detail Transaksi
                </p>

                <p class="mt-1 text-sm text-slate-500">
                  Produk yang dibeli
                </p>

              </div>

              <span class="rounded-xl bg-[#1B1B18] px-3 py-2 text-xs font-bold text-white">
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

                      <p class="truncate text-sm font-bold text-[#17201C]">
                        {{ $item->product?->name ?? 'Produk' }}
                      </p>

                      <p class="mt-1 text-xs text-slate-500">
                        {{ $item->qty }}
                        {{ $item->product?->unit ?? '' }}
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


          {{-- FOOTER --}}
          <div class="flex flex-col gap-3 border-t border-[#EEEAE2] bg-[#FAF9F6] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

            <p class="text-sm text-slate-500">
              Invoice transaksi
            </p>

            <p class="text-sm font-semibold text-[#17201C]">
              {{ $transaction->invoice_no }}
            </p>

          </div>

        </div>

      @empty

        {{-- EMPTY STATE --}}
        <div class="rounded-3xl border border-[#DED9D0] bg-white px-6 py-16 text-center shadow-sm">

          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">

            <svg
              class="h-8 w-8"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M9 14l2 2 4-4m5-3V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.4-.6l-.7-.7a2 2 0 00-1.4-.6H5a2 2 0 00-2 2v14a2 2 0 002 2h13a2 2 0 002-2v-6"
              />
            </svg>

          </div>

          <h3 class="mt-5 text-lg font-bold text-[#17201C]">
            Belum Ada Transaksi
          </h3>

          <p class="mt-2 text-sm text-slate-500">
            Anda belum memiliki transaksi penjualan.
          </p>

        </div>

      @endforelse

    </div>

  </div>
@endsection