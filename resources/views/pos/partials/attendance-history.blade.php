@forelse($history as $record)
  <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-4 shadow-sm">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm font-bold text-[#292522]">{{ \Illuminate\Support\Carbon::parse($record->attendance_date)->setTimezone(config('app.timezone'))->translatedFormat('d M Y') }}</p>
        <p class="mt-1 text-sm text-[#6B4F3A]">Check in: {{ $record->check_in ? \Illuminate\Support\Carbon::createFromFormat('H:i:s', $record->check_in, config('app.timezone'))->format('H:i') : '-' }} • Check out: {{ $record->check_out ? \Illuminate\Support\Carbon::createFromFormat('H:i:s', $record->check_out, config('app.timezone'))->format('H:i') : '-' }}</p>
      </div>
      <div class="text-sm font-semibold text-[#8A5B1E]">
        {{ $record->formatted_duration }} • {{ $record->display_status }}
      </div>
    </div>
  </div>
@empty
  <div class="empty-history rounded-2xl border border-dashed border-[#D8D3C9] bg-white px-4 py-6 text-center text-sm text-[#6B4F3A]">Belum ada riwayat absensi.</div>
@endforelse
