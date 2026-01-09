<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    // public function reset(Request $request)
    // {
    //     $request->validate([
    //         'token' => 'required',
    //         'email' => 'required|email',
    //         'password' => 'required|min:8|confirmed'
    //     ]);

    //     $status = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function ($user, $password) {
    //             $user->forceFill([
    //                 'password' => Hash::make($password),
    //             ])->save();
    //         }
    //     );

    //     return $status === Password::PASSWORD_RESET
    //         ? response()->json(['message' => 'Password berhasil direset'])
    //         : response()->json(['message' => 'Token tidak valid'], 400);
    // }

    public function resetPasswordDirect(Request $request)
{
    $request->validate([
        'password' => 'required|min:8|confirmed',
    ]);

    $user = auth()->user();

    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json([
        'message' => 'Password berhasil diubah'
    ]);
}
}
