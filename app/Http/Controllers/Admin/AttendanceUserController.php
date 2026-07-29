<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AttendanceUserController extends Controller
{
    public function store(Request $request)
    {
        Log::info('STORE MASUK');
        Log::info('STORE PAYLOAD', $request->all());

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:attendance_users,name'],
            'password' => ['required', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $attendanceUser = AttendanceUser::create([
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
                'is_active' => (bool) $request->input('is_active', true),
            ]);

            Log::info('BERHASIL INSERT', $attendanceUser->toArray());

            return redirect()->route('admin.attendances.index')->with('success', 'Nama berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('Failed to create AttendanceUser: ' . $e->getMessage());
            Log::error('STORE EXCEPTION', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return redirect()->route('admin.attendances.index')->with('error', 'Gagal menambahkan nama kasir. Periksa log untuk detail.');
        }
    }

    public function update(Request $request, $user)
    {
        $attendanceUser = AttendanceUser::findOrFail($user);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:attendance_users,name,' . $attendanceUser->id],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $attendanceUser->name = $request->input('name');
        $attendanceUser->is_active = (bool) $request->input('is_active', false);

        if ($request->filled('password')) {
            $attendanceUser->password = Hash::make($request->input('password'));
        }

        $attendanceUser->save();

        return redirect()->route('admin.attendances.index')->with('success', 'Data nama kasir absensi diperbarui.');
    }

    public function destroy($user)
    {
        $attendanceUser = AttendanceUser::findOrFail($user);

        Log::info('DELETE ATTENDANCE USER', [
            'id' => $attendanceUser->id,
            'name' => $attendanceUser->name,
        ]);

        $hasAttendances = $attendanceUser->attendances()->exists();

        Log::info('HAS ATTENDANCE', [
            'exists' => $hasAttendances,
        ]);

        try {
            if ($hasAttendances) {
                $attendanceUser->is_active = false;
                $attendanceUser->save();

                $attendanceUser->refresh();

                return redirect()->route('admin.attendances.index')->with('success', 'Nama kasir memiliki data absensi, dinonaktifkan.');
            }

            $result = $attendanceUser->delete();

            Log::info('DELETE RESULT', [
                'result' => $result,
            ]);

            $attendanceUser->refresh();

            if (! $attendanceUser->exists) {
                return redirect()->route('admin.attendances.index')->with('success', 'Nama kasir dihapus.');
            }

            return redirect()->route('admin.attendances.index')->with('error', 'Gagal menghapus nama kasir.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            Log::error('DELETE ATTENDANCE USER FAILED', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.attendances.index')->with('error', 'Gagal menghapus nama kasir.');
        }
    }
}
