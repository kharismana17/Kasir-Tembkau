@extends('layouts.admin')

@section('title', 'Stok Opname - Kasir Tembakau')

@section('content')

<div class="space-y-6">

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
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5h6M9 13h6m-6 4h4"
                        />
                    </svg>

                </div>

                <div>

                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#6B4F3A]">
                        Inventory Control
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522]">
                        Stok Opname
                    </h1>

                </div>

            </div>

            <p class="mt-3 text-sm text-[#8A8179]">
                Cocokkan stok fisik dengan data sistem untuk memastikan akurasi inventaris toko.
            </p>

        </div>


        <div class="inline-flex w-fit items-center gap-2 rounded-2xl border border-[#E1D5C8] bg-white px-4 py-3 shadow-sm">

            <div class="h-2 w-2 rounded-full bg-[#C68B59]"></div>

            <span class="text-sm font-semibold text-[#6B4F3A]">
                Sesi Opname Aktif
            </span>

        </div>

    </div>


    {{-- ALERT SUCCESS --}}
    @if (session('success'))

        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">

            <svg
                class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600"
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

            <p class="text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </p>

        </div>

    @endif


    {{-- ALERT ERROR --}}
    @if ($errors->any())

        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">

            <div class="flex items-start gap-3">

                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-rose-600"
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

                <ul class="space-y-1 text-sm text-rose-700">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('admin.stock.opname.store') }}"
        class="space-y-5"
    >

        @csrf


        {{-- SUMMARY --}}
        <div class="grid gap-4 md:grid-cols-3">

            {{-- TOTAL PRODUK --}}
            <div class="rounded-3xl border border-[#E7E1D9] bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
                            Produk Aktif
                        </p>

                        <p class="mt-4 text-3xl font-bold tracking-tight text-[#292522]">
                            {{ $products->count() }}
                        </p>

                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FBF8F4] text-[#6B4F3A]">

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

                </div>

                <p class="mt-2 text-sm text-[#8A8179]">
                    Produk yang akan diperiksa
                </p>

            </div>


            {{-- LOW STOCK --}}
            <div class="rounded-3xl border border-[#E7E1D9] bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
                            Stok Menipis
                        </p>

                        <p class="mt-4 text-3xl font-bold tracking-tight text-rose-600">
                            {{ $lowStockCount }}
                        </p>

                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600">

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
                                d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3.14h15.64a2 2 0 001.71-3.14l-7.82-13a2 2 0 00-3.42 0z"
                            />
                        </svg>

                    </div>

                </div>

                <p class="mt-2 text-sm text-[#8A8179]">
                    Produk yang perlu diperhatikan
                </p>

            </div>


            {{-- NOTE --}}
            <div class="rounded-3xl border border-[#E1D5C8] bg-[#FBF8F4] p-5">

                <label
                    for="opname_note"
                    class="block text-xs font-bold uppercase tracking-[0.18em] text-[#6B4F3A]"
                >
                    Catatan Opname
                </label>

                <textarea
                    id="opname_note"
                    name="opname_note"
                    rows="3"
                    placeholder="Tambahkan catatan opname..."
                    class="mt-3 w-full resize-none rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                >{{ old('opname_note') }}</textarea>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            {{-- TABLE HEADER --}}
            <div class="flex flex-col gap-3 border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="text-base font-bold text-[#292522]">
                        Daftar Stok Produk
                    </h2>

                    <p class="mt-1 text-sm text-[#8A8179]">
                        Masukkan jumlah stok fisik yang ditemukan saat pemeriksaan.
                    </p>

                </div>

                <span class="rounded-xl bg-[#292522] px-3 py-2 text-xs font-bold text-white">
                    {{ $products->count() }} PRODUK
                </span>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[1100px]">

                    <thead class="border-b border-[#EEE8E1] bg-white">

                        <tr>

                            <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#A3978D]">
                                Produk
                            </th>

                            <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#A3978D]">
                                Barcode
                            </th>

                            <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#A3978D]">
                                Kategori
                            </th>

                            <th class="px-6 py-4 text-right text-[11px] font-bold uppercase tracking-wider text-[#A3978D]">
                                Sistem
                            </th>

                            <th class="px-6 py-4 text-right text-[11px] font-bold uppercase tracking-wider text-[#A3978D]">
                                Fisik
                            </th>

                            <th class="px-6 py-4 text-right text-[11px] font-bold uppercase tracking-wider text-[#A3978D]">
                                Selisih
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-[#EEE8E1]">

                        @foreach ($products as $product)

                            <tr class="group transition hover:bg-[#FBF9F6]">

                                {{-- PRODUCT --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#FBF8F4] text-sm font-bold text-[#6B4F3A]">
                                            {{ strtoupper(substr($product->name, 0, 1)) }}
                                        </div>

                                        <div>

                                            <p class="font-bold text-[#292522]">
                                                {{ $product->name }}
                                            </p>

                                            <p class="mt-1 text-xs text-[#A3978D]">
                                                SKU: {{ $product->sku }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- BARCODE --}}
                                <td class="px-6 py-5 text-sm text-[#8A8179]">
                                    {{ $product->barcode ?? '-' }}
                                </td>


                                {{-- CATEGORY --}}
                                <td class="px-6 py-5">

                                    <span class="rounded-lg bg-[#FBF8F4] px-2.5 py-1 text-xs font-semibold text-[#6B4F3A]">
                                        {{ $product->category?->name ?? '-' }}
                                    </span>

                                </td>


                                {{-- SYSTEM STOCK --}}
                                <td class="px-6 py-5 text-right">

                                    <p class="text-sm font-bold text-[#292522]">
                                        {{ $product->stock }}
                                    </p>

                                    <p class="mt-1 text-[10px] font-medium uppercase text-[#A3978D]">
                                        {{ $product->stockUnit() }}
                                    </p>

                                </td>


                                {{-- PHYSICAL STOCK --}}
                                <td class="px-6 py-5">

                                    <input
                                        type="number"
                                        name="stock_physical[{{ $product->id }}]"
                                        value="{{ old('stock_physical.' . $product->id, $product->stock) }}"
                                        min="0"
                                        step="1"
                                        data-system-stock="{{ $product->stock }}"
                                        data-stock-unit="{{ $product->stockUnit() }}"
                                        class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-right text-sm font-semibold text-[#292522] outline-none transition hover:border-[#B8AEA5] focus:border-[#C68B59] focus:bg-white focus:ring-4 focus:ring-[#C68B59]/10"
                                        onchange="updateDifference(this)"
                                    >

                                </td>


                                {{-- DIFFERENCE --}}
                                <td class="px-6 py-5 text-right">

                                    <span class="difference inline-flex rounded-xl bg-slate-100 px-3 py-2 text-sm font-bold text-[#292522]">
                                        0 {{ $product->stockUnit() }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        {{-- FOOTER ACTION --}}
        <div class="flex flex-col gap-4 rounded-3xl border border-[#E7E1D9] bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-start gap-3">

                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-[#6B4F3A]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"
                    />
                </svg>

                <p class="text-sm leading-6 text-[#8A8179]">
                    Pastikan semua stok fisik sudah diperiksa dan diisi sebelum menyimpan hasil opname.
                </p>

            </div>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#292522] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-black/10 transition hover:bg-[#3B3530] hover:shadow-xl active:scale-[0.99]"
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
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>

                Simpan Stok Opname

            </button>

        </div>

    </form>

</div>


@push('scripts')

<script>

function updateDifference(input) {

    const systemStock = parseInt(input.dataset.systemStock, 10) || 0;

    const physicalStock = parseInt(input.value, 10) || 0;

    const row = input.closest('tr');

    const diff = physicalStock - systemStock;

    const label = row.querySelector('.difference');


    label.textContent = `${diff > 0 ? '+' : ''}${diff} ${input.dataset.stockUnit}`;


    label.classList.remove(
        'bg-emerald-50',
        'text-emerald-700',
        'bg-rose-50',
        'text-rose-700',
        'bg-slate-100',
        'text-[#292522]'
    );


    if (diff > 0) {

        label.classList.add(
            'bg-emerald-50',
            'text-emerald-700'
        );

    } else if (diff < 0) {

        label.classList.add(
            'bg-rose-50',
            'text-rose-700'
        );

    } else {

        label.classList.add(
            'bg-slate-100',
            'text-[#292522]'
        );

    }

}


document
    .querySelectorAll('input[name^="stock_physical["]')
    .forEach(input => {

        updateDifference(input);

    });

</script>

@endpush

@endsection