@extends('layouts.admin')

@section('title', 'Tambah Kategori - Kasir Tembakau')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

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

                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#A56F45]">
                        Master Data
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522]">
                        Tambah Kategori
                    </h1>

                </div>

            </div>

            <p class="mt-3 text-sm text-[#8A8179]">
                Tambahkan kategori produk baru untuk mengelompokkan inventori toko.
            </p>

        </div>


        <a
            href="{{ route('admin.categories.index') }}"
            class="inline-flex w-fit items-center justify-center gap-2 rounded-2xl border border-[#E1D5C8] bg-white px-4 py-3 text-sm font-semibold text-[#6B4F3A] shadow-sm transition hover:border-[#C68B59] hover:bg-[#F7F5F0]"
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

            Kembali ke Kategori

        </a>

    </div>


    {{-- FORM CARD --}}
    <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

        {{-- CARD HEADER --}}
        <div class="border-b border-[#E7E1D9] bg-[#F7F5F0] px-6 py-5">

            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#A3978D]">
                Informasi Kategori
            </p>

            <h2 class="mt-2 text-lg font-bold text-[#292522]">
                Detail Kategori
            </h2>

            <p class="mt-1 text-sm text-[#8A8179]">
                Isi informasi kategori yang akan digunakan pada produk.
            </p>

        </div>


        {{-- FORM --}}
        <div class="p-6">

            @if ($errors->any())

                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">

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

            @endif


            <form
                method="POST"
                action="{{ route('admin.categories.store') }}"
                class="space-y-6"
            >

                @csrf


                {{-- NAME --}}
                <div class="space-y-2">

                    <label
                        for="name"
                        class="block text-sm font-semibold text-[#292522]"
                    >
                        Nama Kategori
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Contoh: Tembakau"
                        class="w-full rounded-2xl border border-[#D8D1C8] bg-white px-4 py-3.5 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#C68B59] focus:border-[#6B4F3A] focus:ring-4 focus:ring-[#C68B59]/10"
                    >

                    <p class="text-xs text-[#A3978D]">
                        Gunakan nama kategori yang jelas dan mudah dikenali.
                    </p>

                    @error('name')

                        <p class="text-sm text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- DESCRIPTION --}}
                <div class="space-y-2">

                    <label
                        for="description"
                        class="block text-sm font-semibold text-[#292522]"
                    >
                        Deskripsi
                        <span class="font-normal text-[#A3978D]">
                            (Opsional)
                        </span>
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Contoh: Kategori untuk berbagai jenis tembakau..."
                        class="w-full resize-none rounded-2xl border border-[#D8D1C8] bg-white px-4 py-3.5 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#C68B59] focus:border-[#6B4F3A] focus:ring-4 focus:ring-[#C68B59]/10"
                    >{{ old('description') }}</textarea>

                    @error('description')

                        <p class="text-sm text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- INFO --}}
                <div class="rounded-2xl border border-[#E1D5C8] bg-[#F7F5F0] p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#292522] text-[#C68B59]">

                            <svg
                                class="h-4 w-4"
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

                        </div>

                        <div>

                            <p class="text-sm font-semibold text-[#6B4F3A]">
                                Informasi Kategori
                            </p>

                            <p class="mt-1 text-sm leading-6 text-[#8A8179]">
                                Kategori akan digunakan untuk membantu pengelolaan dan pencarian produk di sistem kasir.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ACTION --}}
                <div class="flex flex-col-reverse gap-3 border-t border-[#E7E1D9] pt-6 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F7F5F0]"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#292522] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#292522]/10 transition hover:bg-[#3A332E] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
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

                        Simpan Kategori

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>

@endsection