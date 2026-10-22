<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Login warga belajar pakai username atau NISN. */
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $login = trim($data['username']);
        $user = User::where('username', $login)->orWhere('email', $login)->first()
            ?? Student::where('nisn', $login)->first()?->user;

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['username' => 'Username/NISN atau password salah.']);
        }
        if (! $user->student || ! $user->student->active) {
            throw ValidationException::withMessages(['username' => 'Akun ini bukan akun warga belajar aktif.']);
        }

        return response()->json([
            'token' => $user->createToken($data['device_name'] ?? 'android')->plainTextToken,
            'user' => StudentController::present($user->student),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['data' => ['message' => 'Berhasil keluar.']]);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password:sanctum'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $request->user()->update(['password' => $data['password']]);

        return response()->json(['data' => ['message' => 'Password berhasil diganti.']]);
    }
}
