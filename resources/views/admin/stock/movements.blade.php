@extends('layouts.admin')

@section('title', 'Riwayat Stok - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    <div>
      <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
        Inventory
      </p>

      <h1 class="mt-2 text-3xl font-semibold text-slate-900">
        Riwayat Stok
      </h1>

      <p class="mt-2 text-slate-500">
        Lihat seluruh perubahan stok produk.
      </p>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <form
        method="GET"
        action="{{ route('admin.stock.movements') }}"
        class="grid gap-4 md:grid-cols-3"
      >
        <input
          type="text"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari produk atau SKU..."
          class="rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
        >

        <select
          name="type"
          class="rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
        >
          <option value="">Semua Tipe</option>

          <option
            value="stock_in"
            @selected(request('type') === 'stock_in')
          >
            Stok Masuk
          </option>

          <option
            value="stock_out"
            @selected(request('type') === 'stock_out')
          >
            Stok Keluar
          </option>

          <option
            value="sale"
            @selected(request('type') === 'sale')
          >
            Penjualan
          </option>
        </select>

        <div class="flex gap-3">
          <button
            type="submit"
            class="flex-1 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800"
          >
            Filter
          </button>

          <a
            href="{{ route('admin.stock.movements') }}"
            class="rounded-2xl bg-slate-100 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-200"
          >
            Reset
          </a>
        </div>
      </form>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px]">

          <thead class="border-b border-slate-200 bg-slate-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Waktu
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Produk
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Perubahan
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Tipe
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                User
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Catatan
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">

            @forelse ($movements as $movement)
              <tr class="hover:bg-slate-50">

                <td class="px-6 py-4 text-sm text-slate-600">
                  {{ $movement->created_at->format('d M Y H:i') }}
                </td>

                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">
                    {{ $movement->product?->name ?? '-' }}
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    SKU: {{ $movement->product?->sku ?? '-' }}
                  </p>
                </td>

                <td class="px-6 py-4">
                  <span class="font-semibold
                    {{ $movement->change > 0
                      ? 'text-emerald-700'
                      : 'text-rose-700' }}"
                  >
                    {{ $movement->change > 0 ? '+' : '' }}{{ $movement->change }} {{ $movement->unitLabel() }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  @if ($movement->type === 'stock_in')
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                      Stok Masuk
                    </span>
                  @elseif ($movement->type === 'stock_out')
                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800">
                      Stok Keluar
                    </span>
                  @elseif ($movement->type === 'sale')
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                      Penjualan
                    </span>
                  @else
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                      {{ ucfirst($movement->type) }}
                    </span>
                  @endif
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                  {{ $movement->user?->name ?? '-' }}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                  {{ $movement->note ?? '-' }}
                </td>

              </tr>

            @empty

              <tr>
                <td
                  colspan="6"
                  class="px-6 py-12 text-center text-sm text-slate-500"
                >
                  Belum ada riwayat perubahan stok.
                </td>
              </tr>

            @endforelse

          </tbody>

        </table>
      </div>

      @if ($movements->hasPages())
        <div class="border-t border-slate-200 px-6 py-4">
          {{ $movements->links() }}
        </div>
      @endif
    </div>

  </div>
@endsection