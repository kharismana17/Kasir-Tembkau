<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPinController extends Controller
{
    public function edit(User $user)
    {
        return view('admin.users.pin', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'pin' => ['required', 'string', 'min:4', 'max:8'],
        ]);

        $user->setPin($data['pin']);

        return redirect()->route('admin.cashiers.index')->with('success', 'PIN berhasil diatur.');
    }
}
