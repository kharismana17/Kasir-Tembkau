@extends('layouts.admin')

@section('title', 'Tambah Produk - Kasir Tembakau')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                </div>

                <div>

                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#6B4F3A]">
                        Master Data
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522]">
                        Tambah Produk
                    </h1>

                </div>

            </div>

            <p class="mt-3 text-sm text-[#8A8179]">
                Tambahkan produk baru ke katalog toko.
            </p>

        </div>


        <a
            href="{{ route('admin.products.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-sm font-semibold text-[#6B4F3A] shadow-sm transition hover:border-[#C68B59] hover:bg-[#FBF8F4]"
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

        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">

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

            <div>

                <p class="text-sm font-semibold text-rose-700">
                    Terdapat kesalahan pada form
                </p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-700">

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
        action="{{ route('admin.products.store') }}"
        class="space-y-6"
    >

        @csrf


        {{-- INFORMASI PRODUK --}}
        <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5">

                <div class="flex items-center gap-3">

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

                    <div>

                        <h2 class="text-base font-bold text-[#292522]">
                            Informasi Produk
                        </h2>

                        <p class="mt-1 text-sm text-[#8A8179]">
                            Masukkan informasi dasar produk.
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
                            class="mb-2 block text-sm font-semibold text-[#292522]"
                        >
                            Nama Produk
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            placeholder="Contoh: Gudang Garam Surya 12"
                            class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                        >

                    </div>
               
                    {{-- KATEGORI --}}
                    <div>

                        <label
                            for="category_id"
                            class="mb-2 block text-sm font-semibold text-[#292522]"
                        >
                            Kategori
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            required
                            class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                        >

                            <option value="">
                                Pilih Kategori
                            </option>

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


                    {{-- SATUAN STOK --}}
                    <div>

                        <label
                            for="stock_unit_display"
                            class="mb-2 block text-sm font-semibold text-[#292522]"
                        >
                            Satuan Stok
                        </label>

                        <input
                            id="stock_unit_display"
                            type="text"
                            readonly
                            placeholder="Otomatis"
                            class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm font-semibold text-[#6B4F3A] outline-none"
                        >

                        <p class="mt-2 text-xs text-[#A3978D]">
                            Ditentukan otomatis berdasarkan kategori.
                        </p>

                    </div>


                    {{-- SATUAN PENJUALAN --}}
                    <div>

                        <label
                            for="selling_unit_display"
                            class="mb-2 block text-sm font-semibold text-[#292522]"
                        >
                            Satuan Penjualan
                        </label>

                        <input
                            id="selling_unit_display"
                            type="text"
                            readonly
                            placeholder="Otomatis"
                            class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm font-semibold text-[#6B4F3A] outline-none"
                        >

                        <p class="mt-2 text-xs text-[#A3978D]">
                            Ditentukan otomatis berdasarkan kategori.
                        </p>

                    </div>


                    {{-- STOK AWAL --}}
                    <div>

                        <label
                            id="stockLabel"
                            for="stock"
                            class="mb-2 block text-sm font-semibold text-[#292522]"
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
                            class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                        >

                        <p
                            id="stockHelper"
                            class="mt-2 text-xs text-[#A3978D]"
                        >
                            Masukkan stok.

                        </p>

                    </div>


                    {{-- STOK MINIMUM --}}
                    <div>

                        <label
                            id="stockMinLabel"
                            for="stock_min"
                            class="mb-2 block text-sm font-semibold text-[#292522]"
                        >
                            Stok Minimum
                        </label>

                        <input
                            id="stock_min"
                            name="stock_min"
                            type="number"
                            min="0"
                            value="{{ old('stock_min', $storeSettings->default_stock_min ?? 5) }}"
                            required
                            placeholder="Contoh: 100"
                            class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                        >

                        <p
                            id="stockMinHelper"
                            class="mt-2 text-xs text-[#A3978D]"
                        >
                            Batas minimum stok.

                        </p>

                    </div>

                </div>


                {{-- DESKRIPSI --}}
                <div class="mt-6">

                    <label
                        for="description"
                        class="mb-2 block text-sm font-semibold text-[#292522]"
                    >
                        Deskripsi
                        <span class="font-normal text-[#A3978D]">
                            (Opsional)
                        </span>
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Deskripsi produk (opsional)"
                        class="w-full resize-none rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                    >{{ old('description') }}</textarea>

                </div>

            </div>

        </section>


        {{-- HARGA --}}
        <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5">

                <div class="flex items-center gap-3">

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
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#292522]">
                            Harga Produk
                        </h2>

                        <p class="mt-1 text-sm text-[#8A8179]">
                            Tentukan harga beli dan harga jual produk.
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
                            class="mb-2 block text-sm font-semibold text-[#292522]"
                        >
                            Harga Beli
                        </label>

                        <div class="relative">

                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-[#A3978D]">
                                Rp
                            </span>

                            <input
                                id="buy_price"
                                name="buy_price"
                                type="number"
                                min="0"
                                value="{{ old('buy_price', 0) }}"
                                required
                                class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 pl-12 text-sm text-[#292522] outline-none transition hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                            >

                        </div>

                    </div>


                    {{-- HARGA JUAL --}}
                    <div>

                        <label
                            for="sell_price"
                            class="mb-2 block text-sm font-semibold text-[#292522]"
                        >
                            Harga Jual
                        </label>

                        <div class="relative">

                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-[#A3978D]">
                                Rp
                            </span>

                            <input
                                id="sell_price"
                                name="sell_price"
                                type="number"
                                min="0"
                                value="{{ old('sell_price', 0) }}"
                                required
                                class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 pl-12 text-sm text-[#292522] outline-none transition hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
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
                class="inline-flex items-center justify-center rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#FBF8F4]"
            >
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#292522] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-black/10 transition hover:bg-[#3B3530] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
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
                        d="M5 13l4 4L19 7"
                    />
                </svg>

                Simpan Produk

            </button>

        </div>

    </form>

</div>


@push('scripts')

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


function updateProductUnitForm() {

    const selectedOption =
        categorySelect.options[categorySelect.selectedIndex];

    const categoryName =
        selectedOption?.textContent ?? '';

    const units =
        getUnitsByCategory(categoryName);


    stockUnitDisplay.value = units.stock;

    sellingUnitDisplay.value = units.selling;


    stockLabel.textContent =
        `Stok Awal (${units.stock})`;

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


if (categorySelect.value) {

    updateProductUnitForm();

}

</script>

@endpush

@endsection