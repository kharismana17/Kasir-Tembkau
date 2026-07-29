<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-[#EEEAE2] text-sm">
    <thead class="bg-[#FAF9F6]">
      <tr>
        <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Kasir</th>
        <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Tanggal</th>
        <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Check In</th>
        <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Check Out</th>
        <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Durasi</th>
        <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Status</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-[#EEEAE2] bg-white">
      @forelse($attendances as $attendance)
        <tr>
          <td class="px-4 py-3 font-semibold text-[#292522]">{{ $attendance->attendanceUser?->name ?? '-' }}</td>
          <td class="px-4 py-3 text-[#6B4F3A]">{{ \Illuminate\Support\Carbon::parse($attendance->attendance_date)->translatedFormat('d M Y') }}</td>
          <td class="px-4 py-3 text-[#6B4F3A]">{{ $attendance->check_in ? \Illuminate\Support\Carbon::createFromFormat('H:i:s', $attendance->check_in)->format('H:i') : '-' }}</td>
          <td class="px-4 py-3 text-[#6B4F3A]">{{ $attendance->check_out ? \Illuminate\Support\Carbon::createFromFormat('H:i:s', $attendance->check_out)->format('H:i') : '-' }}</td>
          <td class="px-4 py-3 text-[#6B4F3A]">{{ $attendance->formatted_duration }}</td>
          <td class="px-4 py-3">
            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-bold {{ $attendance->status === 'working' ? 'bg-[#F4EFE6] text-[#8A5B1E]' : 'bg-[#E9F7EE] text-[#2E7D32]' }}">
              {{ $attendance->display_status }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="px-4 py-8 text-center text-[#6B4F3A]">Belum ada data absensi.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
