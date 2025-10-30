<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('manager.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $manager = Manager::where('username', $request->username)
            ->where('status', 'active')
            ->first();

        if (!$manager || !Hash::check($request->password, $manager->password)) throw ValidationException::withMessages([
            'username' => ['아이디 또는 비밀번호가 올바르지 않습니다.'],
        ]);

        Auth::guard('manager')->login($manager, $request->boolean('remember'));

        $manager->update(['last_login_at' => now()]);

        return redirect()->intended(route('manager.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('manager')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('manager.login');
    }
}
