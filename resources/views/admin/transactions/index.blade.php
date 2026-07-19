@extends('layouts.admin')

@section('title', 'Riwayat Transaksi - Kasir Tembakau')

@section('content')

  <div class="space-y-8">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

      <div>

        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
          Sales Management
        </p>

        <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#292522]">
          Riwayat Transaksi
        </h1>

        <p class="mt-2 text-sm text-[#8A8179]">
          Lihat dan pantau seluruh transaksi penjualan yang tercatat.
        </p>

      </div>

      <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-3">

        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-[#A3978D]">
          Total Transaksi
        </p>

        <p class="mt-1 text-xl font-bold text-[#292522]">
          {{ $transactions->total() }}
        </p>

      </div>

    </div>


    {{-- =====================================================
        SUCCESS MESSAGE
    ====================================================== --}}
    @if (session('success'))

      <div class="flex items-start gap-3 rounded-2xl border border-[#D6E4D8] bg-[#F1F7F2] p-4 text-sm text-[#3F6B4A]">

        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#D6E4D8] text-xs font-bold">
          ✓
        </span>

        <p>
          {{ session('success') }}
        </p>

      </div>

    @endif


    {{-- =====================================================
        TRANSACTION TABLE
    ====================================================== --}}
    <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

      {{-- TABLE HEADER --}}
      <div class="flex flex-col gap-3 border-b border-[#E7E1D9] px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

        <div>

          <h2 class="text-base font-bold text-[#292522]">
            Daftar Transaksi
          </h2>

          <p class="mt-1 text-xs text-[#A3978D]">
            Seluruh transaksi penjualan toko.
          </p>

        </div>

        <span class="inline-flex w-fit rounded-full bg-[#F7F5F0] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-[#6B4F3A]">
          Transaction History
        </span>

      </div>


      {{-- TABLE --}}
      <div class="overflow-x-auto">

        <table class="w-full min-w-[1050px] text-left text-sm">

          <thead class="border-b border-[#E7E1D9] bg-[#FAF9F7]">

            <tr>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#8A8179]">
                Invoice
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#8A8179]">
                Kasir
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#8A8179]">
                Pembayaran
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#8A8179]">
                Total
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#8A8179]">
                Status
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#8A8179]">
                Tanggal
              </th>

              <th class="px-6 py-4 text-right text-[11px] font-bold uppercase tracking-wider text-[#8A8179]">
                Aksi
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-[#F0ECE7]">

            @forelse ($transactions as $transaction)

              <tr class="group transition hover:bg-[#FCFBF9]">

                {{-- INVOICE --}}
                <td class="px-6 py-5">

                  <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F7F5F0] text-[#6B4F3A]">

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
                          d="M9 14h6m-6-4h6m2 10H7a2 2 0 01-2-2V6a2 2 0 012-2h6l4 4v10a2 2 0 01-2 2z"
                        />
                      </svg>

                    </div>

                    <div>

                      <p class="font-bold text-[#292522]">
                        {{ $transaction->invoice_no }}
                      </p>

                      <p class="mt-1 text-[11px] text-[#A3978D]">
                        Transaction ID
                      </p>

                    </div>

                  </div>

                </td>


                {{-- KASIR --}}
                <td class="px-6 py-5">

                  <div class="flex items-center gap-2.5">

                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#292522] text-xs font-bold text-white">

                      {{ strtoupper(substr($transaction->user?->name ?? 'U', 0, 1)) }}

                    </div>

                    <span class="text-sm font-medium text-[#6B625B]">
                      {{ $transaction->user?->name ?? '-' }}
                    </span>

                  </div>

                </td>


                {{-- PAYMENT --}}
                <td class="px-6 py-5">

                  <span class="inline-flex rounded-lg bg-[#F7F5F0] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">

                    {{ $transaction->paymentMethod?->name ?? '-' }}

                  </span>

                </td>


                {{-- TOTAL --}}
                <td class="px-6 py-5">

                  <p class="font-bold text-[#6B4F3A]">

                    Rp {{ number_format($transaction->total, 0, ',', '.') }}

                  </p>

                </td>


                {{-- STATUS --}}
                <td class="px-6 py-5">

                  @if ($transaction->status === 'completed' || $transaction->status === 'paid')

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F1F7F2] px-3 py-1.5 text-xs font-semibold text-[#3F6B4A]">

                      <span class="h-1.5 w-1.5 rounded-full bg-[#3F6B4A]"></span>

                      {{ ucfirst($transaction->status) }}

                    </span>

                  @else

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F5F0] px-3 py-1.5 text-xs font-semibold text-[#6B625B]">

                      <span class="h-1.5 w-1.5 rounded-full bg-[#A3978D]"></span>

                      {{ ucfirst($transaction->status) }}

                    </span>

                  @endif

                </td>


                {{-- DATE --}}
                <td class="px-6 py-5">

                  <p class="text-sm font-medium text-[#6B625B]">

                    {{ $transaction->created_at->format('d M Y') }}

                  </p>

                  <p class="mt-1 text-[11px] text-[#A3978D]">

                    {{ $transaction->created_at->format('H:i') }}

                  </p>

                </td>


                {{-- ACTION --}}
                <td class="px-6 py-5 text-right">

                  <a
                    href="{{ route('admin.transactions.show', $transaction) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-xs font-bold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#C68B59] hover:text-white"
                  >

                    Detail

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
                        d="M9 5l7 7-7 7"
                      />
                    </svg>

                  </a>

                </td>

              </tr>

            @empty

              <tr>

                <td
                  colspan="7"
                  class="px-6 py-16 text-center"
                >

                  <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F7F5F0] text-[#A3978D]">

                    <svg
                      class="h-7 w-7"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.6"
                        d="M9 14h6m-6-4h6m2 10H7a2 2 0 01-2-2V6a2 2 0 012-2h6l4 4v10a2 2 0 01-2 2z"
                      />
                    </svg>

                  </div>

                  <p class="mt-4 text-sm font-semibold text-[#292522]">
                    Belum ada transaksi
                  </p>

                  <p class="mt-1 text-xs text-[#A3978D]">
                    Data transaksi akan muncul setelah penjualan tercatat.
                  </p>

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>


      {{-- PAGINATION --}}
      @if ($transactions->hasPages())

        <div class="border-t border-[#E7E1D9] px-6 py-4">

          {{ $transactions->links() }}

        </div>

      @endif

    </section>

  </div>

@endsection