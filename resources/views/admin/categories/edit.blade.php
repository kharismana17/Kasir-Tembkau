@extends('layouts.admin')

@section('title', 'Edit Kategori - Kasir Tembakau')

@section('content')

<div class="mx-auto max-w-3xl space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
                Master Data
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#292522]">
                Edit Kategori
            </h1>

            <p class="mt-2 text-sm text-[#8A8179]">
                Perbarui informasi kategori produk.
            </p>

        </div>

        <a
            href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-sm font-semibold text-[#6B4F3A] shadow-sm transition hover:border-[#C68B59] hover:bg-[#C68B59] hover:text-white"
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

            Kembali
        </a>

    </div>


    {{-- FORM CARD --}}
    <div class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

        {{-- CARD HEADER --}}
        <div class="border-b border-[#E7E1D9] bg-[#292522] px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#C68B59] text-white">

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
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 8.5-8.5z"
                        />
                    </svg>

                </div>

                <div>

                    <h2 class="text-base font-semibold text-white">
                        Informasi Kategori
                    </h2>

                    <p class="mt-1 text-sm text-[#B8AEA5]">
                        Ubah data kategori sesuai kebutuhan.
                    </p>

                </div>

            </div>

        </div>


        {{-- FORM --}}
        <div class="p-6">

            <form
                method="POST"
                action="{{ route('admin.categories.update', $category) }}"
                class="space-y-6"
            >

                @csrf
                @method('PUT')


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
                        value="{{ old('name', $category->name) }}"
                        required
                        autofocus
                        class="w-full rounded-xl border border-[#D8D0C8] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                    >

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
                        class="w-full resize-none rounded-xl border border-[#D8D0C8] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
                    >{{ old('description', $category->description) }}</textarea>

                    @error('description')
                        <p class="text-sm text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ACTION --}}
                <div class="flex flex-col-reverse gap-3 border-t border-[#E7E1D9] pt-6 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F7F5F0]"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C68B59] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#B4774B] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
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

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection