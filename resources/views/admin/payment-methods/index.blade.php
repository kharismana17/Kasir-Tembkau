@extends('layouts.admin')

@section('title', 'Metode Pembayaran - Kasir Tembakau')

@section('content')
  <div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
          Master Data
        </p>

        <h1 class="mt-2 text-3xl font-semibold text-slate-900">
          Metode Pembayaran
        </h1>

        <p class="mt-2 text-slate-500">
          Kelola metode pembayaran yang tersedia untuk transaksi POS.
        </p>
      </div>

      <a
        href="{{ route('admin.payment-methods.create') }}"
        class="inline-flex items-center justify-center rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/15 transition hover:bg-emerald-800"
      >
        + Tambah Metode Pembayaran
      </a>
    </div>

    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[700px]">
          <thead class="border-b border-slate-200 bg-slate-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">No</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Nama Metode</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Kode</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Status</th>
              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-600">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            @forelse ($paymentMethods as $method)
              <tr class="hover:bg-slate-50">
                <td class="px-6 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>

                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">{{ $method->name }}</p>
                </td>

                <td class="px-6 py-4 text-sm text-slate-500">{{ $method->code }}</td>

                <td class="px-6 py-4">
                  @if ($method->is_active)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-800">Aktif</span>
                  @else
                    <span class="rounded-full bg-rose-50 px-3 py-1 text-sm font-medium text-rose-700">Nonaktif</span>
                  @endif
                </td>

                <td class="px-6 py-4">
                  <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.payment-methods.edit', $method) }}" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Edit</a>

                    <form method="POST" action="{{ route('admin.payment-methods.toggle-status', $method) }}" onsubmit="return confirm('Yakin ingin mengubah status metode pembayaran ini?')">
                      @csrf
                      @method('PATCH')

                      <button type="submit" class="rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                        @if ($method->is_active) Nonaktifkan @else Aktifkan @endif
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada metode pembayaran.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
