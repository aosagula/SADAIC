<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        // Socios
        if ($request->has(['member_id', 'heir', 'email'])) {
            
            // Intérpretes
        } else if ($request->has(['player_id', 'email'])) {

        // Usuarios
        } else if ($request->has(['email'])) {
            $this->validateEmail($request);

            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            switch($response) {
                case Password::RESET_LINK_SENT:
                    return [ 'status' => 'success' ];
                case Password::INVALID_USER:
                    return [ 'status' => 'failed', 'errors' => ['error' => 'La dirección de correo electrónico no se encuentra registrada en el sistema de administración.'] ];
                case Password::RESET_THROTTLED:
                    return [ 'status' => 'failed', 'errors' => ['error' => 'Hubo un error procesando su solicitud, por favor, intente nuevamente más tarde.'] ];
                default:
                    return [ 'status' => 'failed' ];
            }
        }
    }
}
