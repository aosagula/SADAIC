<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\SADAIC\Socio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:members');
    }

    public function showUpdate()
    {
        $member = Auth::user();

        return view('member.password-update', [
            'member_id'       => $member->member_id,
            'heir'            => $member->heir
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
        ]);

        try {
            $member = Auth::user();

            // Como la autenticación se sigue manejando con la BBDD vieja, necesitamos recuperar el
            // registro del socio
            $socio = Socio::where('socio', $member->member_id)
                ->where('heredero', $member->heir)
                ->whereBetween('status', [0, 3]);

            if (!$socio) {
                throw new Exception('Socio no encontrado');
            }

            // Para mantener la compatibilidad utilizamos este sistema en vez del Hash provisto por Laravel
            $socio->update([
                'clave' => hash('sha512', $request->newPassword . env('SADAIC_HASH'))
            ]);

            $message_type = 'success';
            $message_data = 'La clave se cambió correctamente.';
        } catch (Throwable $t) {
            $message_type = 'danger';
            $message_data = 'Ocurrió un error al intentar realizar el cambio de clave, por favor, pruebe nuevamente más tarde';

            Log::error("Error realizando actualización de datos",
                [
                    "error" => json_encode($t, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE ),
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );
        }

        return redirect()->action('Member\PasswordController@showUpdate')->with([
            'message.type' => $message_type,
            'message.data' => $message_data
        ]);
    }
}