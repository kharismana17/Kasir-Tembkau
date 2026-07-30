@extends('layouts.admin')

@section('title', 'Penggajian - Admin')

@section('content')
<div class="space-y-6">
  <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Payroll</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">Penggajian Kasir</h1>
        <p class="mt-2 text-sm text-[#6B4F3A]">Hitung gaji kasir berdasarkan total jam kerja antara tanggal yang dipilih.</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-3 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Tarif Per Jam</p>
        <p class="mt-2 text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($storeSettings->hourly_salary ?? 0, 0, ',', '.') }}</p>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="rounded-2xl border border-[#D1E7DD] bg-[#EFF7EE] p-4 text-sm text-[#216E39]">{{ session('success') }}</div>
  @endif

  <section class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm sm:p-6">
    <form method="POST" action="{{ route('admin.salaries.hourly-rate.update') }}" class="grid gap-4 md:grid-cols-3">
      @csrf
      <input type="hidden" name="period_start" value="{{ $periodStart }}" />
      <input type="hidden" name="period_end" value="{{ $periodEnd }}" />
      <div>
        <label class="mb-2 block text-sm font-semibold text-[#292522]">Tarif Per Jam</label>
        <input type="number" step="0.01" min="0" name="hourly_salary" value="{{ old('hourly_salary', $storeSettings->hourly_salary ?? 6000) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm text-[#292522] outline-none" required />
      </div>
      <div class="md:col-span-2 flex items-end">
        <button type="submit" class="w-full rounded-2xl bg-[#292522] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#3A352F]">Simpan Tarif</button>
      </div>
    </form>
  </section>

  <section class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm sm:p-6">
    <form method="POST" action="{{ route('admin.salaries.calculate') }}" class="grid gap-4 md:grid-cols-3">
      @csrf
      <div>
        <label class="mb-2 block text-sm font-semibold text-[#292522]">Periode Mulai</label>
        <input type="date" name="period_start" value="{{ old('period_start', $periodStart) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm text-[#292522] outline-none" required />
      </div>
      <div>
        <label class="mb-2 block text-sm font-semibold text-[#292522]">Periode Selesai</label>
        <input type="date" name="period_end" value="{{ old('period_end', $periodEnd) }}" class="w-full rounded-2xl border border-[#D9CEC4] bg-[#FBF9F6] px-4 py-3 text-sm text-[#292522] outline-none" required />
      </div>
      <div class="flex items-end">
        <button type="submit" class="w-full rounded-2xl bg-[#292522] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#3A352F]">Hitung Gaji</button>
      </div>
    </form>
  </section>

  <section class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm sm:p-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Hasil Perhitungan</p>
        <p class="mt-1 text-sm text-[#6B4F3A]">Daftar gaji kasir yang sudah dihitung dalam periode ini.</p>
      </div>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="min-w-full divide-y divide-[#EEEAE2] text-sm">
        <thead class="bg-[#FAF9F6]">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Nama</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Jumlah Sesi</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Total Jam</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Tarif/Jam</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Total Gaji</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Status</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#EEEAE2] bg-white">
          @forelse($attendanceUsers as $user)
            @php
              $salary = $salaryRecords->get($user->id);
            @endphp
            <tr>
              <td class="px-4 py-3 font-semibold text-[#292522]">{{ $user->name }}</td>
              <td class="px-4 py-3 text-[#6B4F3A]">{{ $salary?->total_sessions ?? 0 }}</td>
              <td class="px-4 py-3 text-[#6B4F3A]">{{ number_format($salary?->total_hours ?? 0, 2, ',', '.') }}</td>
              <td class="px-4 py-3 text-[#6B4F3A]">Rp {{ number_format($salary?->hourly_rate ?? $storeSettings->hourly_salary ?? 0, 0, ',', '.') }}</td>
              <td class="px-4 py-3 text-[#6B4F3A]">Rp {{ number_format($salary?->total_salary ?? 0, 0, ',', '.') }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-bold {{ ($salary?->status === 'paid') ? 'bg-[#E9F7EE] text-[#2E7D32]' : 'bg-[#F4EFE6] text-[#8A5B1E]' }}">
                  {{ $salary?->status_label ?? 'Draft' }}
                </span>
              </td>
              <td class="px-4 py-3 space-y-2">
                @if($salary)
                  <a href="{{ route('admin.salaries.show', $salary) }}" class="inline-flex rounded-2xl bg-[#FBF9F6] px-3 py-2 text-xs font-semibold text-[#6B4F3A] hover:bg-[#F4F1EA]">Detail</a>
                  <form method="POST" action="{{ route('admin.salaries.pay', $salary) }}">
                    @csrf
                    <button type="submit" class="inline-flex rounded-2xl bg-[#292522] px-3 py-2 text-xs font-semibold text-white hover:bg-[#3A352F]">Tandai Dibayar</button>
                  </form>
                  <a href="{{ route('admin.salaries.export-pdf', $salary) }}" class="inline-flex rounded-2xl bg-[#E7E1D9] px-3 py-2 text-xs font-semibold text-[#292522] hover:bg-[#D9CEC4]">Export PDF</a>
                @else
                  <span class="text-xs text-[#6B4F3A]">Belum dihitung</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-[#6B4F3A]">Belum ada data gaji. Silakan pilih periode lalu hitung gaji.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
</div>
@endsection
