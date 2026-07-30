@extends('layouts.admin')

@section('title', 'Detail Penggajian - Admin')

@section('content')
<div class="space-y-6">
  <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Slip Gaji</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">Detail Penggajian {{ $salary->attendanceUser?->name }}</h1>
        <p class="mt-2 text-sm text-[#6B4F3A]">Periode {{ $salary->period_start->translatedFormat('d M Y') }} sampai {{ $salary->period_end->translatedFormat('d M Y') }}.</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-3 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Status</p>
        <p class="mt-2 text-lg font-bold text-[#8A5B1E]">{{ $salary->status_label }}</p>
        @if($salary->paid_at)
          <p class="mt-1 text-xs text-[#6B4F3A]">Dibayar pada {{ $salary->paid_at->translatedFormat('d M Y H:i') }}</p>
        @endif
      </div>
    </div>
  </section>

  <section class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm sm:p-6">
    <div class="grid gap-4 md:grid-cols-4">
      <div class="rounded-2xl border border-[#E7E1D9] bg-[#FBF9F6] p-4">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Jumlah Sesi</p>
        <p class="mt-2 text-2xl font-bold text-[#292522]">{{ $salary->total_sessions }}</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-[#FBF9F6] p-4">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Jam</p>
        <p class="mt-2 text-2xl font-bold text-[#292522]">{{ number_format($salary->total_hours, 2, ',', '.') }}</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-[#FBF9F6] p-4">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Tarif/Jam</p>
        <p class="mt-2 text-2xl font-bold text-[#292522]">Rp {{ number_format($salary->hourly_rate, 0, ',', '.') }}</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-[#FBF9F6] p-4">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Gaji</p>
        <p class="mt-2 text-2xl font-bold text-[#292522]">Rp {{ number_format($salary->total_salary, 0, ',', '.') }}</p>
      </div>
    </div>
  </section>

  <section class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm sm:p-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Rincian Sesi Kerja</p>
      </div>
      <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.salaries.pay', $salary) }}">
          @csrf
          <button type="submit" class="rounded-2xl bg-[#292522] px-4 py-2 text-sm font-semibold text-white hover:bg-[#3A352F]">Tandai Dibayar</button>
        </form>
        <a href="{{ route('admin.salaries.export-pdf', $salary) }}" class="rounded-2xl bg-[#E7E1D9] px-4 py-2 text-sm font-semibold text-[#292522] hover:bg-[#D9CEC4]">Export PDF</a>
      </div>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="min-w-full divide-y divide-[#EEEAE2] text-sm">
        <thead class="bg-[#FBF9F6]">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Tanggal</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Check In</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Check Out</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Durasi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#EEEAE2] bg-white">
          @forelse($sessions as $session)
            <tr>
              <td class="px-4 py-3 text-[#6B4F3A]">{{ $session->attendance_date?->translatedFormat('d M Y') }}</td>
              <td class="px-4 py-3 text-[#6B4F3A]">
                {{ \Carbon\Carbon::parse($session->check_in)->format('H:i') }}
              </td>

              <td class="px-4 py-3 text-[#6B4F3A]">
                {{ \Carbon\Carbon::parse($session->check_out)->format('H:i') }}
              </td>
              <td class="px-4 py-3 text-[#6B4F3A]">{{ $session->formatted_duration }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-8 text-center text-[#6B4F3A]">Belum ada sesi kerja untuk periode ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
</div>
@endsection
