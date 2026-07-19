@extends('layouts.admin')

@section('title', 'Audit Log Admin - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

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
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5h6M9 13h6m-6 4h4"
              />
            </svg>
          </div>

          <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#A56F45]">
              Management
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
              Audit Log Admin
            </h1>
          </div>

        </div>

        <p class="text-sm text-[#8A8179]">
          Lihat semua aktivitas audit yang dicatat oleh sistem.
        </p>
      </div>

      <div class="inline-flex w-fit items-center gap-2 rounded-2xl border border-[#E1D5C8] bg-white px-4 py-3 shadow-sm">

        <span class="h-2 w-2 rounded-full bg-[#C68B59]"></span>

        <span class="text-sm font-semibold text-[#6B4F3A]">
          System Monitoring
        </span>

      </div>

    </div>


    {{-- AUDIT LOG CARD --}}
    <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

      {{-- CARD HEADER --}}
      <div class="border-b border-[#E7E1D9] bg-[#F7F5F0] px-6 py-5">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

          <div>
            <h2 class="text-lg font-bold text-[#292522]">
              Riwayat Audit
            </h2>

            <p class="mt-1 text-sm text-[#8A8179]">
              Filter dan telusuri aktivitas pengguna dalam sistem.
            </p>
          </div>


          {{-- FILTER --}}
          <form
            method="GET"
            class="grid w-full gap-3 sm:grid-cols-2 lg:max-w-2xl lg:grid-cols-3"
          >

            <input
              type="text"
              name="action"
              value="{{ request('action') }}"
              placeholder="Cari action"
              class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
            >

            <input
              type="text"
              name="user_id"
              value="{{ request('user_id') }}"
              placeholder="User ID"
              class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition placeholder:text-[#A3978D] focus:border-[#C68B59] focus:ring-4 focus:ring-[#C68B59]/10"
            >

            <button
              type="submit"
              class="rounded-xl bg-[#292522] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#3A3531] focus:outline-none focus:ring-4 focus:ring-[#292522]/10"
            >
              Filter Audit
            </button>

          </form>

        </div>

      </div>


      {{-- TABLE --}}
      <div class="overflow-x-auto">

        <table class="min-w-[1100px] w-full text-left text-sm">

          <thead class="border-b border-[#E7E1D9] bg-white">

            <tr>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-[0.15em] text-[#A3978D]">
                Waktu
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-[0.15em] text-[#A3978D]">
                User
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-[0.15em] text-[#A3978D]">
                Action
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-[0.15em] text-[#A3978D]">
                Target
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-[0.15em] text-[#A3978D]">
                Deskripsi
              </th>

              <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-[0.15em] text-[#A3978D]">
                IP Address
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-[#EEEAE4]">

            @forelse ($auditLogs as $log)

              <tr class="transition hover:bg-[#F7F5F0]">

                {{-- TIME --}}
                <td class="px-6 py-5 text-sm text-[#6B625B]">
                  {{ $log->created_at->format('d M Y H:i:s') }}
                </td>


                {{-- USER --}}
                <td class="px-6 py-5">

                  <div class="flex items-center gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#F1E6DC] text-xs font-bold text-[#6B4F3A]">
                      {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                    </div>

                    <div>

                      <p class="font-semibold text-[#292522]">
                        {{ $log->user?->name ?? 'System' }}
                      </p>

                      <p class="mt-0.5 text-xs text-[#A3978D]">
                        ID: {{ $log->user_id ?? '-' }}
                      </p>

                    </div>

                  </div>

                </td>


                {{-- ACTION --}}
                <td class="px-6 py-5">

                  <span class="inline-flex rounded-lg bg-[#F1E6DC] px-3 py-1.5 text-xs font-bold text-[#6B4F3A]">
                    {{ $log->action }}
                  </span>

                </td>


                {{-- TARGET --}}
                <td class="px-6 py-5 text-[#6B625B]">

                  @if ($log->auditable_type)

                    <p class="font-semibold text-[#292522]">
                      {{ class_basename($log->auditable_type) }}
                    </p>

                    <p class="mt-1 text-xs text-[#A3978D]">
                      #{{ $log->auditable_id ?? '-' }}
                    </p>

                  @else

                    <span class="text-[#A3978D]">
                      -
                    </span>

                  @endif

                </td>


                {{-- DESCRIPTION --}}
                <td class="max-w-sm px-6 py-5 text-[#6B625B]">

                  <p class="line-clamp-2">
                    {{ $log->description ?? '-' }}
                  </p>

                </td>


                {{-- IP --}}
                <td class="px-6 py-5">

                  <span class="rounded-lg bg-[#F7F5F0] px-3 py-1.5 font-mono text-xs text-[#6B625B]">
                    {{ $log->ip_address ?? '-' }}
                  </span>

                </td>

              </tr>

            @empty

              <tr>

                <td
                  colspan="6"
                  class="px-6 py-16 text-center"
                >

                  <div class="flex flex-col items-center">

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F1E6DC] text-[#C68B59]">

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
                          d="M9 12h6m-6 4h4m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2z"
                        />
                      </svg>

                    </div>

                    <p class="mt-4 font-semibold text-[#292522]">
                      Belum ada aktivitas audit
                    </p>

                    <p class="mt-1 text-sm text-[#8A8179]">
                      Aktivitas sistem akan muncul di sini.
                    </p>

                  </div>

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>


      {{-- PAGINATION --}}
      @if ($auditLogs->hasPages())

        <div class="border-t border-[#E7E1D9] px-6 py-4">

          {{ $auditLogs->links() }}

        </div>

      @endif

    </section>

  </div>
@endsection