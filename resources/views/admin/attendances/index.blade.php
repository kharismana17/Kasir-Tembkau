@extends('layouts.admin')

@section('title', 'Absensi Kasir')

@section('content')
<div class="space-y-6">
  <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Attendance Overview</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">Absensi Kasir</h1>
        <p class="mt-2 text-sm text-[#6B4F3A]">Pantau status absensi seluruh kasir.</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-3 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Jam Kerja</p>
        <p class="mt-2 text-lg font-bold text-[#8A5B1E]">{{ floor($summary['total_working_minutes'] / 60) }} jam {{ $summary['total_working_minutes'] % 60 }} menit</p>
      </div>
    </div>
  </section>

  {{-- Attendance Users management panel --}}
  <section class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Daftar Nama Kasir</p>
        <p class="mt-1 text-sm text-[#6B4F3A]">Kelola nama untuk sistem absensi (tidak terkait user login).</p>
      </div>
      <div>
        <button id="showAddAttendanceUser" class="w-full rounded-2xl bg-[#292522] px-4 py-2 text-sm font-semibold text-white sm:w-auto">Tambah Nama</button>
      </div>
    </div>

    <div id="addAttendanceUserForm" class="mt-4 hidden">
      <form method="POST" action="{{ route('admin.attendances.users.store') }}" class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        @csrf
        @if($errors->any())
          <div class="col-span-3 rounded-lg bg-[#FFF4F0] p-3 text-sm text-[#B45309]">
            <ul class="list-disc pl-5">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div>
          <input name="name" placeholder="Nama" value="{{ old('name') }}" required class="w-full rounded-2xl border px-4 py-2" />
        </div>
        <div>
          <input name="password" type="password" placeholder="Password" required class="w-full rounded-2xl border px-4 py-2" />
        </div>
        <div class="flex gap-2">
          <input type="hidden" name="is_active" value="0" />
          <input type="checkbox" name="is_active" id="is_active_new" value="1" checked />
          <label for="is_active_new" class="text-sm text-[#6B4F3A]">Aktif</label>
          <button type="submit" class="ml-auto w-full rounded-2xl bg-[#8A5B1E] px-4 py-2 text-sm font-semibold text-white sm:w-auto">Simpan</button>
        </div>
      </form>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="min-w-full divide-y divide-[#EEEAE2] text-sm">
        <thead class="bg-[#FAF9F6]">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Nama</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Status</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Total Absensi</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Terakhir Absen</th>
            <th class="px-4 py-3 text-left font-semibold text-[#6B4F3A]">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#EEEAE2] bg-white">
          @if($attendanceUsers->isEmpty())
            <tr><td colspan="5" class="px-4 py-8 text-center text-[#6B4F3A]">Belum ada nama kasir absensi.</td></tr>
          @else
            @foreach($attendanceUsers as $au)
              <tr>
                <td class="px-4 py-3">{{ $au->name }}</td>
                <td class="px-4 py-3">{{ $au->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                <td class="px-4 py-3">{{ $au->attendances()->count() }}</td>
                <td class="px-4 py-3">{{ optional($au->attendances()->latest('attendance_date')->first())->attendance_date?->translatedFormat('d M Y') ?? '-' }}</td>
                <td class="px-4 py-3">
                  <div class="flex flex-col gap-2 sm:flex-row">
                    <button data-id="{{ $au->id }}" class="editAttendanceUser w-full rounded-lg bg-[#F4EFE6] px-3 py-1 text-sm font-semibold text-[#8A5B1E] sm:w-auto">Edit</button>
                    <form method="POST" action="{{ route('admin.attendances.users.destroy', $au) }}" onsubmit="return confirm('Hapus nama kasir?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="w-full rounded-lg bg-[#FFEAEA] px-3 py-1 text-sm font-semibold text-[#B45309] sm:w-auto">Hapus</button>
                    </form>
                  </div>
                  <form class="mt-2 editForm hidden" id="edit-form-{{ $au->id }}" method="POST" action="{{ route('admin.attendances.users.update', $au) }}">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-2 grid-cols-1 md:grid-cols-3">
                      <input name="name" value="{{ $au->name }}" required class="rounded-2xl border px-3 py-2" />
                      <input name="password" type="password" placeholder="Kosongkan jika tidak diubah" class="rounded-2xl border px-3 py-2" />
                      <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" name="is_active" value="1" {{ $au->is_active ? 'checked' : '' }} />
                        <button type="submit" class="ml-auto w-full rounded-2xl bg-[#8A5B1E] px-3 py-1 text-sm font-semibold text-white sm:w-auto">Simpan</button>
                      </div>
                    </div>
                  </form>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </section>

  @if(session('success'))
  <div class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
  @endif

  <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div class="flex-1">
        <label class="mb-2 block text-sm font-semibold text-[#292522]">Cari Nama Kasir</label>
        <input id="attendanceSearch" type="search" value="{{ $search }}" placeholder="Cari nama kasir..." class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none" />
      </div>
      <div class="flex flex-wrap gap-2">
        <button data-filter="all" class="filter-button rounded-2xl bg-[#292522] px-4 py-2 text-xs font-bold text-white">Semua</button>
        <button data-filter="today" class="filter-button rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E]">Hari Ini</button>
        <button data-filter="week" class="filter-button rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E]">Minggu Ini</button>
        <button data-filter="month" class="filter-button rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E]">Bulan Ini</button>
        <button data-filter="custom" class="filter-button rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E]">Custom Range</button>
      </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Belum Check In</p><p class="mt-2 text-xl font-bold text-[#292522]">{{ $summary['not_checked_in'] }}</p></div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Sedang Bekerja</p><p class="mt-2 text-xl font-bold text-[#292522]">{{ $summary['working'] }}</p></div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Sudah Check Out</p><p class="mt-2 text-xl font-bold text-[#292522]">{{ $summary['completed'] }}</p></div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Absensi</p><p class="mt-2 text-xl font-bold text-[#292522]">{{ $summary['total'] }}</p></div>
    </div>

    <div class="mt-4 grid gap-4 grid-cols-1 md:grid-cols-2">
      <input id="attendanceFrom" type="date" value="{{ $from }}" class="rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm" />
      <input id="attendanceTo" type="date" value="{{ $to }}" class="rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm" />
    </div>
  </section>

  <section id="attendanceTableContainer" class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm sm:p-6">
    @include('admin.attendances.partials.table', ['attendances' => $attendances])
  </section>
</div>

<script>
  const filterButtons = Array.from(document.querySelectorAll('.filter-button'));
  const searchInput = document.getElementById('attendanceSearch');
  const fromInput = document.getElementById('attendanceFrom');
  const toInput = document.getElementById('attendanceTo');
  const tableContainer = document.getElementById('attendanceTableContainer');
  let activeFilter = '{{ $filter }}';

  function setActiveFilter(filter) {
    activeFilter = filter;
    filterButtons.forEach((button) => {
      const isActive = button.dataset.filter === filter;
      button.classList.toggle('bg-[#292522]', isActive);
      button.classList.toggle('text-white', isActive);
      button.classList.toggle('bg-[#F4EFE6]', !isActive);
      button.classList.toggle('text-[#8A5B1E]', !isActive);
    });
  }

  async function refreshAttendances() {
    const params = new URLSearchParams({ filter: activeFilter, search: searchInput?.value || '' });
    if (activeFilter === 'custom') {
      if (fromInput?.value) params.set('from', fromInput.value);
      if (toInput?.value) params.set('to', toInput.value);
    }
    const response = await fetch(`/admin/attendances/data?${params.toString()}`, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    const data = await response.json();
    if (tableContainer) tableContainer.innerHTML = data.attendances_html;
  }

  filterButtons.forEach((button) => button.addEventListener('click', () => {
    setActiveFilter(button.dataset.filter);
    refreshAttendances();
  }));
  searchInput?.addEventListener('input', () => refreshAttendances());
  fromInput?.addEventListener('change', () => refreshAttendances());
  toInput?.addEventListener('change', () => refreshAttendances());
  setActiveFilter(activeFilter);

  // Attendance users JS
  document.getElementById('showAddAttendanceUser')?.addEventListener('click', function(){
    const container = document.getElementById('addAttendanceUserForm');
    if (container) container.classList.toggle('hidden');
  });

  document.querySelectorAll('.editAttendanceUser').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const id = btn.dataset.id;
      const form = document.getElementById('edit-form-' + id);
      if (form) form.classList.toggle('hidden');
    });
  });
</script>
@endsection
