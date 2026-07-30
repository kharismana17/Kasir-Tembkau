<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceUser;
use App\Models\Salary;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $periodStart = $request->input('period_start', Carbon::now()->startOfMonth()->toDateString());
        $periodEnd = $request->input('period_end', Carbon::now()->endOfMonth()->toDateString());

        $salaryRecords = Salary::with('attendanceUser')
            ->when($request->filled('period_start') && $request->filled('period_end'), function ($query) use ($periodStart, $periodEnd) {
                $query->where('period_start', $periodStart)
                    ->where('period_end', $periodEnd);
            })
            ->orderByDesc('total_salary')
            ->get()
            ->keyBy('user_id');

        $attendanceUsers = AttendanceUser::orderBy('name')->get();
        $storeSettings = StoreSetting::firstOrCreate([]);

        return view('admin.salaries.index', compact('attendanceUsers', 'salaryRecords', 'periodStart', 'periodEnd', 'storeSettings'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $periodStart = Carbon::parse($request->input('period_start'))->toDateString();
        $periodEnd = Carbon::parse($request->input('period_end'))->toDateString();
        $storeSettings = StoreSetting::firstOrCreate([]);
        if ($storeSettings->hourly_salary === null) {
            $storeSettings->hourly_salary = 6000;
            $storeSettings->save();
        }
        $hourlyRate = (float) ($storeSettings->hourly_salary ?? 0);

        $attendanceGroups = Attendance::query()
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->whereBetween('attendance_date', [$periodStart, $periodEnd])
            ->get()
            ->groupBy('attendance_user_id');

        $attendanceUsers = AttendanceUser::orderBy('name')->get();

        foreach ($attendanceUsers as $user) {
            $records = $attendanceGroups->get($user->id, collect());
            $totalSessions = $records->count();
            $totalMinutes = $records->reduce(function ($carry, Attendance $attendance) {
                $checkIn = Carbon::createFromFormat('H:i:s', $attendance->check_in, config('app.timezone'));
                $checkOut = Carbon::createFromFormat('H:i:s', $attendance->check_out, config('app.timezone'));
                return $carry + $checkIn->diffInMinutes($checkOut);
            }, 0);

            $totalHours = $totalMinutes > 0 ? round($totalMinutes / 60, 2) : 0.00;
            $totalSalary = round($totalHours * $hourlyRate, 2);

            $salary = Salary::firstOrNew([
                'user_id' => $user->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ]);

            $salary->total_sessions = $totalSessions;
            $salary->total_hours = $totalHours;
            $salary->hourly_rate = $hourlyRate;
            $salary->total_salary = $totalSalary;

            if (! $salary->exists) {
                $salary->status = 'draft';
            } elseif ($salary->status !== 'paid') {
                $salary->status = 'draft';
                $salary->paid_at = null;
            }

            $salary->save();
        }

        return redirect()->route('admin.salaries.index', ['period_start' => $periodStart, 'period_end' => $periodEnd])
            ->with('success', 'Perhitungan gaji selesai untuk periode yang dipilih.');
    }

    public function updateHourlyRate(Request $request)
    {
        $data = $request->validate([
            'hourly_salary' => 'required|numeric|min:0',
        ]);

        $settings = StoreSetting::firstOrCreate([]);
        $settings->hourly_salary = $data['hourly_salary'];
        $settings->save();

        return redirect()->route('admin.salaries.index', [
            'period_start' => $request->input('period_start', Carbon::now()->startOfMonth()->toDateString()),
            'period_end' => $request->input('period_end', Carbon::now()->endOfMonth()->toDateString()),
        ])->with('success', 'Tarif per jam berhasil disimpan.');
    }

    public function show(Salary $salary)
    {
        $sessions = Attendance::query()
            ->where('attendance_user_id', $salary->user_id)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->whereBetween('attendance_date', [$salary->period_start->toDateString(), $salary->period_end->toDateString()])
            ->orderBy('attendance_date')
            ->orderBy('check_in')
            ->get();

        return view('admin.salaries.show', compact('salary', 'sessions'));
    }

    public function pay(Salary $salary)
    {
        if ($salary->status !== 'paid') {
            $salary->status = 'paid';
            $salary->paid_at = Carbon::now(config('app.timezone'));
            $salary->save();
        }

        return redirect()->route('admin.salaries.show', $salary)
            ->with('success', 'Slip gaji berhasil ditandai sebagai dibayar.');
    }

    public function exportPdf(Salary $salary)
    {
        $sessions = Attendance::query()
            ->where('attendance_user_id', $salary->user_id)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->whereBetween('attendance_date', [$salary->period_start->toDateString(), $salary->period_end->toDateString()])
            ->orderBy('attendance_date')
            ->orderBy('check_in')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.salaries.pdf', compact('salary', 'sessions'))
            ->setPaper('a4', 'portrait');

        $fileName = sprintf('slip-gaji-%s-%s-%s.pdf',
            str()->slug($salary->attendanceUser?->name ?? 'kasir'),
            $salary->period_start->format('Ymd'),
            $salary->period_end->format('Ymd')
        );

        return $pdf->download($fileName);
    }
}
