@extends('layouts.admin')

@section('title', 'Pengaturan - Kasir Tembakau')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#292522] text-[#C68B59] shadow-sm">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4" />
                    </svg>

                </div>

                <div>

                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#6B4F3A]">Pengaturan</p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#292522]">Pengaturan</h1>

                </div>

            </div>

            <p class="mt-3 text-sm text-[#8A8179]">Kelola pengaturan toko dan akun admin.</p>

        </div>

    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 text-sm text-[#2f6f44]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">

            <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3.14h15.64a2 2 0 001.71-3.14l-7.82-13a2 2 0 00-3.42 0z" />
            </svg>

            <div>

                <p class="text-sm font-semibold text-rose-700">Terdapat kesalahan pada form</p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-700">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        </div>
    @endif

    {{-- Informasi Toko --}}
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="section" value="store">

        <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FBF8F4] text-[#6B4F3A]">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h18M5 7v10h14V7M8 11h4" />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#292522]">Informasi Toko</h2>

                        <p class="mt-1 text-sm text-[#8A8179]">Nama, alamat, dan logo toko.</p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <div class="grid gap-6 md:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Nama Toko</label>
                        <input name="store_name" type="text" value="{{ old('store_name', $settings->store_name) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Nomor Telepon</label>
                        <input name="phone" type="text" value="{{ old('phone', $settings->phone) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Alamat</label>
                        <textarea name="address" rows="3" class="w-full resize-none rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none">{{ old('address', $settings->address) }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Logo Toko</label>
                        <input type="file" name="logo" accept="image/*" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />

                        @if ($settings->logo_path)
                            <p class="mt-2 text-xs text-[#A3978D]">Logo saat ini:</p>
                            <img src="{{ asset('storage/'.$settings->logo_path) }}" alt="Logo" class="mt-2 h-20 w-auto rounded-xl border border-[#E7E1D9]" />
                        @endif

                    </div>

                </div>

                <div class="mt-6 flex items-center justify-end gap-3">

                    <button type="submit" class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-sm font-semibold text-[#6B4F3A]">Simpan</button>

                </div>

            </div>

        </section>
    </form>

    {{-- Pengaturan Transaksi --}}
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <input type="hidden" name="section" value="transaction">

        <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FBF8F4] text-[#6B4F3A]">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2v6h6v-6c0-1.105-1.343-2-3-2zM6 8h.01M18 8h.01" />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#292522]">Pengaturan Transaksi</h2>

                        <p class="mt-1 text-sm text-[#8A8179]">Pajak, pembulatan, dan format nomor transaksi.</p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <div class="grid gap-6 md:grid-cols-3">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Pajak (%)</label>
                        <input name="tax_percentage" type="number" step="0.01" min="0" max="100" value="{{ old('tax_percentage', $settings->tax_percentage ?? 0) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Pembulatan (Rp)</label>
                        <input name="rounding" type="number" min="0" value="{{ old('rounding', $settings->rounding ?? 0) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
                        <p class="mt-2 text-xs text-[#A3978D]">Masukkan 0 jika tidak ingin pembulatan.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Format Nomor Transaksi</label>
                        <input name="transaction_number_format" type="text" value="{{ old('transaction_number_format', $settings->transaction_number_format ?? 'TRX-{Y}{m}{d}-{seq}') }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
                        <p class="mt-2 text-xs text-[#A3978D]">Gunakan placeholder seperti {Y}{m}{d} dan {seq} untuk sequence.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Tarif per Jam</label>
                        <input name="hourly_salary" type="number" step="0.01" min="0" value="{{ old('hourly_salary', $settings->hourly_salary ?? 0) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
                        <p class="mt-2 text-xs text-[#A3978D]">Masukkan tarif per jam untuk perhitungan gaji kasir.</p>
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="submit" class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-sm font-semibold text-[#6B4F3A]">Simpan</button>
                </div>

            </div>

        </section>
    </form>

    {{-- Pengaturan Stok --}}
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <input type="hidden" name="section" value="stock">

        <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FBF8F4] text-[#6B4F3A]">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h18v4H3zM3 11h18v10H3z" />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#292522]">Pengaturan Stok</h2>

                        <p class="mt-1 text-sm text-[#8A8179]">Batas stok minimum dan notifikasi stok menipis.</p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <div class="grid gap-6 md:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Batas Stok Minimum (default)</label>
                        <input name="default_stock_min" type="number" min="0" value="{{ old('default_stock_min', $settings->default_stock_min ?? 5) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Notifikasi Stok Menipis</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="notify_low_stock" value="1" @if(old('notify_low_stock', $settings->notify_low_stock ?? true)) checked @endif />
                                <span class="text-sm text-[#6B4F3A]">Aktifkan notifikasi</span>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="submit" class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-sm font-semibold text-[#6B4F3A]">Simpan</button>
                </div>

            </div>

        </section>
    </form>

    {{-- Pengaturan Barcode --}}
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <input type="hidden" name="section" value="barcode">

        <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FBF8F4] text-[#6B4F3A]">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h18v18H3z" />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#292522]">Pengaturan Barcode</h2>

                        <p class="mt-1 text-sm text-[#8A8179]">Barcode selalu menggunakan EAN-13 dan dibuat otomatis oleh sistem.</p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <p class="text-sm text-[#8A8179]">Sistem akan selalu menggunakan EAN-13 untuk barcode produk. Barcode dihasilkan otomatis saat pembuatan produk dan tidak dapat diubah melalui halaman ini.</p>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="submit" class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-sm font-semibold text-[#6B4F3A]">Simpan Catatan</button>
                </div>

            </div>

        </section>
    </form>

    {{-- Akun Admin --}}
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <input type="hidden" name="section" value="account">

        <section class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

            <div class="border-b border-[#EEE8E1] bg-[#FBF9F6] px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FBF8F4] text-[#6B4F3A]">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zM2 22c0-3.866 3.582-7 8-7h4c4.418 0 8 3.134 8 7v1H2v-1z" />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#292522]">Akun Admin</h2>

                        <p class="mt-1 text-sm text-[#8A8179]">Informasi akun dan ganti password.</p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <div class="grid gap-6 md:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Nama</label>
                        <input type="text" value="{{ $user->name }}" readonly class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm text-[#6B4F3A] outline-none" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">Email</label>
                        <input type="text" value="{{ $user->email }}" readonly class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm text-[#6B4F3A] outline-none" />
                    </div>

                    {{-- Password Lama --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">
                            Password Lama
                        </label>

                        <div class="relative">
                            <input
                                type="password"
                                name="current_password"
                                id="current_password"
                                required
                                class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 pr-12 text-sm text-[#292522] outline-none"
                            />

                            <button
                                type="button"
                                onclick="togglePassword('current_password', this)"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#8A8179]"
                            >
                                <svg class="h-5 w-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                    <circle cx="12" cy="12" r="2.5" stroke-width="1.8" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">
                            Password Baru
                        </label>

                        <div class="relative">
                            <input
                                type="password"
                                name="new_password"
                                id="new_password"
                                required
                                class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 pr-12 text-sm text-[#292522] outline-none"
                            />

                            <button
                                type="button"
                                onclick="togglePassword('new_password', this)"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#8A8179]"
                            >
                                <svg class="h-5 w-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                    <circle cx="12" cy="12" r="2.5" stroke-width="1.8" />
                                </svg>
                            </button>
                        </div>

                        <p class="mt-2 text-xs text-[#A3978D]">
                            Minimal 8 karakter.
                        </p>
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#292522]">
                            Konfirmasi Password Baru
                        </label>

                        <div class="relative">
                            <input
                                type="password"
                                name="new_password_confirmation"
                                id="new_password_confirmation"
                                required
                                class="w-full rounded-2xl border border-[#D9CEC4] bg-white px-4 py-3 pr-12 text-sm text-[#292522] outline-none"
                            />

                            <button
                                type="button"
                                onclick="togglePassword('new_password_confirmation', this)"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#8A8179]"
                            >
                                <svg class="h-5 w-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                    <circle cx="12" cy="12" r="2.5" stroke-width="1.8" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="submit" class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-sm font-semibold text-[#6B4F3A]">Ganti Password</button>
                </div>

            </div>

        </section>
    </form>

</div>

@endsection

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('.eye-icon');

        if (input.type === 'password') {
            input.type = 'text';

            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M3 3l18 18M10.58 10.58a2 2 0 002.83 2.83M9.88 5.09A10.94 10.94 0 0112 4.75c6 0 9.75 6.75 9.75 6.75a17.2 17.2 0 01-3.03 3.94M6.61 6.61C3.9 8.38 2.25 12 2.25 12s3.75 6.75 9.75 6.75a10.9 10.9 0 004.39-.91" />
            `;
        } else {
            input.type = 'password';

            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                <circle cx="12" cy="12" r="2.5" stroke-width="1.8" />
            `;
        }
    }
</script>