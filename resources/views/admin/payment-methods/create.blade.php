@extends('layouts.admin')

@section('title', 'Tambah Metode Pembayaran - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-3xl space-y-8">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

      <div>

        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-[#6B4F3A]">

          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#EAD8C8]">

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
                d="M12 4v16m8-8H4"
              />
            </svg>

          </span>

          <span>Master Data</span>

        </div>


        <h1 class="text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
          Tambah Metode Pembayaran
        </h1>

        <p class="mt-2 text-sm text-[#8A8179]">
          Tambahkan metode pembayaran baru untuk transaksi POS.
        </p>

      </div>


      <a
        href="{{ route('admin.payment-methods.index') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#E1D5C8] bg-white px-4 py-2.5 text-sm font-semibold text-[#6B4F3A] shadow-sm transition hover:border-[#C68B59] hover:bg-[#F7F5F0]"
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
    <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

      <div class="border-b border-[#E7E1D9] bg-[#F7F5F0] px-6 py-5">

        <div class="flex items-center gap-3">

          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#292522] text-[#C68B59]">

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
                d="M3 10h18M7 15h2m2 0h2m-8 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>

          </div>

          <div>

            <h2 class="text-base font-bold text-[#292522]">
              Informasi Metode Pembayaran
            </h2>

            <p class="mt-1 text-sm text-[#8A8179]">
              Isi data metode pembayaran yang akan digunakan di POS.
            </p>

          </div>

        </div>

      </div>


      <div class="p-6">

        <form
          method="POST"
          action="{{ route('admin.payment-methods.store') }}"
          class="space-y-6"
        >

          @csrf


          {{-- NAMA --}}
          <div class="space-y-2">

            <label
              for="name"
              class="block text-sm font-semibold text-[#292522]"
            >
              Nama Metode Pembayaran
            </label>

            <input
              id="name"
              name="name"
              type="text"
              value="{{ old('name') }}"
              required
              autofocus
              placeholder="Contoh: Tunai"
              class="w-full rounded-xl border border-[#D8D0C8] bg-white px-4 py-3.5 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
            >

            @error('name')
              <p class="text-sm text-rose-600">
                {{ $message }}
              </p>
            @enderror

            <p class="text-xs text-[#A3978D]">
              Nama yang akan ditampilkan pada halaman transaksi.
            </p>

          </div>


          {{-- CODE --}}
          <div class="space-y-2">

            <label
              for="code"
              class="block text-sm font-semibold text-[#292522]"
            >
              Kode Metode
            </label>

            <input
              id="code"
              name="code"
              type="text"
              value="{{ old('code') }}"
              required
              placeholder="Contoh: CASH"
              class="w-full rounded-xl border border-[#D8D0C8] bg-white px-4 py-3.5 text-sm font-medium uppercase text-[#292522] outline-none transition placeholder:normal-case placeholder:text-[#A3978D] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
            >

            @error('code')
              <p class="text-sm text-rose-600">
                {{ $message }}
              </p>
            @enderror

            <p class="text-xs text-[#A3978D]">
              Gunakan kode singkat dan mudah dikenali, seperti CASH atau QRIS.
            </p>

          </div>


          {{-- STATUS --}}
          <div class="rounded-2xl border border-[#E7E1D9] bg-[#F7F5F0] p-4">

            <label class="flex cursor-pointer items-center gap-3">

              <input
                type="checkbox"
                id="is_active"
                name="is_active"
                checked
                class="h-4 w-4 rounded border-[#C9C0B8] text-[#6B4F3A] focus:ring-[#C68B59]"
              >

              <span>

                <span class="block text-sm font-semibold text-[#292522]">
                  Metode pembayaran aktif
                </span>

                <span class="mt-1 block text-xs text-[#8A8179]">
                  Metode ini dapat dipilih saat proses transaksi POS.
                </span>

              </span>

            </label>

          </div>


          {{-- ACTION --}}
          <div class="flex flex-col-reverse gap-3 border-t border-[#E7E1D9] pt-6 sm:flex-row sm:justify-end">

            <a
              href="{{ route('admin.payment-methods.index') }}"
              class="inline-flex items-center justify-center rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F7F5F0]"
            >
              Batal
            </a>

            <button
              type="submit"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#6B4F3A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#543B2B] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
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

              Simpan Metode

            </button>

          </div>

        </form>

      </div>

    </section>

  </div>
@endsection