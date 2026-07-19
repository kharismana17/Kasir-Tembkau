@extends('layouts.admin')

@section('title', 'Edit Metode Pembayaran - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-3xl space-y-8">

    {{-- PAGE HEADER --}}
    <div>
      <div class="mb-3 flex items-center gap-3">

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
              d="M12 6v12m6-6H6"
            />
          </svg>
        </div>

        <div>
          <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#A66F43]">
            Master Data
          </p>

          <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
            Edit Metode Pembayaran
          </h1>
        </div>

      </div>

      <p class="text-sm text-[#8A8179]">
        Perbarui informasi metode pembayaran yang digunakan dalam transaksi.
      </p>
    </div>


    {{-- FORM CARD --}}
    <div class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

      {{-- CARD HEADER --}}
      <div class="border-b border-[#EEEAE4] bg-[#FAF9F6] px-6 py-5">

        <h2 class="text-base font-bold text-[#292522]">
          Informasi Metode Pembayaran
        </h2>

        <p class="mt-1 text-sm text-[#8A8179]">
          Pastikan data metode pembayaran sudah sesuai.
        </p>

      </div>


      <div class="p-6">

        @if ($errors->any())
          <div class="mb-6 flex items-start gap-3 rounded-2xl border border-[#E8CFC2] bg-[#FBF1EC] p-4">

            <svg
              class="mt-0.5 h-5 w-5 shrink-0 text-[#A94A32]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
              />
            </svg>

            <ul class="space-y-1 text-sm text-[#A94A32]">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>

          </div>
        @endif


        <form
          method="POST"
          action="{{ route('admin.payment-methods.update', $paymentMethod) }}"
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
              Nama Metode
            </label>

            <input
              id="name"
              name="name"
              type="text"
              value="{{ old('name', $paymentMethod->name) }}"
              required
              autofocus
              placeholder="Contoh: Tunai"
              class="w-full rounded-2xl border border-[#D9D2CA] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#B8AEA5] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
            >

            @error('name')
              <p class="text-sm text-[#A94A32]">
                {{ $message }}
              </p>
            @enderror

          </div>


          {{-- CODE --}}
          <div class="space-y-2">

            <label
              for="code"
              class="block text-sm font-semibold text-[#292522]"
            >
              Kode
            </label>

            <input
              id="code"
              name="code"
              type="text"
              value="{{ old('code', $paymentMethod->code) }}"
              required
              placeholder="Contoh: CASH"
              class="w-full rounded-2xl border border-[#D9D2CA] bg-white px-4 py-3 text-sm uppercase text-[#292522] outline-none transition placeholder:text-[#B8AEA5] hover:border-[#B8AEA5] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
            >

            <p class="text-xs text-[#A3978D]">
              Gunakan kode singkat dan mudah dikenali.
            </p>

            @error('code')
              <p class="text-sm text-[#A94A32]">
                {{ $message }}
              </p>
            @enderror

          </div>


          {{-- ACTIVE STATUS --}}
          <div class="rounded-2xl border border-[#E7E1D9] bg-[#F7F5F0] p-4">

            <label class="flex cursor-pointer items-start gap-3">

              <input
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}
                class="mt-0.5 h-4 w-4 rounded border-[#CFC6BD] text-[#C68B59] focus:ring-[#C68B59]"
              >

              <span>
                <span class="block text-sm font-semibold text-[#292522]">
                  Metode pembayaran aktif
                </span>

                <span class="mt-1 block text-xs text-[#8A8179]">
                  Metode ini dapat digunakan saat proses transaksi.
                </span>
              </span>

            </label>

          </div>


          {{-- ACTION --}}
          <div class="flex flex-col-reverse gap-3 border-t border-[#EEEAE4] pt-6 sm:flex-row sm:justify-end">

            <a
              href="{{ route('admin.payment-methods.index') }}"
              class="inline-flex items-center justify-center rounded-xl border border-[#E1D5C8] bg-white px-5 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#F7F5F0]"
            >
              Batal
            </a>

            <button
              type="submit"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#292522] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3A3430] focus:outline-none focus:ring-4 focus:ring-[#C68B59]/20"
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