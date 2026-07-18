@extends('layouts.admin')

@section('title', 'Stok Masuk - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-3xl space-y-8">

    <div>
      <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
        Inventory
      </p>

      <h1 class="mt-2 text-3xl font-semibold text-slate-900">
        Stok Masuk
      </h1>

      <p class="mt-2 text-slate-500">
        Tambahkan stok baru untuk produk.
      </p>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

      <div class="mb-6 rounded-2xl bg-slate-50 p-4">
        <p class="text-sm text-slate-500">
          Produk
        </p>

        <p class="mt-1 text-lg font-semibold text-slate-900">
          {{ $product->name }}
        </p>

        <p class="mt-1 text-sm text-slate-500">
          SKU: {{ $product->sku }}
        </p>

        <p class="mt-2 text-sm text-slate-600">
          Stok saat ini:
          <span class="font-semibold">
            {{ $product->stock }} {{ $product->stockUnit() }}
          </span>
        </p>
      </div>

      @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4">
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

        <div class="space-y-2">
          <label
            for="change"
            class="block text-sm font-medium text-slate-700"
          >
            Jumlah Stok Masuk
          </label>

          <input
            id="change"
            name="change"
            type="number"
            min="1"
            value="{{ old('change') }}"
            required
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >

          @error('change')
            <p class="text-sm text-rose-600">
              {{ $message }}
            </p>
          @enderror
        </div>

        <div class="space-y-2">
          <label
            for="note"
            class="block text-sm font-medium text-slate-700"
          >
            Catatan
          </label>

          <textarea
            id="note"
            name="note"
            rows="4"
            placeholder="Contoh: Restock dari supplier"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >{{ old('note') }}</textarea>

          @error('note')
            <p class="text-sm text-rose-600">
              {{ $message }}
            </p>
          @enderror
        </div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
          <a
            href="{{ route('admin.stock.index') }}"
            class="rounded-2xl bg-slate-100 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-200"
          >
            Batal
          </a>

          <button
            type="submit"
            class="rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/15 hover:bg-emerald-800"
          >
            Simpan Stok
          </button>
        </div>
      </form>

    </div>

  </div>
@endsection