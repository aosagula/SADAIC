<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\SADAIC\Integration;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(Integration $sadaic)
    {
        $this->middleware('guest:web')->except('logout');
        $this->middleware('guest:members')->except('logout');
        $this->middleware('guest:players')->except('logout');
        $this->sadaic = $sadaic;
    }

    public function login(Request $request)
    {
        try {
            $intended = RouteServiceProvider::HOME;
            $test = true;

            // Login Socios
            if ($request->has(['member_id', 'heir', 'password'])) {
                $credentials = $request->only(['member_id', 'heir', 'password']);
                $test = Auth::guard('members')->attempt($credentials);
                $intended = '/member';

                // Login a SADAIC para obtener cookie
                if ($test) {
                    $this->sadaic->login(
                        $credentials['member_id'],
                        $credentials['heir'],
                        $credentials['password']
                    );
                }

            // Login Intérpretes
            } else if ($request->has(['player_id', 'password'])) {
                $credentials = $request->only(['player_id', 'password']);
                $test = Auth::guard('players')->attempt($credentials);
                $intended = '/player';

                if ($test) {
                    return view('player.login', $credentials);
                }

            // Login Usuarios
            } else if ($request->has(['email', 'password'])) {
                $credentials = $request->only(['email', 'password']);
                $test = Auth::guard('web')->attempt($credentials);
                $intended = '/user';

            // Si no matcheó ninguno, nosvi
            } else {
                abort(400);
            }

            if($request->ajax()) {
                if ($test) {
                    return [
                        'status'   => 'success',
                        'intended' => $intended
                    ];
                }

                return [
                    'status' => 'failed',
                    'errors' => [
                        'login' => 'El usuario o clave son incorrectos'
                    ]
                ];
            }

            if ($test) {
                return redirect()->intended($intended);
            }

            return back();
        } catch (\Throwable $error) {
            if($request->ajax()) {
                return [
                    'status' => 'failed',
                    'errors' => [
                        'login' => 'Ocurrió un problema al validar las credenciales. Por favor, intente nuevamente más tarde'
                    ]
                ];
            }

            return back()->with('errors', $error);
        }
    }

    public function logout()
    {
        Auth::guard('members')->logout();
        Auth::guard('players')->logout();
        Auth::guard('web')->logout();

        return redirect('/login');
    }
}
