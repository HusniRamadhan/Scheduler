<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required', function ($attribute, $value, $fail) {
                $user = User::find(Auth::id());
                if (!($user && Hash::check($value, $user->password))) {
                    return $fail(__('Password Lama Tidak Sesuai.'));
                }
            }],
            'password' => [
                'required',
                'confirmed',
                function ($attribute, $value, $fail) {
                    $user = User::find(Auth::id());
                    if ($user && Hash::check($value, $user->password)) {
                        return $fail(__('Password Baru Tidak Boleh Sama Dengan Password Lama.'));
                    }
                }
            ],
        ], [
            'password.confirmed' => __('Konfirmasi Password Baru Gagal dikarenakan ketidaksesuaian antara Password Baru dan Konfirmasinya.'),
        ]);

        $user = User::find(Auth::id());
        if ($user) {
            $user->password = bcrypt($request->password);
            $user->save();

            Auth::logout();

            return redirect()->route('home')->with('password_success', 'Password berhasil diubah! Silakan login kembali.');
        }

        return back()->with('password_error', 'Password Lama Tidak Sesuai.');
    }
}
