<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $userInfo = User::where('email', $request->email)->first();
        if (!$userInfo) {
            return back()->with('fail', 'Usuario o contraseña incorrecto');
        } else if ($userInfo->status == 'I') {
            return back()->with('fail', 'El usuario ' . $userInfo->name . ' ' . $userInfo->lastName . ' se encuentra Inactivo');
        } else {
            if (Hash::check($request->password, $userInfo->password)) {
                $request->session()->put('LoggerUser', $userInfo);
                Auth::login($userInfo, true);
                return redirect($userInfo->roles()->first()->modulo_ini);
            } else {
                return back()->with('fail', 'Usuario o contraseña incorrecto');
            };
        }
    }

    public function Logout()
    {
        session()->pull('LoggerUser');
        Auth::logout();
        return redirect('/login');
    }
}
