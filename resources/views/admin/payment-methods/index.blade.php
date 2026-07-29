@extends('layouts.admin')

@section('title', 'Metode Pembayaran - Kasir Tembakau')

@section('content')
  <div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#A06F45]">
          Master Data
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#292522]">
          Metode Pembayaran
        </h1>

        <p class="mt-2 text-sm text-[#8A8179]">
          Kelola metode pembayaran yang tersedia untuk transaksi POS.
        </p>
      </div>

      <a
        href="{{ route('admin.payment-methods.create') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C68B59] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#292522]/10 transition hover:bg-[#A96F43]"
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
            d="M12 4v16m8-8H4"
          />
        </svg>

        Tambah Metode Pembayaran
      </a>

    </div>


    {{-- SUCCESS ALERT --}}
    @if (session('success'))

      <div class="flex items-start gap-3 rounded-2xl border border-[#D8C2AD] bg-[#F4EDE7] p-4">

        <svg
          class="mt-0.5 h-5 w-5 shrink-0 text-[#8A5B3D]"
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

        <p class="text-sm font-medium text-[#6B4F3A]">
          {{ session('success') }}
        </p>

      </div>

    @endif


    {{-- PAYMENT METHODS TABLE --}}
    <div class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

      {{-- TABLE HEADER --}}
      <div class="flex flex-col gap-3 border-b border-[#E7E1D9] bg-[#FBFAF8] px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

        <div>
          <h2 class="text-base font-semibold text-[#292522]">
            Daftar Metode Pembayaran
          </h2>

          <p class="mt-1 text-sm text-[#8A8179]">
            Metode pembayaran yang tersedia di sistem kasir.
          </p>
        </div>

        <span class="inline-flex w-fit rounded-full bg-[#F4EDE7] px-3 py-1.5 text-xs font-semibold text-[#8A5B3D]">
          {{ $paymentMethods->count() }} Metode
        </span>

      </div>


      <div class="overflow-x-auto">

        <table class="w-full min-w-[760px] text-left">

          <thead class="border-b border-[#E7E1D9] bg-white">

            <tr>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                No
              </th>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Nama Metode
              </th>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Kode
              </th>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Status
              </th>

              <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Aksi
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-[#F0ECE7]">

            @forelse ($paymentMethods as $method)

              <tr class="transition hover:bg-[#FBFAF8]">

                {{-- NUMBER --}}
                <td class="px-6 py-5 text-sm text-[#8A8179]">
                  {{ $loop->iteration }}
                </td>


                {{-- NAME --}}
                <td class="px-6 py-5">

                  <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE7] text-[#8A5B3D]">

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
                          d="M3 10h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"
                        />
                      </svg>

                    </div>

                    <p class="font-semibold text-[#292522]">
                      {{ $method->name }}
                    </p>

                  </div>

                </td>


                {{-- CODE --}}
                <td class="px-6 py-5">

                  <span class="rounded-lg bg-[#F7F5F0] px-3 py-1.5 font-mono text-xs font-semibold text-[#6B625B]">
                    {{ $method->code }}
                  </span>

                </td>


                {{-- STATUS --}}
                <td class="px-6 py-5">

                  @if ($method->is_active)

                    <span class="inline-flex items-center gap-2 rounded-full bg-[#F4EDE7] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">

                      <span class="h-1.5 w-1.5 rounded-full bg-[#C68B59]"></span>

                      Aktif

                    </span>

                  @else

                    <span class="inline-flex items-center gap-2 rounded-full bg-[#F3F1EF] px-3 py-1.5 text-xs font-semibold text-[#8A8179]">

                      <span class="h-1.5 w-1.5 rounded-full bg-[#A3978D]"></span>

                      Nonaktif

                    </span>

                  @endif

                </td>


                {{-- ACTION --}}
                <td class="px-6 py-5">

                  <div class="flex justify-end gap-2">

                    <a
                      href="{{ route('admin.payment-methods.edit', $method) }}"
                      class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-xs font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#F4EDE7]"
                    >
                      Edit
                    </a>


                    <form
                      method="POST"
                      action="{{ route('admin.payment-methods.toggle-status', $method) }}"
                      onsubmit="return confirm('Yakin ingin mengubah status metode pembayaran ini?')"
                    >

                      @csrf

                      @method('PATCH')

                      <button
                        type="submit"
                        class="rounded-xl px-4 py-2 text-xs font-semibold transition
                        {{ $method->is_active
                            ? 'bg-[#F4EDE7] text-[#8A5B3D] hover:bg-[#EADDD2]'
                            : 'bg-[#292522] text-white hover:bg-[#403A36]' }}"
                      >
                        {{ $method->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                      </button>

                    </form>

                  </div>

                </td>

              </tr>

            @empty

              <tr>

                <td
                  colspan="5"
                  class="px-6 py-16 text-center"
                >

                  <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F7F5F0] text-[#A3978D]">

                    <svg
                      class="h-7 w-7"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M3 10h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"
                      />
                    </svg>

                  </div>

                  <p class="mt-4 text-sm font-semibold text-[#292522]">
                    Belum ada metode pembayaran
                  </p>

                  <p class="mt-1 text-sm text-[#8A8179]">
                    Tambahkan metode pembayaran untuk digunakan pada transaksi POS.
                  </p>

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>

    </div>

  </div>
@endsection