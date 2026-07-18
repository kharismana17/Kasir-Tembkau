@extends('layouts.admin')

@section('title', 'Tambah Produk - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-4xl space-y-8">
    <div>
      <a
        href="{{ route('admin.products.index') }}"
        class="text-sm font-medium text-emerald-700 hover:text-emerald-800"
      >
        ← Kembali ke Produk
      </a>

      <p class="mt-6 text-sm uppercase tracking-[0.25em] text-emerald-600">
        Master Data
      </p>

      <h1 class="mt-2 text-3xl font-semibold text-slate-900">
        Tambah Produk
      </h1>

      <p class="mt-2 text-slate-500">
        Tambahkan produk baru ke katalog toko.
      </p>
    </div>

    @if ($errors->any())
      <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
        <p class="font-semibold">Terdapat kesalahan:</p>

        <ul class="mt-2 list-disc space-y-1 pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form
      method="POST"
      action="{{ route('admin.products.store') }}"
      class="space-y-6"
    >
      @csrf

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">
          Informasi Produk
        </h2>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
          <div>
            <label
              for="name"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Nama Produk
            </label>

            <input
              id="name"
              name="name"
              type="text"
              value="{{ old('name') }}"
              required
              placeholder="Contoh: Gudang Garam Surya 12"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >
          </div>

          <div>
            <label
              for="sku"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              SKU
            </label>

            <input
              id="sku"
              name="sku"
              type="text"
              value="{{ old('sku') }}"
              required
              placeholder="Contoh: GGS-12"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >
          </div>

          <div>
            <label
              for="barcode"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Barcode
            </label>

            <input
              id="barcode"
              name="barcode"
              type="text"
              value="{{ old('barcode') }}"
              required
              placeholder="Contoh: 8992200000123"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >
          </div>

          <div>
            <label
              for="category_id"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Kategori
            </label>

            <select
              id="category_id"
              name="category_id"
              required
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >
              <option value="">Pilih Kategori</option>

              @foreach ($categories as $category)
                <option
                  value="{{ $category->id }}"
                  data-category="{{ strtolower($category->name) }}"
                  @selected(old('category_id') == $category->id)
                >
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label
              for="stock_unit_display"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Satuan Stok
            </label>

            <input
              id="stock_unit_display"
              type="text"
              readonly
              class="w-full rounded-2xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm outline-none"
            >

            <p class="mt-2 text-xs text-slate-500">
              Ditentukan otomatis berdasarkan kategori
            </p>
          </div>

          <div>
            <label
              for="selling_unit_display"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Satuan Penjualan
            </label>

            <input
              id="selling_unit_display"
              type="text"
              readonly
              class="w-full rounded-2xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm outline-none"
            >

            <p class="mt-2 text-xs text-slate-500">
              Ditentukan otomatis berdasarkan kategori
            </p>
          </div>

          <div class="mt-6">
            <label
              id="stockLabel"
              for="stock"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Stok Awal
            </label>

            <input
              id="stock"
              name="stock"
              type="number"
              min="0"
              value="{{ old('stock', 0) }}"
              required
              placeholder="Contoh: 1000"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >

            <p class="mt-2 text-xs text-slate-500" id="stockHelper">
              Masukkan stok.
            </p>
          </div>

          <div class="mt-6">
            <label
              id="stockMinLabel"
              for="stock_min"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Stok Minimum
            </label>

            <input
              id="stock_min"
              name="stock_min"
              type="number"
              min="0"
              value="{{ old('stock_min', 5) }}"
              required
              placeholder="Contoh: 100"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >

            <p class="mt-2 text-xs text-slate-500" id="stockMinHelper">
              Batas minimum stok.
            </p>
          </div>

          <script>
            const categorySelect = document.getElementById('category_id');
            const stockUnitDisplay = document.getElementById('stock_unit_display');
            const sellingUnitDisplay = document.getElementById('selling_unit_display');
            const stockLabel = document.getElementById('stockLabel');
            const stockMinLabel = document.getElementById('stockMinLabel');
            const stockHelper = document.getElementById('stockHelper');
            const stockMinHelper = document.getElementById('stockMinHelper');

            function getUnitsByCategory(categoryName) {
              const categoryLower = (categoryName ?? '').trim().toLowerCase();

              if (categoryLower === 'tembakau') {
                return { stock: 'gram', selling: 'ons' };
              }

              if (categoryLower.includes('pack') || categoryLower.includes('kemasan')) {
                return { stock: 'pack', selling: 'pack' };
              }

              return { stock: 'pcs', selling: 'pcs' };
            }

            function updateProductUnitForm() {
              const selectedOption = categorySelect.options[categorySelect.selectedIndex];
              const categoryName = selectedOption?.textContent ?? '';
              const units = getUnitsByCategory(categoryName);

              stockUnitDisplay.value = units.stock;
              sellingUnitDisplay.value = units.selling;

              stockLabel.textContent = `Stok Awal (${units.stock})`;
              stockMinLabel.textContent = `Stok Minimum (${units.stock})`;
              stockHelper.textContent = `Masukkan stok dalam ${units.stock}.`;
              stockMinHelper.textContent = `Batas minimum stok dalam ${units.stock}.`;
            }

            categorySelect.addEventListener('change', updateProductUnitForm);

            // Initialize on page load if category is already selected
            if (categorySelect.value) {
              updateProductUnitForm();
            }
          </script>

        <div class="mt-6">
          <label
            for="description"
            class="mb-2 block text-sm font-medium text-slate-700"
          >
            Deskripsi
          </label>

          <textarea
            id="description"
            name="description"
            rows="4"
            placeholder="Deskripsi produk (opsional)"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >{{ old('description') }}</textarea>
        </div>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">
          Harga 
        </h2>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
          <div>
            <label
              for="buy_price"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Harga Beli
            </label>

            <input
              id="buy_price"
              name="buy_price"
              type="number"
              min="0"
              value="{{ old('buy_price', 0) }}"
              required
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >
          </div>

          <div>
            <label
              for="sell_price"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Harga Jual
            </label>

            <input
              id="sell_price"
              name="sell_price"
              type="number"
              min="0"
              value="{{ old('sell_price', 0) }}"
              required
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >
          </div>

      <div class="flex justify-end gap-3">
        <a
          href="{{ route('admin.products.index') }}"
          class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
        >
          Batal
        </a>

        <button
          type="submit"
          class="rounded-2xl bg-emerald-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/15 hover:bg-emerald-800"
        >
          Simpan Produk
        </button>
      </div>
    </form>
  </div>
@endsection