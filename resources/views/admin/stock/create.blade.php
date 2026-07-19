@extends('layouts.admin')

@section('title', 'Stok Masuk - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-4xl space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-emerald-700">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
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

        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
          Stok Masuk
        </h1>

        <p class="mt-2 text-sm text-slate-500">
          Tambahkan stok baru ke dalam inventori toko.
        </p>
      </div>

      <a
        href="{{ route('admin.stock.index') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
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


    {{-- PRODUCT SUMMARY --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

      <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4 sm:px-6">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">
          Produk yang dipilih
        </p>
      </div>

      <div class="p-5 sm:p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

          <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                />
              </svg>
            </div>

            <div>
              <p class="text-lg font-bold text-slate-900">
                {{ $product->name }}
              </p>

              <p class="mt-1 text-sm text-slate-500">
                SKU: {{ $product->sku }}
              </p>
            </div>
          </div>

          <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-5 py-3 sm:min-w-[190px]">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
              Stok Saat Ini
            </p>

            <p class="mt-1 text-2xl font-bold tracking-tight text-emerald-900">
              {{ $product->stock }}
              <span class="text-sm font-semibold text-emerald-700">
                {{ $product->stockUnit() }}
              </span>
            </p>
          </div>

        </div>
      </div>

    </section>


    {{-- FORM --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

      <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <h2 class="text-base font-bold text-slate-900">
          Detail Stok Masuk
        </h2>

        <p class="mt-1 text-sm text-slate-500">
          Isi jumlah stok yang diterima dari supplier.
        </p>
      </div>

      <div class="p-5 sm:p-6">

        @if ($errors->any())
          <div class="mb-6 flex gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
              />
            </svg>

            <ul class="space-y-1 text-sm text-rose-700">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form
          method="POST"
          action="{{ route('admin.stock.store', $product) }}"
          class="space-y-6"
        >
          @csrf

          <div class="grid gap-6 md:grid-cols-2">

            {{-- STOCK INPUT --}}
            <div class="space-y-2">
              <label
                for="change"
                class="block text-sm font-semibold text-slate-700"
              >
                Jumlah Stok Masuk
              </label>

              <div class="relative">
                <input
                  id="change"
                  name="change"
                  type="number"
                  min="1"
                  value="{{ old('change') }}"
                  required
                  autofocus
                  placeholder="0"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 pr-20 text-lg font-semibold text-slate-900 outline-none transition placeholder:text-slate-300 hover:border-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                >

                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-semibold text-slate-400">
                  {{ $product->stockUnit() }}
                </span>
              </div>

              @error('change')
                <p class="text-sm text-rose-600">
                  {{ $message }}
                </p>
              @enderror

              <p class="text-xs text-slate-400">
                Masukkan jumlah stok yang ditambahkan.
              </p>
            </div>


            {{-- NOTE --}}
            <div class="space-y-2">
              <label
                for="note"
                class="block text-sm font-semibold text-slate-700"
              >
                Catatan
                <span class="font-normal text-slate-400">(Opsional)</span>
              </label>

              <textarea
                id="note"
                name="note"
                rows="4"
                placeholder="Contoh: Restock dari supplier"
                class="w-full resize-none rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
              >{{ old('note') }}</textarea>

              @error('note')
                <p class="text-sm text-rose-600">
                  {{ $message }}
                </p>
              @enderror
            </div>

          </div>


          {{-- PREVIEW --}}
          <div class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50/60 p-4">
            <div class="flex items-start gap-3">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                  />
                </svg>
              </div>

              <div>
                <p class="text-sm font-semibold text-emerald-900">
                  Stok akan ditambahkan ke produk
                </p>

                <p class="mt-1 text-sm text-emerald-700">
                  Pastikan jumlah stok yang dimasukkan sudah sesuai dengan stok fisik yang diterima.
                </p>
              </div>
            </div>
          </div>


          {{-- ACTION --}}
          <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">

            <a
              href="{{ route('admin.stock.index') }}"
              class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
              Batal
            </a>

            <button
              type="submit"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>

              Simpan Stok
            </button>

          </div>

        </form>

      </div>

    </section>

  </div>
@endsection