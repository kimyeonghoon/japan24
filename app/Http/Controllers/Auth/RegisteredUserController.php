<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        // 회원가입이 차단되어 있는지 확인
        if (!SystemSetting::isRegistrationEnabled()) {
            return redirect()->route('login')->with('error', '현재 회원가입이 일시적으로 중단되어 있습니다. 관리자에게 문의해주세요.');
        }

        return view('auth.register');
    }

    public function store(Request $request)
    {
        // 회원가입이 차단되어 있는지 확인
        if (!SystemSetting::isRegistrationEnabled()) {
            return redirect()->route('login')->with('error', '현재 회원가입이 일시적으로 중단되어 있습니다. 관리자에게 문의해주세요.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}