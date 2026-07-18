@extends('layouts.admin')

@section('title', 'Riwayat Transaksi - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
          Transaksi
        </p>

        <h1 class="mt-2 text-3xl font-semibold text-slate-900">
          Riwayat Transaksi
        </h1>

        <p class="mt-2 text-slate-500">
          Lihat seluruh transaksi penjualan yang tercatat.
        </p>
      </div>
    </div>

    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">

          <thead class="border-b border-slate-200 bg-slate-50">
            <tr>
              <th class="px-6 py-4 font-semibold text-slate-700">
                Invoice
              </th>

              <th class="px-6 py-4 font-semibold text-slate-700">
                Kasir
              </th>

              <th class="px-6 py-4 font-semibold text-slate-700">
                Metode Pembayaran
              </th>

              <th class="px-6 py-4 font-semibold text-slate-700">
                Total
              </th>

              <th class="px-6 py-4 font-semibold text-slate-700">
                Status
              </th>

              <th class="px-6 py-4 font-semibold text-slate-700">
                Tanggal
              </th>

              <th class="px-6 py-4 text-right font-semibold text-slate-700">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">

            @forelse ($transactions as $transaction)

              <tr class="hover:bg-slate-50">

                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">
                    {{ $transaction->invoice_no }}
                  </p>
                </td>

                <td class="px-6 py-4 text-slate-600">
                  {{ $transaction->user?->name ?? '-' }}
                </td>

                <td class="px-6 py-4 text-slate-600">
                  {{ $transaction->paymentMethod?->name ?? '-' }}
                </td>

                <td class="px-6 py-4 font-semibold text-emerald-700">
                  Rp {{ number_format($transaction->total, 0, ',', '.') }}
                </td>

                <td class="px-6 py-4">
                  <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    {{ ucfirst($transaction->status) }}
                  </span>
                </td>

                <td class="px-6 py-4 text-slate-600">
                  {{ $transaction->created_at->format('d M Y H:i') }}
                </td>

                <td class="px-6 py-4 text-right">
                  <a
                    href="{{ route('admin.transactions.show', $transaction) }}"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                  >
                    Detail
                  </a>
                </td>

              </tr>

            @empty

              <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                  Belum ada transaksi.
                </td>
              </tr>

            @endforelse

          </tbody>

        </table>
      </div>

      @if ($transactions->hasPages())
        <div class="border-t border-slate-200 px-6 py-4">
          {{ $transactions->links() }}
        </div>
      @endif

    </div>

  </div>
@endsection