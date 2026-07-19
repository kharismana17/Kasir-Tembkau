@extends('layouts.admin')

@section('title', 'Riwayat Stok - Kasir Tembakau')

@section('content')
  <div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-[#6B4F3A]">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#EAD8C8]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12h18M12 3v18"
              />
            </svg>
          </span>

          <span>Inventory</span>
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
          Riwayat Stok
        </h1>

        <p class="mt-1 text-sm text-[#8A8179]">
          Pantau seluruh perubahan stok produk secara terstruktur.
        </p>
      </div>

      <div class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-sm text-[#8A8179] shadow-sm">
        Total

        <span class="font-bold text-[#292522]">
          {{ $movements->total() }}
        </span>

        aktivitas
      </div>
    </div>


    {{-- FILTER --}}
    <section class="rounded-2xl border border-[#E7E1D9] bg-white p-5 shadow-sm">

      <div class="mb-4">
        <h2 class="text-sm font-bold text-[#292522]">
          Filter Riwayat
        </h2>

        <p class="mt-1 text-xs text-[#8A8179]">
          Cari perubahan stok berdasarkan produk atau tipe aktivitas.
        </p>
      </div>

      <form
        method="GET"
        action="{{ route('admin.stock.movements') }}"
        class="grid gap-3 md:grid-cols-3"
      >

        {{-- SEARCH --}}
        <div class="relative">
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari produk atau SKU..."
            class="w-full rounded-xl border border-[#D9CEC4] bg-[#FBF8F4] px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] focus:border-[#C68B59] focus:bg-white focus:ring-4 focus:ring-[#C68B59]/10"
          >
        </div>


        {{-- TYPE --}}
        <select
          name="type"
          class="rounded-xl border border-[#D9CEC4] bg-[#FBF8F4] px-4 py-3 text-sm text-[#5C514A] outline-none transition focus:border-[#C68B59] focus:bg-white focus:ring-4 focus:ring-[#C68B59]/10"
        >
          <option value="">Semua Aktivitas</option>

          <option value="stock_in" @selected(request('type') === 'stock_in')}>
            Stok Masuk
          </option>

          <option value="stock_out" @selected(request('type') === 'stock_out')}>
            Stok Keluar
          </option>

          <option value="sale" @selected(request('type') === 'sale')}>
            Penjualan
          </option>

          <option value="stock_adjustment" @selected(request('type') === 'stock_adjustment')}>
            Penyesuaian Stok
          </option>
        </select>


        {{-- BUTTON --}}
        <div class="flex gap-3">

          <button
            type="submit"
            class="flex-1 rounded-xl bg-[#292522] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#3B3530] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
          >
            Terapkan Filter
          </button>

          <a
            href="{{ route('admin.stock.movements') }}"
            class="rounded-xl border border-[#E1D5C8] bg-[#FBF8F4] px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-white"
          >
            Reset
          </a>

        </div>

      </form>

    </section>


    {{-- TABLE --}}
    <section class="overflow-hidden rounded-2xl border border-[#E7E1D9] bg-white shadow-sm">

      {{-- TABLE HEADER --}}
      <div class="flex flex-col gap-2 border-b border-[#EEE8E1] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
          <h2 class="text-sm font-bold text-[#292522]">
            Aktivitas Perubahan Stok
          </h2>

          <p class="mt-1 text-xs text-[#8A8179]">
            Data terbaru ditampilkan terlebih dahulu.
          </p>
        </div>

        <span class="rounded-lg bg-[#FBF3EC] px-3 py-1.5 text-xs font-bold text-[#6B4F3A]">
          INVENTORY LOG
        </span>

      </div>


      {{-- TABLE --}}
      <div class="overflow-x-auto">

        <table class="w-full min-w-[1050px]">

          <thead class="border-b border-[#EEE8E1] bg-[#FBF9F6]">

            <tr>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Waktu
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Produk
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Perubahan
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Tipe
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Referensi
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                User
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Catatan
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-[#F1ECE7]">

            @forelse($movements as $movement)

              <tr class="transition hover:bg-[#FBF9F6]">

                {{-- WAKTU --}}
                <td class="px-5 py-4">

                  <p class="text-sm font-semibold text-[#292522]">
                    {{ $movement->created_at->format('d M Y') }}
                  </p>

                  <p class="mt-1 text-xs text-[#8A8179]">
                    {{ $movement->created_at->format('H:i') }}
                  </p>

                </td>


                {{-- PRODUK --}}
                <td class="px-5 py-4">

                  <p class="text-sm font-bold text-[#292522]">
                    {{ $movement->product?->name ?? 'Produk tidak ditemukan' }}
                  </p>

                  <p class="mt-1 text-xs text-[#8A8179]">
                    SKU:
                    {{ $movement->product?->sku ?? '-' }}
                  </p>

                </td>


                {{-- PERUBAHAN --}}
                <td class="px-5 py-4">

                  <span
                    class="inline-flex items-center rounded-lg px-2.5 py-1 text-sm font-bold
                    {{ $movement->change > 0
                      ? 'bg-[#F1E8DF] text-[#6B4F3A]'
                      : 'bg-[#FFF0ED] text-[#A33A2B]' }}"
                  >

                    {{ $movement->change > 0 ? '+' : '' }}
                    {{ $movement->change }}
                    {{ $movement->unitLabel() }}

                  </span>

                </td>


                {{-- TIPE --}}
                <td class="px-5 py-4">

                  @if ($movement->type === 'stock_in')

                    <span class="rounded-lg bg-[#F1E8DF] px-2.5 py-1 text-xs font-bold text-[#6B4F3A]">
                      Stok Masuk
                    </span>

                  @elseif ($movement->type === 'stock_out')

                    <span class="rounded-lg bg-[#FFF0ED] px-2.5 py-1 text-xs font-bold text-[#A33A2B]">
                      Stok Keluar
                    </span>

                  @elseif ($movement->type === 'sale')

                    <span class="rounded-lg bg-[#EEF2F4] px-2.5 py-1 text-xs font-bold text-[#52616B]">
                      Penjualan
                    </span>

                  @elseif ($movement->type === 'stock_adjustment')

                    <span class="rounded-lg bg-[#F7EEDC] px-2.5 py-1 text-xs font-bold text-[#9A6A16]">
                      Penyesuaian
                    </span>

                  @else

                    <span class="rounded-lg bg-[#F3F0ED] px-2.5 py-1 text-xs font-bold text-[#6B625B]">
                      {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                    </span>

                  @endif

                </td>


                {{-- REFERENSI --}}
                <td class="px-5 py-4 text-sm text-[#6B625B]">

                  {{ $movement->reference_type
                    ? ucfirst(str_replace('_', ' ', $movement->reference_type))
                    : '-' }}

                </td>


                {{-- USER --}}
                <td class="px-5 py-4">

                  <p class="text-sm font-semibold text-[#5C514A]">
                    {{ $movement->user?->name ?? '-' }}
                  </p>

                </td>


                {{-- CATATAN --}}
                <td class="px-5 py-4 text-sm text-[#8A8179]">

                  {{ $movement->note ?? '-' }}

                </td>

              </tr>

            @empty

              <tr>

                <td
                  colspan="7"
                  class="px-6 py-16 text-center"
                >

                  <div class="mx-auto max-w-sm">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#F3F0ED] text-[#A3978D]">
                      —
                    </div>

                    <p class="mt-4 text-sm font-semibold text-[#5C514A]">
                      Belum ada riwayat stok
                    </p>

                    <p class="mt-1 text-xs text-[#8A8179]">
                      Aktivitas perubahan stok akan muncul di halaman ini.
                    </p>

                  </div>

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>


      {{-- PAGINATION --}}
      @if ($movements->hasPages())

        <div class="border-t border-[#EEE8E1] px-5 py-4">

          {{ $movements->links() }}

        </div>

      @endif

    </section>

  </div>
@endsection