@extends('layouts.admin')

@section('title', 'Stok Masuk - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-4xl space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-[#8B7355]">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#D4C5A9] text-[#6B5B3E]">
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

        <h1 class="text-3xl font-bold text-[#4A3728]">
          Stok Masuk
        </h1>

        <p class="mt-2 text-sm text-[#8B7A6B]">
          Tambahkan stok baru ke dalam inventori toko.
        </p>
      </div>

      <a
        href="{{ route('admin.stock.index') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#D4C5A9] bg-[#F5F0E8] px-4 py-2.5 text-sm font-semibold text-[#4A3728] shadow-sm transition hover:bg-[#E8DFD2] hover:border-[#C4B49C]"
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
    <section class="rounded-[28px] border border-[#D4C5A9] bg-[#FBF8F3] shadow-sm overflow-hidden">

      <div class="border-b border-[#D4C5A9] bg-[#F0EAE0] px-6 py-5">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#8B7A6B]">
          Produk yang dipilih
        </p>
      </div>

      <div class="p-5 sm:p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

          <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#D4C5A9] text-[#6B5B3E]">
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
              <p class="text-lg font-bold text-[#4A3728]">
                {{ $product->name }}
              </p>

              <p class="mt-1 text-sm text-[#8B7A6B]">
                SKU: {{ $product->sku }}
              </p>
            </div>
          </div>

          <div class="rounded-2xl border border-[#C4B49C] bg-[#F0EAE0] px-6 py-5 sm:min-w-[220px]">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#8B7355]">
              Stok Saat Ini
            </p>

            <p class="mt-1 text-2xl font-bold tracking-tight text-[#6B5B3E]">
              {{ $product->stock }}
              <span class="text-sm font-semibold text-[#6B5B3E]">
                {{ $product->stockUnit() }}
              </span>
            </p>
          </div>

        </div>
      </div>

    </section>


    {{-- FORM --}}
    <section class="rounded-[28px] border border-[#D4C5A9] bg-[#FBF8F3] shadow-sm">

      <div class="border-b border-[#D4C5A9] bg-[#F0EAE0] px-6 py-5">
        <h2 class="text-lg font-bold text-[#4A3728]">
          Detail Stok Masuk
        </h2>

        <p class="mt-1 text-sm text-[#8B7A6B]">
          Isi jumlah stok yang diterima dari supplier.
        </p>
      </div>

      <div class="p-5 sm:p-6">

        @if ($errors->any())
          <div class="mb-6 flex gap-3 rounded-xl border border-[#D4A0A0] bg-[#F8EEEE] p-4">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#8B5A5A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
              />
            </svg>

            <ul class="space-y-1 text-sm text-[#7A4A4A]">
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
                    class="block text-sm font-semibold text-[#5C4635]"
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
                        class="w-full rounded-2xl border border-[#C4B49C] bg-[#FDFBF8] px-5 py-4 pr-20 text-lg font-semibold text-[#4A3728] focus:border-[#8B7355] focus:ring-4 focus:ring-[#8B7355]/20"
                    />

                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-semibold text-[#8B7355]">
                        {{ $product->stockUnit() }}
                    </span>
                </div>

                @error('change')
                    <p class="text-sm text-[#8B5A5A]">
                        {{ $message }}
                    </p>
                @enderror

                <p class="text-xs text-[#8B7A6B]">
                    Masukkan jumlah stok yang ditambahkan.
                </p>
            </div>


            {{-- NOTE --}}
            <div class="space-y-2">
              <label
                for="note"
                class="block text-sm font-semibold text-[#4A3728]"
              >
                Catatan
                <span class="font-normal text-[#8B7A6B]">(Opsional)</span>
              </label>

              <textarea
                  id="note"
                  name="note"
                  rows="4"
                  class="w-full rounded-2xl border border-[#C4B49C] bg-[#FDFBF8] px-5 py-4 text-[#4A3728] focus:border-[#8B7355] focus:ring-4 focus:ring-[#8B7355]/20"
              >{{ old('note') }}</textarea>
              @error('note')
                <p class="text-sm text-[#8B5A5A]">
                  {{ $message }}
                </p>
              @enderror
            </div>

          </div>


          {{-- PREVIEW --}}
          <div class="rounded-2xl border border-[#D4C5A9] bg-[#F5F0E8] p-5">
            <div class="flex items-start gap-3">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#D4C5A9] text-[#6B5B3E]">
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
                <p class="text-sm font-semibold text-[#4A3728]">
                  Stok akan ditambahkan ke produk
                </p>

                <p class="mt-1 text-sm text-[#8B7355]">
                  Pastikan jumlah stok yang dimasukkan sudah sesuai dengan stok fisik yang diterima.
                </p>
              </div>
            </div>
          </div>


          {{-- ACTION --}}
          <div class="flex flex-col-reverse gap-3 border-t border-[#D4C5A9] pt-6 sm:flex-row sm:justify-end">

            <a
                href="{{ route('admin.stock.index') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-[#D4C5A9] bg-[#FDFBF8] px-5 py-3 text-sm font-semibold text-[#4A3728] transition hover:bg-[#F0EAE0]"
            >
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-[#8B7355] px-6 py-3 font-semibold text-[#FBF8F3] shadow transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#7A6348]"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7"/>
                </svg>

                Simpan Stok
            </button>

          </div>

        </form>

      </div>

    </section>

  </div>
@endsection