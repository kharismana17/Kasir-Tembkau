<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::latest()->get();

        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods,code'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama metode pembayaran wajib diisi.',
            'code.required' => 'Kode metode pembayaran wajib diisi.',
            'code.unique' => 'Kode metode pembayaran sudah digunakan.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        PaymentMethod::create($data);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view(
            'admin.payment-methods.edit',
            compact('paymentMethod')
        );
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('payment_methods', 'code')
                    ->ignore($paymentMethod->id),
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama metode pembayaran wajib diisi.',
            'code.required' => 'Kode metode pembayaran wajib diisi.',
            'code.unique' => 'Kode metode pembayaran sudah digunakan.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $paymentMethod->update($data);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active' => ! $paymentMethod->is_active,
        ]);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Status metode pembayaran berhasil diubah.');
    }
}