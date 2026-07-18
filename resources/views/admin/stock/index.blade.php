@extends('layouts.admin')

@section('title', 'Stok - Kasir Tembakau')

@section('content')
  <div class="space-y-8">
    <div>
      <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
        Inventory
      </p>

      <h1 class="mt-2 text-3xl font-semibold text-slate-900">
        Stok Produk
      </h1>

      <p class="mt-2 text-slate-500">
        Kelola dan pantau stok produk toko.
      </p>
    </div>

    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <form
        method="GET"
        action="{{ route('admin.stock.index') }}"
        class="flex flex-col gap-4 sm:flex-row"
      >
        <input
          type="text"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari nama atau SKU produk..."
          class="flex-1 rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
        >

        <button
          type="submit"
          class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800"
        >
          Cari
        </button>

        <a
          href="{{ route('admin.stock.index') }}"
          class="rounded-2xl bg-slate-100 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-200"
        >
          Reset
        </a>
      </form>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
          <thead class="border-b border-slate-200 bg-slate-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Produk
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Kategori
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Stok Saat Ini
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Minimum
              </th>

              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-600">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            @forelse ($products as $product)
              <tr class="hover:bg-slate-50">
                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">
                    {{ $product->name }}
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    SKU: {{ $product->sku }}
                  </p>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                  {{ $product->category?->name ?? '-' }}
                </td>

                <td class="px-6 py-4">
                  <span class="text-lg font-semibold
                    {{ $product->stock <= $product->stock_min
                      ? 'text-rose-700'
                      : 'text-slate-900' }}"
                  >
                    {{ $product->stock }} {{ $product->stockUnit() }}
                  </span>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                  {{ $product->stock_min }} {{ $product->stockUnit() }}
                </td>

                <td class="px-6 py-4 text-right">
                  <a
                    href="{{ route('admin.stock.create', $product) }}"
                    class="rounded-xl bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-200"
                  >
                    Stok Masuk
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                  Belum ada produk aktif.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($products->hasPages())
        <div class="border-t border-slate-200 px-6 py-4">
          {{ $products->links() }}
        </div>
      @endif
    </div>
  </div>
@endsection