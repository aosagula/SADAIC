<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function reset(Request $request)
    {
        // Socios
        if ($request->has(['member_id', 'heir', 'email'])) {
            
            // Intérpretes
        } else if ($request->has(['player_id', 'email'])) {

        // Usuarios
        } else if ($request->has(['email'])) {
            $request->validate($this->rules(), $this->validationErrorMessages());

            $response = $this->broker()->reset(
                $this->credentials($request), function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            if ($response == Password::PASSWORD_RESET) {
                return [ 'status' => 'success' ];
            } else {
                return [ 'status' => 'failed' ];
            }
        }
    }
}
