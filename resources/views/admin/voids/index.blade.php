@extends('layouts.admin')

@section('title', 'Permintaan Void Transaksi')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#17352b] text-[#d99a3d] shadow-sm">

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

                <div>

                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#b47727]">
                        Approval Center
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#17201c]">
                        Permintaan Void Transaksi
                    </h1>

                </div>

            </div>

            <p class="mt-3 text-sm text-slate-500">
                Tinjau dan kelola permintaan pembatalan transaksi dari kasir.
            </p>

        </div>


        {{-- COUNTER --}}
        <div class="inline-flex w-fit items-center gap-2 rounded-2xl border border-[#e8cfa7] bg-[#fff8eb] px-4 py-3">

            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-[#d99a3d] text-xs font-bold text-white">
                {{ $requests->count() }}
            </span>

            <span class="text-sm font-semibold text-[#8a5b1e]">
                Permintaan menunggu
            </span>

        </div>

    </div>


    {{-- CONTENT --}}
    <div class="space-y-4">

        @forelse($requests as $r)

            <div class="rounded-3xl border border-[#ded9d0] bg-white p-5 shadow-sm transition hover:border-[#c9c1b4] hover:shadow-md sm:p-6">

                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                    {{-- INFO --}}
                    <div class="min-w-0 flex-1">

                        <div class="flex flex-wrap items-center gap-3">

                            <span class="inline-flex items-center gap-2 rounded-xl bg-[#f4efe6] px-3 py-1.5 text-xs font-bold text-[#17352b]">

                                <svg
                                    class="h-4 w-4 text-[#b47727]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M9 14l2 2 4-4m5-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>

                                VOID REQUEST

                            </span>

                            <span class="text-xs text-slate-400">
                                {{ $r->created_at->format('d M Y H:i') }}
                            </span>

                        </div>


                        {{-- INVOICE --}}
                        <h2 class="mt-4 text-lg font-bold tracking-tight text-[#17201c]">
                            {{ $r->transaction->invoice_no }}
                        </h2>


                        {{-- REQUESTER --}}
                        <div class="mt-3 flex items-center gap-2 text-sm text-slate-500">

                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#17352b] text-xs font-bold text-[#d99a3d]">
                                {{ strtoupper(substr($r->requester?->name ?? 'K', 0, 1)) }}
                            </div>

                            <span>
                                Diminta oleh
                                <span class="font-semibold text-slate-700">
                                    {{ $r->requester?->name ?? 'Tidak diketahui' }}
                                </span>
                            </span>

                        </div>


                        {{-- REASON --}}
                        <div class="mt-4 rounded-2xl border border-[#eeeae2] bg-[#faf9f6] p-4">

                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                Alasan Void
                            </p>

                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                {{ $r->reason ?? 'Tidak ada alasan yang diberikan.' }}
                            </p>

                        </div>

                    </div>


                    {{-- ACTION --}}
                    <div class="flex flex-col gap-3 border-t border-[#eeeae2] pt-5 lg:w-44 lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 lg:text-center">
                            Tindakan
                        </p>

                        <form
                            method="POST"
                            action="{{ route('admin.voids.approve', $r->id) }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#17352b] px-4 py-3 text-sm font-bold text-white shadow-lg shadow-[#17352b]/10 transition hover:bg-[#214638] hover:shadow-xl active:scale-[0.99]"
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

                                Setujui

                            </button>

                        </form>


                        <form
                            method="POST"
                            action="{{ route('admin.voids.reject', $r->id) }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700 transition hover:bg-rose-100 active:scale-[0.99]"
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
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>

                                Tolak

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @empty

            {{-- EMPTY STATE --}}
            <div class="rounded-3xl border border-dashed border-[#d8d3c9] bg-[#faf9f6] px-6 py-16 text-center">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-[#f4efe6] text-[#b47727]">

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
                            d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                </div>

                <h2 class="mt-5 text-base font-bold text-[#17201c]">
                    Tidak ada permintaan void
                </h2>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                    Semua permintaan void sudah ditangani. Tidak ada tindakan yang perlu dilakukan saat ini.
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection