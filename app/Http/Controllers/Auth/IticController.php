<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IticController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('cache.headers:no_store');
    }

    /**
     * Show ITIC login page
     *
     * @return \Illuminate\Http\Response
     */
    public function showLogin()
    {
        return view('auth.itic.login', [
            'remoteAddr' => request()->server('REMOTE_ADDR'),
        ]);
    }

    /**
     * Logs in user via ITIC
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function doLogin(Request $request)
    {
        $data = $request->validate(
            [
                'username' => 'required|exists:users,ypareo_login',
                'password' => 'required',
                'remember' => 'sometimes|required|boolean',
            ],[
                'username.exists' => __('The username or password is incorrect.'),
            ]
        );

        if (auth()->attempt(['ypareo_login' => $data['username'], 'password' => $data['password']], $data['remember'] ?? false)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => __('The username or password is incorrect.'),
        ])->onlyInput('username');
    }
}
