@extends('layouts.admin')

@section('title', 'Stok - Kasir Tembakau')

@section('content')
  <div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

      <div>
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#6B4F3A]">
          Inventory
        </p>

        <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
          Stok Produk
        </h1>

        <p class="mt-1 text-sm text-[#8A8179]">
          Pantau dan kelola ketersediaan stok produk toko.
        </p>
      </div>

      <div class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-sm text-[#8A8179] shadow-sm">
        <span class="font-bold text-[#292522]">
          {{ $products->total() }}
        </span>
        produk aktif
      </div>

    </div>


    {{-- SUCCESS --}}
    @if (session('success'))
      <div class="flex items-start gap-3 rounded-xl border border-[#D8C4B2] bg-[#FBF3EC] p-4 text-sm text-[#6B4F3A]">

        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#C68B59] text-xs font-bold text-white">
          ✓
        </div>

        <p>
          {{ session('success') }}
        </p>

      </div>
    @endif


    {{-- FILTER --}}
    <section class="rounded-2xl border border-[#E7E1D9] bg-white p-5 shadow-sm">

      <div class="mb-4">
        <h2 class="text-sm font-bold text-[#292522]">
          Cari Produk
        </h2>

        <p class="mt-1 text-xs text-[#8A8179]">
          Cari produk berdasarkan nama atau SKU.
        </p>
      </div>

      <form
        method="GET"
        action="{{ route('admin.stock.index') }}"
        class="flex flex-col gap-3 lg:flex-row"
      >

        {{-- SEARCH --}}
        <div class="relative flex-1">

          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama atau SKU produk..."
            class="w-full rounded-xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] focus:border-[#C68B59] focus:bg-white focus:ring-2 focus:ring-[#C68B59]/10"
          >

        </div>


        {{-- SEARCH --}}
        <button
          type="submit"
          class="rounded-xl bg-[#292522] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#3B3530]"
        >
          Cari Produk
        </button>


        {{-- OPNAME --}}
        <a
          href="{{ route('admin.stock.opname.index') }}"
          class="rounded-xl bg-[#FBF3EC] px-6 py-3 text-center text-sm font-bold text-[#6B4F3A] transition hover:bg-[#F3E5D8]"
        >
          Stok Opname
        </a>


        {{-- RESET --}}
        <a
          href="{{ route('admin.stock.index') }}"
          class="rounded-xl border border-[#E1D5C8] bg-[#FBF9F6] px-6 py-3 text-center text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F3EEE8]"
        >
          Reset
        </a>

      </form>

    </section>


    {{-- PRODUCT TABLE --}}
    <section class="overflow-hidden rounded-2xl border border-[#E7E1D9] bg-white shadow-sm">

      {{-- TABLE HEADER --}}
      <div class="flex flex-col gap-2 border-b border-[#EEE8E1] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
          <h2 class="text-sm font-bold text-[#292522]">
            Daftar Stok Produk
          </h2>

          <p class="mt-1 text-xs text-[#8A8179]">
            Produk dengan stok menipis membutuhkan perhatian.
          </p>
        </div>

        <span class="rounded-lg bg-[#FBF3EC] px-3 py-1.5 text-xs font-bold text-[#6B4F3A]">
          STOCK CONTROL
        </span>

      </div>


      {{-- TABLE --}}
      <div class="overflow-x-auto">

        <table class="w-full min-w-[950px]">

          <thead class="border-b border-[#E7E1D9] bg-[#FBF9F6]">

            <tr>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Produk
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Kategori
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Stok Saat Ini
              </th>

              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Minimum
              </th>

              <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wide text-[#8A8179]">
                Aksi
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-[#F1ECE7]">

            @forelse ($products as $product)

              <tr class="transition hover:bg-[#FBF9F6]">

                {{-- PRODUK --}}
                <td class="px-5 py-4">

                  <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#FBF3EC] text-sm font-bold text-[#6B4F3A]">
                      {{ strtoupper(substr($product->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">

                      <p class="truncate text-sm font-bold text-[#292522]">
                        {{ $product->name }}
                      </p>

                      <p class="mt-1 text-xs text-[#8A8179]">
                        SKU:
                        <span class="font-medium text-[#6B4F3A]">
                          {{ $product->sku }}
                        </span>
                      </p>

                    </div>

                  </div>

                </td>


                {{-- KATEGORI --}}
                <td class="px-5 py-4">

                  <span class="rounded-lg bg-[#F3EEE8] px-2.5 py-1 text-xs font-semibold text-[#6B4F3A]">
                    {{ $product->category?->name ?? '-' }}
                  </span>

                </td>


                {{-- STOK --}}
                <td class="px-5 py-4">

                  @if ($product->stock <= $product->stock_min)

                    <div class="flex items-center gap-2">

                      <span class="inline-flex items-center rounded-lg bg-[#FFF3F0] px-2.5 py-1 text-sm font-bold text-[#A33A2B]">
                        {{ $product->stock }}
                        {{ $product->stockUnit() }}
                      </span>

                      <span class="rounded-full bg-[#FBE4DF] px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-[#A33A2B]">
                        Menipis
                      </span>

                    </div>

                  @else

                    <span class="inline-flex items-center rounded-lg bg-[#FBF3EC] px-2.5 py-1 text-sm font-bold text-[#6B4F3A]">
                      {{ $product->stock }}
                      {{ $product->stockUnit() }}
                    </span>

                  @endif

                </td>


                {{-- MINIMUM --}}
                <td class="px-5 py-4">

                  <p class="text-sm font-semibold text-[#5C514A]">
                    {{ $product->stock_min }}
                    {{ $product->stockUnit() }}
                  </p>

                  <p class="mt-1 text-xs text-[#A3978D]">
                    Batas minimum
                  </p>

                </td>


                {{-- AKSI --}}
                <td class="px-5 py-4">

                  <div class="flex justify-end gap-2">

                    <a
                      href="{{ route('admin.stock.create', $product) }}"
                      class="rounded-lg bg-[#C68B59] px-3 py-2 text-xs font-bold text-white transition hover:bg-[#B77949]"
                    >
                      + Stok Masuk
                    </a>

                    <a
                      href="{{ route('admin.stock.adjust.create', $product) }}"
                      class="rounded-lg border border-[#E1D5C8] bg-white px-3 py-2 text-xs font-bold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#FBF9F6]"
                    >
                      Sesuaikan
                    </a>

                  </div>

                </td>

              </tr>

            @empty

              <tr>

                <td
                  colspan="5"
                  class="px-6 py-16 text-center"
                >

                  <div class="mx-auto max-w-sm">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#F3EEE8] text-xl text-[#A3978D]">
                      —
                    </div>

                    <p class="mt-4 text-sm font-bold text-[#5C514A]">
                      Belum ada produk aktif
                    </p>

                    <p class="mt-1 text-xs text-[#8A8179]">
                      Produk aktif akan muncul di daftar stok ini.
                    </p>

                  </div>

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>


      {{-- PAGINATION --}}
      @if ($products->hasPages())

        <div class="border-t border-[#EEE8E1] px-5 py-4">

          {{ $products->links() }}

        </div>

      @endif

    </section>

  </div>
@endsection