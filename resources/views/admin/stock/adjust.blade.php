@extends('layouts.admin')

@section('title', 'Penyesuaian Stok - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-4xl space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-[#6B4F3A]">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#EAD8C8]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
          </span>

          <span>Inventory</span>
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
          Penyesuaian Stok Manual
        </h1>

        <p class="mt-2 text-sm text-[#8A8179]">
          Tambah atau kurangi stok produk dengan alasan yang jelas.
        </p>
      </div>

      <a
        href="{{ route('admin.stock.index') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-sm font-semibold text-[#6B4F3A] shadow-sm transition hover:border-[#C68B59] hover:bg-[#FBF8F4]"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
          />
        </svg>

        Kembali ke Stok
      </a>
    </div>


    {{-- ALERT SUCCESS --}}
    @if (session('success'))
      <div class="flex gap-3 rounded-2xl border border-[#D8C4B2] bg-[#FBF3EC] p-4 text-sm text-[#6B4F3A]">
        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M5 13l4 4L19 7"
          />
        </svg>

        <span>{{ session('success') }}</span>
      </div>
    @endif


    {{-- ALERT ERROR --}}
    @if ($errors->any())
      <div class="flex gap-3 rounded-2xl border border-[#E8B4A8] bg-[#FFF3F0] p-4 text-sm text-[#A33A2B]">
        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
          />
        </svg>

        <ul class="space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif


    {{-- PRODUCT SUMMARY --}}
    <section class="overflow-hidden rounded-2xl border border-[#E7E1D9] bg-white shadow-sm">

      <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-5 py-4 sm:px-6">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
          Produk yang dipilih
        </p>
      </div>

      <div class="p-5 sm:p-6">
        <div class="grid gap-6 md:grid-cols-2">

          <div>
            <p class="text-sm font-semibold text-[#292522]">
              Produk
            </p>

            <p class="mt-2 text-lg font-bold text-[#292522]">
              {{ $product->name }}
            </p>

            <p class="mt-1 text-sm text-[#8A8179]">
              SKU: {{ $product->sku }}
            </p>

            <p class="mt-1 text-sm text-[#8A8179]">
              Kategori: {{ $product->category?->name ?? '-' }}
            </p>
          </div>

          <div class="rounded-2xl border border-[#E1D5C8] bg-[#FBF8F4] p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#8A8179]">
              Stok Saat Ini
            </p>

            <p class="mt-2 text-3xl font-bold tracking-tight text-[#292522]">
              {{ $product->stock }}
              <span class="text-sm font-semibold text-[#8A8179]">
                {{ $product->stockUnit() }}
              </span>
            </p>

            <p class="mt-1 text-sm text-[#8A8179]">
              Minimum: {{ $product->stock_min }} {{ $product->stockUnit() }}
            </p>
          </div>

        </div>
      </div>

    </section>


    {{-- ADJUSTMENT FORM --}}
    <section class="rounded-2xl border border-[#E7E1D9] bg-white shadow-sm">

      <div class="border-b border-[#EEE8E1] px-5 py-4 sm:px-6">
        <h2 class="text-base font-bold text-[#292522]">
          Detail Penyesuaian
        </h2>

        <p class="mt-1 text-sm text-[#8A8179]">
          Tentukan perubahan stok dan masukkan alasan penyesuaian.
        </p>
      </div>

      <div class="p-5 sm:p-6">

        <form
          method="POST"
          action="{{ route('admin.stock.adjust.store', $product) }}"
          class="space-y-6"
        >
          @csrf

          <div class="grid gap-6 md:grid-cols-2">

            {{-- ACTION --}}
            <div class="space-y-2">
              <label
                class="block text-sm font-semibold text-[#5C514A]"
                for="action"
              >
                Tipe Penyesuaian
              </label>

              <select
                id="action"
                name="action"
                class="w-full rounded-xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition hover:border-[#C68B59] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >
                <option value="add" @selected(old('action') === 'add')}>
                  Tambah stok
                </option>

                <option value="reduce" @selected(old('action') === 'reduce')}>
                  Kurangi stok
                </option>
              </select>
            </div>


            {{-- AMOUNT --}}
            <div class="space-y-2">
              <label
                class="block text-sm font-semibold text-[#5C514A]"
                for="amount"
              >
                Jumlah
              </label>

              <div class="relative">
                <input
                  id="amount"
                  name="amount"
                  type="number"
                  min="1"
                  value="{{ old('amount') }}"
                  required
                  placeholder="0"
                  class="w-full rounded-xl border border-[#D9CEC4] bg-white px-4 py-3 text-lg font-semibold text-[#292522] outline-none transition placeholder:text-[#C8BDB4] hover:border-[#C68B59] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                >

                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-semibold text-[#A3978D]">
                  {{ $product->stockUnit() }}
                </span>
              </div>
            </div>


            {{-- NOTE --}}
            <div class="space-y-2 md:col-span-2">
              <label
                class="block text-sm font-semibold text-[#5C514A]"
                for="note"
              >
                Alasan Penyesuaian
              </label>

              <textarea
                id="note"
                name="note"
                rows="4"
                required
                placeholder="Contoh: Stok rusak, stok fisik berbeda, atau koreksi stok"
                class="w-full resize-none rounded-xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#C68B59] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
              >{{ old('note') }}</textarea>
            </div>

          </div>


          {{-- STOCK PREVIEW --}}
          <div class="rounded-xl border border-dashed border-[#D8C4B2] bg-[#FBF8F4] p-4">

            <div class="flex items-start gap-3">

              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#EAD8C8] text-[#6B4F3A]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4m-4-8h6m0 0v6m0-6L10 14"
                  />
                </svg>
              </div>

              <div class="text-sm">
                <p class="font-semibold text-[#292522]">
                  Preview Perubahan Stok
                </p>

                <p class="mt-2 text-[#8A8179]">
                  <span class="font-semibold text-[#5C514A]">
                    Stok sebelum:
                  </span>

                  {{ $product->stock }} {{ $product->stockUnit() }}
                </p>

                <p class="mt-1 text-[#8A8179]">
                  <span class="font-semibold text-[#5C514A]">
                    Stok sesudah:
                  </span>

                  <span
                    id="stock-after"
                    class="font-semibold text-[#6B4F3A]"
                  >
                    -
                  </span>
                </p>
              </div>

            </div>

          </div>


          {{-- ACTION --}}
          <div class="flex flex-col-reverse gap-3 border-t border-[#EEE8E1] pt-6 sm:flex-row sm:justify-end">

            <a
              href="{{ route('admin.stock.index') }}"
              class="inline-flex items-center justify-center rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#FBF8F4]"
            >
              Batal
            </a>

            <button
              type="submit"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#292522] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3B3530] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>

              Simpan Penyesuaian
            </button>

          </div>

        </form>

      </div>

    </section>

  </div>


  @push('scripts')
    <script>
      const actionSelect = document.querySelector('#action');
      const amountInput = document.querySelector('#amount');
      const stockAfterEl = document.querySelector('#stock-after');
      const currentStock = {{ $product->stock }};

      function updateStockAfter() {
        const action = actionSelect.value;
        const amount = parseInt(amountInput.value, 10) || 0;
        let result = currentStock;

        if (action === 'add') {
          result = currentStock + amount;
        } else if (action === 'reduce') {
          result = currentStock - amount;
        }

        stockAfterEl.textContent = `${result} {{ $product->stockUnit() }}`;
      }

      actionSelect.addEventListener('change', updateStockAfter);
      amountInput.addEventListener('input', updateStockAfter);

      updateStockAfter();
    </script>
  @endpush
@endsection