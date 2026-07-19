<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::first();

        if (! $settings) {
            $settings = StoreSetting::create([]);
        }

        return view('admin.settings', [
            'settings' => $settings,
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $settings = StoreSetting::firstOrCreate([]);

        $section = $request->input('section');

        if ($section === 'store') {
            $data = $request->validate([
                'store_name' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:50',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');

                $path = $file->store('logos', 'public');

                // delete old file if exists
                if ($settings->logo_path) {
                    @Storage::disk('public')->delete($settings->logo_path);
                }

                $data['logo_path'] = $path;
            }

            $settings->update($data);

            return back()->with('success', 'Informasi toko berhasil disimpan.');
        }

        if ($section === 'transaction') {
            $data = $request->validate([
                'tax_percentage' => 'required|numeric|min:0|max:100',
                'rounding' => 'required|integer|min:0',
                'transaction_number_format' => 'nullable|string|max:255',
            ]);

            $settings->update($data);

            return back()->with('success', 'Pengaturan transaksi berhasil disimpan.');
        }

        if ($section === 'stock') {
            $data = $request->validate([
                'default_stock_min' => 'required|integer|min:0',
                'notify_low_stock' => 'sometimes|boolean',
            ]);

            $data['notify_low_stock'] = $request->boolean('notify_low_stock');

            $settings->update($data);

            return back()->with('success', 'Pengaturan stok berhasil disimpan.');
        }

        if ($section === 'barcode') {
            // Barcode rules are enforced in Product model/controller.
            return back()->with('success', 'Pengaturan barcode: EAN-13 dipaksa dan dibuat otomatis.');
        }

        if ($section === 'account') {
            $data = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = auth()->user();

            if (! Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak cocok.']);
            }

            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);

            return back()->with('success', 'Password berhasil diubah.');
        }

        return back()->with('error', 'Section tidak dikenali.');
    }
}
