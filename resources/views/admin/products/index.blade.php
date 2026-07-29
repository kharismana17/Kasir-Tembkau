@extends('layouts.admin')

@section('title', 'Produk - Kasir Tembakau')

@section('content')

  <div class="space-y-8">

{{-- HEADER --}}
<div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

    <div>
      <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
        Master Data
      </p>

      <h1 class="mt-2 text-xl font-semibold tracking-tight text-[#292522] sm:text-2xl lg:text-3xl">
        Produk
      </h1>

      <p class="mt-2 text-sm text-[#8A8179] sm:text-base">
        Kelola produk dan stok toko tembakau.
      </p>
    </div>


    {{-- ACTION HEADER --}}
    <div class="flex flex-wrap gap-3">

      {{-- CETAK SEMUA BARCODE --}}
      <a
        href="{{ route('admin.products.print-all-barcodes') }}"
        target="_blank"
        class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#F4EAE1]"
      >

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
            d="M6 9V4h12v5M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v6H6v-6z"
          />
        </svg>

        Cetak Semua Barcode
      </a>


      {{-- TAMBAH PRODUK --}}
      <a
        href="{{ route('admin.products.create') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C68B59] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#C68B59]/15 transition hover:bg-[#B4774A]"
      >
        <span class="text-lg leading-none">+</span>
        Tambah Produk
      </a>

    </div>

  </div>


{{-- SUCCESS --}}
@if (session('success'))
  <div class="rounded-2xl border border-[#D9C7B5] bg-[#F4EAE1] p-4 text-sm font-medium text-[#6B4F3A]">
    {{ session('success') }}
  </div>
@endif


{{-- FILTER --}}
<div class="rounded-3xl border border-[#E7E1D9] bg-white p-6 shadow-sm">

  <div class="mb-5 flex items-center gap-3">

    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EAE1] text-[#6B4F3A]">

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
          d="M3 4h18M6 12h12m-9 8h6"
        />
      </svg>

    </div>

    <div>

      <h2 class="text-sm font-semibold text-[#292522]">
        Filter Produk
      </h2>

      <p class="mt-0.5 text-xs text-[#A3978D]">
        Cari dan filter data produk.
      </p>

    </div>

  </div>


  <form
    method="GET"
    action="{{ route('admin.products.index') }}"
    class="grid gap-4 grid-cols-1 lg:grid-cols-3"
  >

    {{-- SEARCH --}}
    <div class="lg:col-span-2">

      <label
        for="search"
        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-[#8A8179]"
      >
        Cari Produk
      </label>

      <div class="relative">

        <svg
          class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#A3978D]"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>

        <input
          id="search"
          name="search"
          type="text"
          value="{{ request('search') }}"
          placeholder="Cari nama, SKU, atau barcode..."
          class="w-full rounded-xl border border-[#E1D5C8] bg-[#FFFEFC] py-3 pl-11 pr-4 text-sm text-[#292522] outline-none transition placeholder:text-[#B8AEA5] hover:border-[#C8B8A8] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
        >

      </div>

    </div>


    {{-- CATEGORY --}}
    <div>

      <label
        for="category_id"
        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-[#8A8179]"
      >
        Kategori
      </label>

      <select
        id="category_id"
        name="category_id"
        class="w-full rounded-xl border border-[#E1D5C8] bg-[#FFFEFC] px-4 py-3 text-sm text-[#292522] outline-none transition hover:border-[#C8B8A8] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
      >

        <option value="">
          Semua Kategori
        </option>

        @foreach ($categories as $category)

          <option
            value="{{ $category->id }}"
            @selected(request('category_id') == $category->id)
          >
            {{ $category->name }}
          </option>

        @endforeach

      </select>

    </div>


    {{-- ACTION --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end lg:col-span-3">

      <button
        type="submit"
        class="w-full rounded-xl bg-[#292522] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#403A36] sm:w-auto"
      >
        Terapkan Filter
      </button>

      <a
        href="{{ route('admin.products.index') }}"
        class="w-full rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-center text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#F4EAE1] sm:w-auto"
      >
        Reset
      </a>

    </div>

  </form>

</div>


{{-- TABLE --}}
<div class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

  <div class="flex flex-col gap-3 border-b border-[#E7E1D9] bg-[#FFFEFC] px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

    <div>

      <h2 class="text-base font-semibold text-[#292522]">
        Daftar Produk
      </h2>

      <p class="mt-1 text-sm text-[#8A8179]">
        Kelola seluruh produk yang tersedia di toko.
      </p>

    </div>

    <span class="w-fit rounded-full bg-[#F4EAE1] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">
      {{ $products->total() }} Produk
    </span>

  </div>


  <div class="overflow-x-auto">

    <table class="w-full min-w-[1000px]">

      <thead class="border-b border-[#E7E1D9] bg-[#F7F5F0]">

        <tr>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-[#8A8179]">
            No
          </th>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-[#8A8179]">
            Produk
          </th>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-[#8A8179]">
            Barcode
          </th>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-[#8A8179]">
            Kategori
          </th>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-[#8A8179]">
            Harga Jual
          </th>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-[#8A8179]">
            Stok
          </th>

          <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-[#8A8179]">
            Aksi
          </th>

        </tr>

      </thead>


      <tbody class="divide-y divide-[#F0ECE7]">

        @forelse ($products as $product)

          <tr class="transition hover:bg-[#FDFBF8]">

            {{-- NO --}}
            <td class="px-6 py-5 text-sm text-[#A3978D]">
              {{ $products->firstItem() + $loop->index }}
            </td>


            {{-- PRODUCT --}}
            <td class="px-6 py-5">

              <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EAE1] text-sm font-bold text-[#6B4F3A]">
                  {{ strtoupper(substr($product->name, 0, 1)) }}
                </div>

                <div>

                  <p class="font-semibold text-[#292522]">
                    {{ $product->name }}
                  </p>

                  <p class="mt-1 text-xs text-[#A3978D]">
                    SKU: {{ $product->sku }}
                  </p>

                </div>

              </div>

            </td>


            {{-- BARCODE --}}
            <td class="px-6 py-5">

                <div class="flex flex-col gap-2">

                    <code class="rounded-lg bg-[#F7F5F0] px-2.5 py-1 font-mono text-xs text-[#6B4F3A]">
                        {{ $product->barcode }}
                    </code>

                    <img
                        src="{{ route('admin.products.barcode', $product) }}"
                        alt="Barcode {{ $product->barcode }}"
                        class="h-10 w-40 object-contain"
                    >

                </div>

            </td>


            {{-- CATEGORY --}}
            <td class="px-6 py-5 text-sm text-[#6B625B]">
              {{ $product->category?->name ?? '-' }}
            </td>


            {{-- PRICE --}}
            <td class="px-6 py-5 text-sm font-semibold text-[#292522]">
              Rp {{ number_format($product->sell_price, 0, ',', '.') }}
            </td>


            {{-- STOCK --}}
            <td class="px-6 py-5">

              <div>

                <p class="font-semibold
                  {{ $product->stock <= $product->stock_min
                    ? 'text-[#A43D32]'
                    : 'text-[#292522]' }}"
                >
                  {{ $product->stock }} {{ $product->unit }}
                </p>

                <p class="mt-1 text-xs text-[#A3978D]">
                  Minimum: {{ $product->stock_min }}
                </p>

              </div>

            </td>


            {{-- ACTION --}}
            <td class="px-6 py-5">

              <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">

                  <a
                      href="{{ route('admin.products.print-barcode', $product) }}"
                      target="_blank"
                      class="rounded-xl bg-[#292522] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#403A36]"
                  >
                      Cetak Barcode
                  </a>

                  <a
                      href="{{ route('admin.products.edit', $product) }}"
                      class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-xs font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#F4EAE1]"
                  >
                      Edit
                  </a>

                  <form
                      method="POST"
                      action="{{ route('admin.products.destroy', $product) }}"
                      onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                  >

                      @csrf
                      @method('DELETE')

                      <button
                          type="submit"
                          class="rounded-xl bg-[#F7E8E5] px-4 py-2 text-xs font-semibold text-[#A43D32] transition hover:bg-[#F2D8D4]"
                      >
                          Hapus
                      </button>

                  </form>

              </div>

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
                    stroke-width="1.8"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                  />
                </svg>

              </div>

              <p class="mt-4 text-sm font-semibold text-[#292522]">
                Belum ada produk
              </p>

              <p class="mt-1 text-sm text-[#A3978D]">
                Tambahkan produk pertama untuk mulai mengelola inventori.
              </p>

            </td>

          </tr>

        @endforelse

      </tbody>

    </table>

  </div>


  @if ($products->hasPages())

    <div class="border-t border-[#E7E1D9] px-6 py-4">

      {{ $products->links() }}

    </div>

  @endif

</div>

  </div>
@endsection
