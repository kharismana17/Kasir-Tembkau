@extends('layouts.admin')

@section('title', 'Atur PIN Pengguna')

@section('content')

<div class="mx-auto max-w-2xl">

    {{-- HEADER --}}
    <div class="mb-8">

        <div class="flex items-center gap-3">

            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#17352b] text-[#d99a3d] shadow-sm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M7 10V8a5 5 0 0110 0v2m-9 0h8a2 2 0 012 2v7a2 2 0 01-2 2H8a2 2 0 01-2-2v-7a2 2 0 012-2z"
                    />
                </svg>
            </div>

            <div>

                <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#b47727]">
                    Manajemen Pengguna
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#17201c]">
                    Atur PIN Pengguna
                </h1>

            </div>

        </div>

        <p class="mt-3 text-sm text-slate-500">
            Buat atau perbarui PIN akses untuk pengguna yang dipilih.
        </p>

    </div>


    {{-- USER INFO --}}
    <div class="mb-5 rounded-2xl border border-[#ded9d0] bg-[#f4efe6] p-5">

        <div class="flex items-center gap-4">

            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#17352b] text-lg font-bold text-[#d99a3d]">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div>

                <p class="text-sm font-bold text-[#17201c]">
                    {{ $user->name }}
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $user->email }}
                </p>

            </div>

        </div>

    </div>


    {{-- FORM CARD --}}
    <div class="rounded-3xl border border-[#ded9d0] bg-white p-6 shadow-sm sm:p-8">

        <div class="mb-6">

            <h2 class="text-base font-bold text-[#17201c]">
                PIN Akses
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Masukkan PIN baru untuk pengguna ini.
            </p>

        </div>


        {{-- ERROR --}}
        @if ($errors->any())

            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4">

                <div class="flex gap-3">

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

                    <div class="space-y-1 text-sm text-rose-700">

                        @foreach ($errors->all() as $error)

                            <p>
                                {{ $error }}
                            </p>

                        @endforeach

                    </div>

                </div>

            </div>

        @endif


        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('admin.users.pin.update', $user->id) }}"
            class="space-y-6"
        >

            @csrf


            {{-- PIN --}}
            <div>

                <label
                    for="pin"
                    class="mb-2 block text-sm font-semibold text-[#17201c]"
                >
                    PIN baru
                </label>

                <div class="relative">

                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

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
                                d="M7 10V8a5 5 0 0110 0v2m-9 0h8a2 2 0 012 2v7a2 2 0 01-2 2H8a2 2 0 01-2-2v-7a2 2 0 012-2z"
                            />
                        </svg>

                    </div>

                    <input
                        id="pin"
                        type="password"
                        name="pin"
                        required
                        minlength="4"
                        maxlength="8"
                        inputmode="numeric"
                        placeholder="Masukkan PIN baru"
                        class="w-full rounded-2xl border border-[#d8d3c9] bg-[#faf9f6] px-4 py-3.5 pl-12 text-sm text-[#17201c] shadow-sm outline-none transition placeholder:text-slate-400 hover:border-[#b8b1a4] focus:border-[#17352b] focus:bg-white focus:ring-4 focus:ring-[#17352b]/10"
                    >

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    PIN harus terdiri dari 4 sampai 8 karakter.
                </p>

            </div>


            {{-- ACTION --}}
            <div class="flex flex-col-reverse gap-3 border-t border-[#eeeae2] pt-6 sm:flex-row sm:justify-end">

                <a
                    href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-[#d8d3c9] px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-[#f4efe6]"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#17352b] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#17352b]/15 transition hover:bg-[#214638] hover:shadow-xl active:scale-[0.99]"
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

                    Simpan PIN

                </button>

            </div>

        </form>

    </div>

</div>

@endsection