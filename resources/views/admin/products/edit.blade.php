@extends('layouts.admin')

@section('title', 'Edit Produk - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-5xl space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

      <div>
        <div class="flex items-center gap-3">

          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#292522] text-[#C68B59] shadow-sm">
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
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 14l-4 1 1-4 8.5-8.5z"
              />
            </svg>
          </div>

          <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#A46F45]">
              Master Data
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
              Edit Produk
            </h1>
          </div>

        </div>

        <p class="mt-3 text-sm text-[#8A8179]">
          Perbarui informasi produk
          <span class="font-semibold text-[#292522]">
            {{ $product->name }}
          </span>
        </p>
      </div>

      <a
        href="{{ route('admin.products.index') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-sm font-semibold text-[#6B4F3A] shadow-sm transition hover:border-[#C68B59] hover:bg-[#FAF7F3]"
      >
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
            d="M15 19l-7-7 7-7"
          />
        </svg>

        Kembali ke Produk
      </a>

    </div>


    {{-- ERROR --}}
    @if ($errors->any())

      <div class="flex items-start gap-3 rounded-2xl border border-[#E7B8B8] bg-[#FFF4F4] p-4">

        <svg
          class="mt-0.5 h-5 w-5 shrink-0 text-[#B45309]"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3.14h15.64a2 2 0 001.71-3.14l-7.82-13a2 2 0 00-3.42 0z"
          />
        </svg>

        <div>
          <p class="text-sm font-semibold text-[#8B2C2C]">
            Terdapat kesalahan
          </p>

          <ul class="mt-2 space-y-1 text-sm text-[#A33A3A]">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>

      </div>

    @endif


    {{-- FORM --}}
    <form
      method="POST"
      action="{{ route('admin.products.update', $product) }}"
      class="space-y-6"
    >

      @csrf
      @method('PUT')


      {{-- INFORMASI PRODUK --}}
      <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

        <div class="border-b border-[#EEEAE4] bg-[#FAF8F5] px-6 py-5">

          <div class="flex items-center gap-3">

            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F1E5D8] text-[#8A5B3D]">

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
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                />
              </svg>

            </div>

            <div>
              <h2 class="text-base font-bold text-[#292522]">
                Informasi Produk
              </h2>

              <p class="mt-1 text-sm text-[#8A8179]">
                Kelola identitas dan informasi dasar produk.
              </p>
            </div>

          </div>

        </div>


        <div class="p-6">

          <div class="grid gap-6 md:grid-cols-2">

            {{-- NAMA --}}
            <div>
              <label
                for="name"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Nama Produk
              </label>

              <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $product->name) }}"
                required
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >
            </div>


            {{-- SKU --}}
            <div>
              <label
                for="sku"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                SKU
              </label>

              <input
                id="sku"
                name="sku"
                type="text"
                value="{{ old('sku', $product->sku) }}"
                required
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >
            </div>


            {{-- BARCODE --}}
            <div>
              <label
                for="barcode"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Barcode
              </label>

              <input
                id="barcode"
                name="barcode"
                type="text"
                value="{{ old('barcode', $product->barcode) }}"
                required
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >
            </div>


            {{-- KATEGORI --}}
            <div>
              <label
                for="category_id"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Kategori
              </label>

              <select
                id="category_id"
                name="category_id"
                required
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >
                <option value="">
                  Pilih Kategori
                </option>

                @foreach ($categories as $category)

                  <option
                    value="{{ $category->id }}"
                    @selected(old('category_id', $product->category_id) == $category->id)
                  >
                    {{ $category->name }}
                  </option>

                @endforeach

              </select>
            </div>

            {{-- TIPE JUAL --}}
            <div>
              <label
                for="sale_type"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Tipe Jual
              </label>

              <select
                id="sale_type"
                name="sale_type"
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >
                <option value="pcs" @selected(old('sale_type', $product->sale_type) === 'pcs')>PCS</option>
                <option value="gram" @selected(old('sale_type', $product->sale_type) === 'gram')>GRAM</option>
                <option value="pack" @selected(old('sale_type', $product->sale_type) === 'pack')>PACK</option>
                <option value="pcs_grosir" @selected(old('sale_type', $product->sale_type) === 'pcs_grosir')>PCS (Grosir)</option>
                <option value="gram_grosir" @selected(old('sale_type', $product->sale_type) === 'gram_grosir')>GRAM (Grosir)</option>
              </select>

              <p class="mt-2 text-xs text-[#9A9189]">
                Tipe jual produk. Default otomatis berdasarkan kategori.
              </p>
            </div>


            {{-- SATUAN STOK --}}
            <div>
              <label
                for="stock_unit_display"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Satuan Stok
              </label>

              <input
                id="stock_unit_display"
                type="text"
                readonly
                value="{{ old('stock_unit', $product->stock_unit) }}"
                class="w-full rounded-2xl border border-[#D9D2C9] bg-[#F4F1ED] px-4 py-3 text-sm font-semibold text-[#6B625B] outline-none"
              >

              <p class="mt-2 text-xs text-[#9A9189]">
                Ditentukan otomatis berdasarkan kategori.
              </p>
            </div>


            {{-- SATUAN PENJUALAN --}}
            <div>
              <label
                for="selling_unit_display"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Satuan Penjualan
              </label>

              <input
                id="selling_unit_display"
                type="text"
                readonly
                value="{{ old('selling_unit', $product->selling_unit) }}"
                class="w-full rounded-2xl border border-[#D9D2C9] bg-[#F4F1ED] px-4 py-3 text-sm font-semibold text-[#6B625B] outline-none"
              >

              <p class="mt-2 text-xs text-[#9A9189]">
                Ditentukan otomatis berdasarkan kategori.
              </p>
            </div>

          </div>


          {{-- STOK --}}
          <div class="mt-6 grid gap-6 md:grid-cols-2">

            <div>
              <label
                id="stockLabel"
                for="stock"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Stok ({{ $product->stock_unit }})
              </label>

              <input
                id="stock"
                name="stock"
                type="number"
                min="0"
                value="{{ old('stock', $product->stock) }}"
                required
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm font-semibold text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >

              <p
                id="stockHelper"
                class="mt-2 text-xs text-[#9A9189]"
              >
                Masukkan stok produk.
              </p>
            </div>


            {{-- STOK MINIMUM --}}
            <div>
              <label
                id="stockMinLabel"
                for="stock_min"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Stok Minimum ({{ $product->stock_unit }})
              </label>

              <input
                id="stock_min"
                name="stock_min"
                type="number"
                min="0"
                value="{{ old('stock_min', $product->stock_min) }}"
                required
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm font-semibold text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >

              <p
                id="stockMinHelper"
                class="mt-2 text-xs text-[#9A9189]"
              >
                Batas minimum stok produk.
              </p>
            </div>

          </div>


          {{-- DESKRIPSI --}}
          <div class="mt-6">

            <label
              for="description"
              class="mb-2 block text-sm font-semibold text-[#4B443F]"
            >
              Deskripsi
            </label>

            <textarea
              id="description"
              name="description"
              rows="4"
              class="w-full resize-none rounded-2xl border border-[#D9D2C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
            >{{ old('description', $product->description) }}</textarea>

          </div>

        </div>

      </section>


      {{-- HARGA --}}
      <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

        <div class="border-b border-[#EEEAE4] bg-[#FAF8F5] px-6 py-5">

          <div class="flex items-center gap-3">

            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F1E5D8] text-[#8A5B3D]">

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
                  d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-14V5m0 14v-2m-6-5H5m14 0h-1"
                />
              </svg>

            </div>

            <div>
              <h2 class="text-base font-bold text-[#292522]">
                Harga Produk
              </h2>

              <p class="mt-1 text-sm text-[#8A8179]">
                Atur harga beli dan harga jual produk.
              </p>
            </div>

          </div>

        </div>


        <div class="p-6">

          <div class="grid gap-6 md:grid-cols-2">

            {{-- HARGA BELI --}}
            <div>
              <label
                for="buy_price"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Harga Beli
              </label>

              <div class="relative">

                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-[#9A9189]">
                  Rp
                </span>

                <input
                  id="buy_price"
                  name="buy_price"
                  type="number"
                  min="0"
                  value="{{ old('buy_price', $product->buy_price) }}"
                  required
                  class="w-full rounded-2xl border border-[#D9D2C9] bg-white py-3 pl-12 pr-4 text-sm font-semibold text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                >

              </div>
            </div>

            {{-- HARGA GROSIR --}}
            <div>
              <label
                for="wholesale_price"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Harga Grosir
              </label>

              <div class="relative">

                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-[#9A9189]">
                  Rp
                </span>

                <input
                  id="wholesale_price"
                  name="wholesale_price"
                  type="number"
                  min="0"
                  value="{{ old('wholesale_price', $product->wholesale_price) }}"
                  class="w-full rounded-2xl border border-[#D9D2C9] bg-white py-3 pl-12 pr-4 text-sm font-semibold text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                >

              </div>

              <p class="mt-2 text-xs text-[#9A9189]">
                Harga grosir opsional untuk pembelian dalam jumlah lebih besar.
              </p>
            </div>

            {{-- MINIMUM GROSIR --}}
            <div>
              <label
                for="wholesale_min_qty"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Minimum Qty Grosir
              </label>

              <input
                id="wholesale_min_qty"
                name="wholesale_min_qty"
                type="number"
                min="1"
                value="{{ old('wholesale_min_qty', $product->wholesale_min_qty) }}"
                class="w-full rounded-2xl border border-[#D9D2C9] bg-white py-3 px-4 text-sm font-semibold text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >

              <p class="mt-2 text-xs text-[#9A9189]">
                Minimal qty untuk harga grosir.
              </p>
            </div>

            {{-- HARGA JUAL --}}
            <div>
              <label
                for="sell_price"
                class="mb-2 block text-sm font-semibold text-[#4B443F]"
              >
                Harga Jual
              </label>

              <div class="relative">

                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-[#9A9189]">
                  Rp
                </span>

                <input
                  id="sell_price"
                  name="sell_price"
                  type="number"
                  min="0"
                  value="{{ old('sell_price', $product->sell_price) }}"
                  required
                  class="w-full rounded-2xl border border-[#D9D2C9] bg-white py-3 pl-12 pr-4 text-sm font-semibold text-[#292522] outline-none transition hover:border-[#B9AEA3] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                >

              </div>
            </div>

          </div>

        </div>

      </section>


      {{-- ACTION --}}
      <div class="flex flex-col-reverse gap-3 border-t border-[#E7E1D9] pt-6 sm:flex-row sm:justify-end">

        <a
          href="{{ route('admin.products.index') }}"
          class="inline-flex items-center justify-center rounded-2xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#FAF7F3]"
        >
          Batal
        </a>

        <button
          type="submit"
          class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#292522] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-[#292522]/15 transition hover:bg-[#3A332E] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
        >

          <svg
            class="h-4 w-4 text-[#D8A06A]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
          </svg>

          Simpan Perubahan

        </button>

      </div>

    </form>

  </div>


  @push('scripts')

    <script>
      const categorySelect = document.getElementById('category_id');
      const saleTypeSelect = document.getElementById('sale_type');
      const stockUnitDisplay = document.getElementById('stock_unit_display');
      const sellingUnitDisplay = document.getElementById('selling_unit_display');
      const stockLabel = document.getElementById('stockLabel');
      const stockMinLabel = document.getElementById('stockMinLabel');
      const stockHelper = document.getElementById('stockHelper');
      const stockMinHelper = document.getElementById('stockMinHelper');

      function getUnitsByCategory(categoryName) {
        const categoryLower = (categoryName ?? '').trim().toLowerCase();

        if (categoryLower === 'tembakau') {
          return {
            stock: 'gram',
            selling: 'gram'
          };
        }

        if (
          categoryLower.includes('pack') ||
          categoryLower.includes('kemasan')
        ) {
          return {
            stock: 'pack',
            selling: 'pack'
          };
        }

        return {
          stock: 'pcs',
          selling: 'pcs'
        };
      }

      function getSaleTypeByCategory(categoryName) {
        const categoryLower = (categoryName ?? '').trim().toLowerCase();

        if (categoryLower === 'tembakau') {
          return 'gram';
        }

        if (
          categoryLower.includes('pack') ||
          categoryLower.includes('kemasan')
        ) {
          return 'pack';
        }

        return 'pcs';
      }

      function getUnitsBySaleType(saleType) {
        const type = (saleType ?? '').trim().toLowerCase();

        if (type === 'gram') {
          return {
            stock: 'gram',
            selling: 'gram'
          };
        }

        if (type === 'pack') {
          return {
            stock: 'pack',
            selling: 'pack'
          };
        }

        return {
          stock: 'pcs',
          selling: 'pcs'
        };
      }

      function updateProductUnitForm() {
        const selectedOption =
          categorySelect.options[categorySelect.selectedIndex];

        const categoryName =
          selectedOption?.textContent ?? '';

        const inferredSaleType = getSaleTypeByCategory(categoryName);

        if (! saleTypeSelect.value || saleTypeSelect.value === inferredSaleType) {
          saleTypeSelect.value = inferredSaleType;
        }

        const units = getUnitsBySaleType(saleTypeSelect.value);

        stockUnitDisplay.value = units.stock;
        sellingUnitDisplay.value = units.selling;

        stockLabel.textContent =
          `Stok (${units.stock})`;

        stockMinLabel.textContent =
          `Stok Minimum (${units.stock})`;

        stockHelper.textContent =
          `Masukkan stok dalam ${units.stock}.`;

        stockMinHelper.textContent =
          `Batas minimum stok dalam ${units.stock}.`;
      }

      categorySelect.addEventListener(
        'change',
        updateProductUnitForm
      );

      saleTypeSelect.addEventListener(
        'change',
        updateProductUnitForm
      );

      updateProductUnitForm();
    </script>

  @endpush

@endsection