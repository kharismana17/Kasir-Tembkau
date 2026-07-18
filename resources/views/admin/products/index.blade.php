@extends('layouts.admin')

@section('title', 'Produk - Kasir Tembakau')

@section('content')
  <div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
          Master Data
        </p>

        <h1 class="mt-2 text-3xl font-semibold text-slate-900">
          Produk
        </h1>

        <p class="mt-2 text-slate-500">
          Kelola produk dan stok toko tembakau.
        </p>
      </div>

      <a
        href="{{ route('admin.products.create') }}"
        class="inline-flex items-center justify-center rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/15 transition hover:bg-emerald-800"
      >
        + Tambah Produk
      </a>
    </div>

    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <form
        method="GET"
        action="{{ route('admin.products.index') }}"
        class="grid gap-4 lg:grid-cols-4"
      >
        <div class="lg:col-span-2">
          <label for="search" class="mb-2 block text-sm font-medium text-slate-700">
            Cari Produk
          </label>

          <input
            id="search"
            name="search"
            type="text"
            value="{{ request('search') }}"
            placeholder="Cari nama, SKU, atau barcode..."
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >
        </div>

        <div>
          <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">
            Kategori
          </label>

          <select
            id="category_id"
            name="category_id"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >
            <option value="">Semua Kategori</option>

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

        <div>
          <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
            Status
          </label>

          <select
            id="status"
            name="status"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >
            <option value="">Semua Status</option>

            <option value="active" @selected(request('status') === 'active')>
              Aktif
            </option>

            <option value="inactive" @selected(request('status') === 'inactive')>
              Nonaktif
            </option>
          </select>
        </div>

        <div class="flex items-end gap-3 lg:col-span-4">
          <button
            type="submit"
            class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800"
          >
            Filter
          </button>

          <a
            href="{{ route('admin.products.index') }}"
            class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
          >
            Reset
          </a>
        </div>
      </form>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px]">
          <thead class="border-b border-slate-200 bg-slate-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                No
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Produk
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Barcode
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Kategori
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Harga Jual
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Stok
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Status
              </th>

              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-600">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            @forelse ($products as $product)
              <tr class="hover:bg-slate-50">
                <td class="px-6 py-4 text-sm text-slate-500">
                  {{ $products->firstItem() + $loop->index }}
                </td>

                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">
                    {{ $product->name }}
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    SKU: {{ $product->sku }}
                  </p>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                  <code class="rounded bg-slate-100 px-2 py-1 font-mono text-xs">
                    {{ $product->barcode ?? '-' }}
                  </code>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                  {{ $product->category?->name ?? '-' }}
                </td>

                <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                  Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                </td>

                <td class="px-6 py-4">
                  <div>
                    <p class="font-semibold
                      {{ $product->stock <= $product->stock_min ? 'text-rose-700' : 'text-slate-900' }}"
                    >
                      {{ $product->stock }} {{ $product->unit }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                      Minimum: {{ $product->stock_min }}
                    </p>
                  </div>
                </td>

                <td class="px-6 py-4">
                  @if ($product->is_active)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-800">
                      Aktif
                    </span>
                  @else
                    <span class="rounded-full bg-slate-200 px-3 py-1 text-sm font-medium text-slate-600">
                      Nonaktif
                    </span>
                  @endif
                </td>

                <td class="px-6 py-4">
                  <div class="flex justify-end gap-2">
                    <a
                      href="{{ route('admin.products.edit', $product) }}"
                      class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                      Edit
                    </a>

                    <form
                      method="POST"
                      action="{{ route('admin.products.destroy', $product) }}"
                      onsubmit="return confirm('Yakin ingin mengubah status produk ini?')"
                    >
                      @csrf
                      @method('DELETE')

                      <button
                        type="submit"
                        class="rounded-xl px-4 py-2 text-sm font-semibold
                          {{ $product->is_active
                            ? 'bg-rose-100 text-rose-700 hover:bg-rose-200'
                            : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}"
                      >
                        {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                  Belum ada produk.
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