<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function cashierIndex()
    {
        Log::info('Attendance timezone audit', [
            'timezone' => config('app.timezone'),
            'now' => Carbon::now(config('app.timezone'))->toDateTimeString(),
            'action' => 'cashierIndex',
        ]);

        $attendanceUsers = \App\Models\AttendanceUser::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Attach today's latest attendance record to each attendance user for status display.
        // If there is an active session, prefer that record; otherwise use the latest completed session.
        $today = Carbon::now(config('app.timezone'))->toDateString();
        $todayRecords = Attendance::query()
            ->whereIn('attendance_user_id', $attendanceUsers->pluck('id')->all())
            ->whereDate('attendance_date', $today)
            ->get()
            ->groupBy('attendance_user_id')
            ->mapWithKeys(function ($group, $userId) {
                $activeRecord = $group->first(fn (Attendance $record) => $record->check_out === null);
                if ($activeRecord) {
                    return [$userId => $activeRecord];
                }

                $latestRecord = $group->sortByDesc('created_at')->first();
                return [$userId => $latestRecord];
            });

        foreach ($attendanceUsers as $au) {
            $au->today_attendance = $todayRecords->has($au->id) ? $todayRecords->get($au->id) : null;
        }

        $history = Attendance::query()
            ->with('attendanceUser')
            ->orderByDesc('attendance_date')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        $now = Carbon::now(config('app.timezone'));

        return view('pos.attendance', compact('attendanceUsers', 'history', 'now'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:attendance_users,id'],
            'password' => ['required', 'string'],
        ]);

        $attendanceUser = \App\Models\AttendanceUser::findOrFail($request->input('user_id'));

        if (! $attendanceUser->is_active) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Nama tidak tersedia untuk absensi.'], 422);
            }

            return redirect()->route('pos.attendance.index')->with('error', 'Nama tidak tersedia untuk absensi.');
        }

        if (! Hash::check($request->input('password'), $attendanceUser->password)) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Password salah.'], 422);
            }

            return redirect()->route('pos.attendance.index')->with('error', 'Password salah.');
        }

        $today = Carbon::now(config('app.timezone'))->toDateString();
        $activeAttendance = Attendance::query()
            ->where('attendance_user_id', $attendanceUser->id)
            ->whereDate('attendance_date', $today)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if ($activeAttendance) {
            $checkOut = Carbon::now(config('app.timezone'));
            $checkIn = Carbon::createFromFormat('H:i:s', $activeAttendance->check_in, config('app.timezone'));
            $workingMinutes = (int) floor($checkIn->diffInMinutes($checkOut));

            $activeAttendance->forceFill([
                'check_out' => $checkOut->format('H:i:s'),
                'working_minutes' => $workingMinutes,
                'status' => 'completed',
            ])->save();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(array_merge($this->buildAttendancePayload($attendanceUser->id), [
                    'success' => true,
                    'message' => 'Check Out berhasil.',
                ]));
            }

            return redirect()->route('pos.attendance.index')->with('success', 'Check Out berhasil.');
        }

        Attendance::create([
            'attendance_user_id' => $attendanceUser->id,
            'attendance_date' => $today,
            'check_in' => Carbon::now(config('app.timezone'))->format('H:i:s'),
            'status' => 'working',
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(array_merge($this->buildAttendancePayload($attendanceUser->id), [
                'success' => true,
                'message' => 'Check In berhasil.',
            ]));
        }

        return redirect()->route('pos.attendance.index')->with('success', 'Check In berhasil.');
    }


    private function getTodayAttendance(?int $userId = null): ?Attendance
    {
        if (! $userId) {
            return null;
        }

        $today = Carbon::now(config('app.timezone'))->toDateString();

        $activeAttendance = Attendance::query()
            ->where('attendance_user_id', $userId)
            ->whereDate('attendance_date', $today)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if ($activeAttendance) {
            return $activeAttendance;
        }

        return Attendance::query()
            ->where('attendance_user_id', $userId)
            ->whereDate('attendance_date', $today)
            ->latest('check_in')
            ->first();
    }

    private function getAttendanceHistory(?int $userId = null)
    {
        if (! $userId) {
            return collect();
        }

        return Attendance::query()
            ->where('attendance_user_id', $userId)
            ->orderByDesc('attendance_date')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    private function buildAttendancePayload(?int $userId = null, ?Attendance $attendance = null, $history = null): array
    {
        $selectedUser = $userId ? \App\Models\AttendanceUser::find($userId) : null;
        $freshAttendance = $attendance ?? $this->getTodayAttendance($userId);
        $freshHistory = $history ?? $this->getAttendanceHistory($userId);

        return [
            'selected_user' => $selectedUser ? [
                'id' => $selectedUser->id,
                'name' => $selectedUser->name,
            ] : null,
            'attendance' => $freshAttendance ? [
                'id' => $freshAttendance->id,
                'attendance_date' => $freshAttendance->attendance_date?->toDateString(),
                'check_in' => $freshAttendance->check_in,
                'check_out' => $freshAttendance->check_out,
                'working_minutes' => $freshAttendance->working_minutes,
                'status' => $freshAttendance->status,
                'status_label' => $freshAttendance->display_status,
                'duration_text' => $freshAttendance->formatted_duration,
            ] : null,
            'history' => $freshHistory->map(function (Attendance $record): array {
                return [
                    'id' => $record->id,
                    'attendance_date' => $record->attendance_date?->toDateString(),
                    'check_in' => $record->check_in,
                    'check_out' => $record->check_out,
                    'working_minutes' => $record->working_minutes,
                    'status' => $record->status,
                    'status_label' => $record->display_status,
                    'duration_text' => $record->formatted_duration,
                ];
            })->values()->all(),
            'history_html' => view('pos.partials.attendance-history', ['history' => $freshHistory])->render(),
        ];
    }

    public function adminIndex(Request $request)
    {
        $query = Attendance::query()
            ->with('attendanceUser');

        $search = trim((string) $request->input('search', ''));
        $filter = $request->input('filter', 'all');
        $from = $request->input('from');
        $to = $request->input('to');

        if ($search !== '') {
            $query->whereHas('attendanceUser', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            });
        }

        $now = Carbon::now(config('app.timezone'));

        if ($filter === 'today') {
            $query->whereDate('attendance_date', $now->toDateString());
        } elseif ($filter === 'week') {
            $query->whereBetween('attendance_date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
        } elseif ($filter === 'month') {
            $query->whereBetween('attendance_date', [$now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString()]);
        } elseif ($filter === 'custom' && $from && $to) {
            $query->whereBetween('attendance_date', [$from, $to]);
        }

        $attendances = $query->orderByDesc('attendance_date')
            ->orderByDesc('check_in')
            ->paginate(20);

        // Provide full attendance user list to admin view (show all, include inactive)
        $attendanceUsers = \App\Models\AttendanceUser::query()
            ->orderBy('name')
            ->get();

        $summary = [
            'total' => (clone $query)->count(),
            'not_checked_in' => (clone $query)->whereNull('check_in')->count(),
            'working' => (clone $query)->where('status', 'working')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'total_working_minutes' => (clone $query)->sum('working_minutes'),
        ];

        return view('admin.attendances.index', compact('attendances', 'summary', 'filter', 'search', 'from', 'to', 'attendanceUsers'));
    }

    public function adminData(Request $request)
    {
        $query = Attendance::query()
            ->with('attendanceUser');

        $search = trim((string) $request->input('search', ''));
        $filter = $request->input('filter', 'all');
        $from = $request->input('from');
        $to = $request->input('to');

        if ($search !== '') {
            $query->whereHas('attendanceUser', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            });
        }

        $now = Carbon::now(config('app.timezone'));

        if ($filter === 'today') {
            $query->whereDate('attendance_date', $now->toDateString());
        } elseif ($filter === 'week') {
            $query->whereBetween('attendance_date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
        } elseif ($filter === 'month') {
            $query->whereBetween('attendance_date', [$now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString()]);
        } elseif ($filter === 'custom' && $from && $to) {
            $query->whereBetween('attendance_date', [$from, $to]);
        }

        $attendances = $query->orderByDesc('attendance_date')->orderByDesc('check_in')->get();

        return response()->json([
            'attendances_html' => view('admin.attendances.partials.table', compact('attendances'))->render(),
        ]);
    }
}
