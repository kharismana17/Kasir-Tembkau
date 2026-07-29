<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_check_in_and_check_out_persist_and_return_latest_data(): void
    {
        $kasirRole = Role::create([
            'name' => 'Kasir',
            'slug' => 'kasir',
            'description' => 'Kasir',
        ]);

        $kasir = User::create([
            'name' => 'Kasir User',
            'email' => 'kasir@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'is_active' => true,
        ]);

        $attendanceUser = AttendanceUser::create([
            'name' => 'Kasir User',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->actingAs($kasir);

        $verifyResponse = $this->postJson('/pos/attendance/verify', [
            'user_id' => $attendanceUser->id,
            'password' => 'password',
        ]);
        $verifyResponse->assertOk();
        $verifyResponse->assertJsonPath('success', true);

        $attendance = Attendance::query()
            ->where('attendance_user_id', $attendanceUser->id)
            ->whereDate('attendance_date', now()->toDateString())
            ->latest()
            ->first();
        $this->assertNotNull($attendance);
        $this->assertSame('working', $attendance->status);
        $this->assertNotNull($attendance->check_in);

        $checkOutResponse = $this->postJson('/pos/attendance/verify', [
            'user_id' => $attendanceUser->id,
            'password' => 'password',
        ]);
        $checkOutResponse->assertOk();
        $checkOutResponse->assertJsonPath('success', true);

        $updatedAttendance = Attendance::find($attendance->id);
        $this->assertNotNull($updatedAttendance->check_out);
        $this->assertSame('completed', $updatedAttendance->status);
        $this->assertGreaterThanOrEqual(0, $updatedAttendance->working_minutes);
    }

    public function test_multiple_cashiers_can_check_in_for_the_same_day_without_overwriting_each_other(): void
    {
        $kasirRole = Role::create([
            'name' => 'Kasir',
            'slug' => 'kasir',
            'description' => 'Kasir',
        ]);

        $andi = User::create([
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'is_active' => true,
        ]);

        $budi = User::create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'is_active' => true,
        ]);

        $andiAttendanceUser = AttendanceUser::create([
            'name' => 'Andi',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $budiAttendanceUser = AttendanceUser::create([
            'name' => 'Budi',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->actingAs($andi);
        $this->postJson('/pos/attendance/verify', [
            'user_id' => $andiAttendanceUser->id,
            'password' => 'password',
        ])->assertOk();

        $this->actingAs($budi);
        $this->postJson('/pos/attendance/verify', [
            'user_id' => $budiAttendanceUser->id,
            'password' => 'password',
        ])->assertOk();

        $todayAttendances = Attendance::query()
            ->whereDate('attendance_date', now()->toDateString())
            ->whereIn('attendance_user_id', [$andiAttendanceUser->id, $budiAttendanceUser->id])
            ->get();

        $todayAttendances = Attendance::query()
            ->whereDate('attendance_date', now()->toDateString())
            ->whereIn('attendance_user_id', [$andiAttendanceUser->id, $budiAttendanceUser->id])
            ->get();

        $this->assertCount(2, $todayAttendances);
        $this->assertTrue($todayAttendances->contains('attendance_user_id', $andiAttendanceUser->id));
        $this->assertTrue($todayAttendances->contains('attendance_user_id', $budiAttendanceUser->id));
    }

    public function test_admin_can_delete_attendance_user_without_attendance_history(): void
    {
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $attendanceUser = AttendanceUser::create([
            'name' => 'Tanpa Riwayat',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.attendances.users.destroy', $attendanceUser));

        $response->assertRedirect(route('admin.attendances.index'));
        $this->assertDatabaseMissing('attendance_users', ['id' => $attendanceUser->id]);
    }

    public function test_admin_deactivates_attendance_user_when_attendance_history_exists(): void
    {
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin2@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $attendanceUser = AttendanceUser::create([
            'name' => 'Punya Riwayat',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        Attendance::create([
            'attendance_user_id' => $attendanceUser->id,
            'attendance_date' => now()->toDateString(),
            'check_in' => '08:00:00',
            'check_out' => '16:00:00',
            'working_minutes' => 480,
            'status' => 'completed',
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.attendances.users.destroy', $attendanceUser));

        $response->assertRedirect(route('admin.attendances.index'));
        $this->assertDatabaseHas('attendance_users', ['id' => $attendanceUser->id, 'is_active' => false]);
        $this->assertDatabaseHas('attendances', ['attendance_user_id' => $attendanceUser->id]);
    }
}
