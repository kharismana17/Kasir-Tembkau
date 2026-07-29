@extends('layouts.cashier')

@section('title', 'Absensi Kasir')

@section('content')
<div class="space-y-6">
  <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Attendance</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">Absensi Karyawan</h1>
        <p class="mt-2 text-sm text-[#6B4F3A]">Pilih nama kasir, verifikasi password, lalu lakukan check in atau check out.</p>
      </div>
      <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
      <div class="max-w-xl rounded-[24px] border border-[#E7E1D9] bg-white p-6 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#B47727]">Login Absensi</p>
        <h2 class="mt-2 text-xl font-semibold text-[#292522]">Pilih Nama Kasir</h2>

        <form id="attendanceLoginForm" class="mt-5 space-y-4">
          <div>
            <label for="attendanceUserId" class="mb-2 block text-sm font-semibold text-[#6B4F3A]">Pilih Nama Kasir</label>
            <select id="attendanceUserId" name="user_id" class="w-full rounded-2xl border border-[#DDD0C0] bg-[#FCFAF8] px-4 py-3 text-sm text-[#292522] focus:border-[#8A5B1E] focus:outline-none">
              <option value="">▼ Pilih Nama</option>
              @foreach($attendanceUsers as $user)
                @php
                  $statusLabel = 'Belum Check In';
                  $dot = '⚪';
                  if (! empty($user->today_attendance)) {
                    if ($user->today_attendance->check_out) {
                      $dot = '🔵';
                      $statusLabel = 'Sudah Check Out';
                    } elseif ($user->today_attendance->check_in) {
                      $dot = '🟢';
                      $statusLabel = 'Sedang Bekerja';
                    }
                  }
                @endphp
                <option value="{{ $user->id }}">{{ $dot }} {{ $user->name }} ({{ $statusLabel }})</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="attendancePassword" class="mb-2 block text-sm font-semibold text-[#6B4F3A]">Password</label>
            <input id="attendancePassword" name="password" type="password" class="w-full rounded-2xl border border-[#DDD0C0] bg-[#FCFAF8] px-4 py-3 text-sm text-[#292522] focus:border-[#8A5B1E] focus:outline-none" placeholder="********" />
          </div>

          <button type="submit" class="w-full rounded-2xl bg-[#292522] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#3A352F]">
            Verifikasi & Absensi
          </button>
        </form>
      </div>
    </section>
  </section>

  <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
    <div class="grid gap-4 lg:grid-cols-3">
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-5 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Status</p>
        <p id="attendanceStatus" class="mt-3 text-lg font-semibold text-[#292522]">Belum memilih kasir</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-5 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Check In</p>
        <p id="attendanceCheckIn" class="mt-3 text-lg font-semibold text-[#292522]">-</p>
      </div>
      <div class="rounded-2xl border border-[#E7E1D9] bg-white p-5 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Check Out</p>
        <p id="attendanceCheckOut" class="mt-3 text-lg font-semibold text-[#292522]">-</p>
      </div>
    </div>

    <div class="mt-5 rounded-2xl border border-[#E7E1D9] bg-white p-5 shadow-sm">
      <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Durasi Kerja</p>
      <p id="attendanceWorkingMinutes" class="mt-3 text-lg font-semibold text-[#292522]">0 Menit</p>
    </div>
  </section>

  <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Riwayat Absensi</p>
        <p class="mt-1 text-sm text-[#6B4F3A]">10 catatan terakhir</p>
      </div>
    </div>

    <div id="attendanceHistoryList" class="mt-4 space-y-3">
      @include('pos.partials.attendance-history', ['history' => $history])
    </div>
  </section>
</div>

<script>
  const attendanceStatus = document.getElementById('attendanceStatus');
  const attendanceCheckIn = document.getElementById('attendanceCheckIn');
  const attendanceCheckOut = document.getElementById('attendanceCheckOut');
  const attendanceWorkingMinutes = document.getElementById('attendanceWorkingMinutes');
  const attendanceHistoryList = document.getElementById('attendanceHistoryList');
  const attendanceLoginForm = document.getElementById('attendanceLoginForm');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
  const actionUrls = {
    'verify': '{{ route('pos.attendance.verify') }}',
  };

  function setButtonsLoading(isLoading, button = null) {
    const buttons = [attendanceLoginForm?.querySelector('button[type="submit"]')].filter(Boolean);

    buttons.forEach((btn) => {
      btn.disabled = isLoading;
      btn.classList.toggle('opacity-60', isLoading);
      btn.classList.toggle('cursor-not-allowed', isLoading);
    });

    if (button) {
      button.textContent = isLoading ? 'Memproses...' : button.dataset.defaultText || button.textContent;
    }
  }

  function setDefaultButtonLabels() {
    const submitButton = attendanceLoginForm?.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.dataset.defaultText = 'Verifikasi & Absensi';
      submitButton.textContent = 'Verifikasi & Absensi';
    }
  }

  function updateAttendanceUI(payload) {
    const attendance = payload.attendance;

    if (attendanceStatus) attendanceStatus.textContent = attendance?.status_label || 'Belum Check In';
    if (attendanceCheckIn) attendanceCheckIn.textContent = attendance?.check_in ? attendance.check_in.slice(0, 5) : '-';
    if (attendanceCheckOut) attendanceCheckOut.textContent = attendance?.check_out ? attendance.check_out.slice(0, 5) : '-';
    if (attendanceWorkingMinutes) attendanceWorkingMinutes.textContent = attendance?.duration_text || '0 Menit';

    if (attendanceHistoryList && payload.history_html) {
      attendanceHistoryList.innerHTML = payload.history_html;
    }
  }

  async function submitAttendance(action, body = null) {
    setDefaultButtonLabels();
    const button = attendanceLoginForm?.querySelector('button[type="submit"]') || null;
    setButtonsLoading(true, button);

    try {
      const response = await fetch(actionUrls[action], {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: body ? new URLSearchParams(body) : undefined,
      });

      const contentType = response.headers.get('content-type') || '';
      const data = contentType.includes('application/json') ? await response.json() : await response.text();

      if (!response.ok) {
        alert(data.message || 'Terjadi kesalahan.');
        setButtonsLoading(false, button);
        return;
      }

      window.location.reload();
    } catch (error) {
      console.error(error);
      alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
      setButtonsLoading(false, button);
    }
  }

  attendanceLoginForm?.addEventListener('submit', (event) => {
    event.preventDefault();

    const formData = new FormData(attendanceLoginForm);
    submitAttendance('verify', Object.fromEntries(formData));
  });
</script>
@endsection
