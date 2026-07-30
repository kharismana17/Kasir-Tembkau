<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji {{ $salary->attendanceUser?->name }}</title>
    <style>
        body { font-family: sans-serif; color: #1f2937; }
        .border { border: 1px solid #d8d8d8; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .py-2 { padding-top: .5rem; padding-bottom: .5rem; }
        .px-3 { padding-left: .75rem; padding-right: .75rem; }
        .h4 { font-size: 1.25rem; margin-bottom: .5rem; }
        .small { font-size: .85rem; color: #555; }
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { border: 1px solid #d8d8d8; padding: .75rem; }
        .table th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1 class="h4">Slip Gaji Kasir</h1>
    <p class="small">Nama: {{ $salary->attendanceUser?->name }}</p>
    <p class="small">Periode: {{ $salary->period_start->translatedFormat('d M Y') }} - {{ $salary->period_end->translatedFormat('d M Y') }}</p>
    <p class="small">Status: {{ $salary->status_label }}</p>
    <p class="small">Tarif/Jam: Rp {{ number_format($salary->hourly_rate, 0, ',', '.') }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Durasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
                <tr>
                    <td>{{ $session->attendance_date?->translatedFormat('d M Y') }}</td>
                    <td>{{ \Illuminate\Support\Carbon::createFromFormat('H:i:s', $session->check_in)->format('H:i') }}</td>
                    <td>{{ \Illuminate\Support\Carbon::createFromFormat('H:i:s', $session->check_out)->format('H:i') }}</td>
                    <td>{{ $session->formatted_duration }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada sesi kerja.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        <p class="small"><strong>Total Sesi:</strong> {{ $salary->total_sessions }}</p>
        <p class="small"><strong>Total Jam:</strong> {{ number_format($salary->total_hours, 2, ',', '.') }}</p>
        <p class="small"><strong>Total Gaji:</strong> Rp {{ number_format($salary->total_salary, 0, ',', '.') }}</p>
    </div>
</body>
</html>
