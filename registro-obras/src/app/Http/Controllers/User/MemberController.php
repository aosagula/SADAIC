<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRequest;
use App\Models\SADAIC\Cities;
use App\Models\SADAIC\Countries;
use App\Models\SADAIC\Genres;
use App\Models\SADAIC\States;
use App\Models\MemberRegistration;
use App\Models\Work\Registration;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use \Throwable;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware('verified');
    }

    public function showRegister(Request $request)
    {
        $cities = Cities::orderBy('city')->get();
        $states = States::orderBy('state')->get();
        $countries = Countries::select(['idx', 'name_ter'])->orderBy('name_ter')->get();
        $genres = Genres::orderBy('des_int_gen')->get();

        $work_id = $request->input('work_id');
        $work = Registration::find($work_id);
        if (optional($work)->user_id != Auth::user()->id) {
            $work = null;
        }

        return view('user.member.register', [
            'cities'    => $cities,
            'countries' => $countries,
            'genres'    => $genres,
            'states'    => $states,
            'work'      => $work
        ]);
    }

    public function register(MemberRequest $request)
    {
        try {
            $params = $request->only([ 'name', 'birth_date', 'birth_city_id', 'birth_city_text', 'birth_state_id', 'birth_state_text',
                'birth_country_id', 'doc_number', 'doc_country', 'work_code', 'address_street', 'address_number', 'address_floor', 'address_apt',
                'address_zip', 'address_city_id', 'address_city_text', 'address_state_id', 'address_state_text', 'address_country_id', 'landline',
                'mobile', 'email', 'pseudonym', 'band', 'entrance_work', 'genre_id', 'work_id' ]);

            $params['user_id'] = Auth::user()->id;
            $params['address_floor'] = $request->input('address_floor', null) ?? '';
            $params['address_apt'] = $request->input('address_apt', null) ?? '';

            if (isset($params['work_id']) && $params['work_id']) {
                $work = Registration::find($params['work_id']);
                if (optional($work)->user_id != Auth::user()->id) {
                    $params['work_id'] = null;
                }
            } else {
                $params['work_id'] = null;
            }

            if ($params['work_id']) {
                $params['status_id'] = 2; // En espera (de registro de obra)
            } else {
                $params['status_id'] = 1; // Pendiente
            }

            $reg = MemberRegistration::create($params);

            return redirect()->action('User\MemberController@showList')->with([
                'message.type' => 'success',
                'message.data' => 'Su solicitud de registro de socio se cargó correctamente'
            ]);
        } catch (ValidationException $v) {
            throw $v;
        } catch (Throwable $t) {
            throw $t;

            Log::error("Error realizando registro de socio",
                [
                    "error" => json_encode($t, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE ),
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return redirect()->back()->with([
                'message.type' => 'danger',
                'message.data' => 'Ocurrió un error al guardar su solicitud de registro, por favor, intente nuevamente más tarde'
            ])->withInput();
        }
    }

    public function showList()
    {
        $requests = MemberRegistration::where('user_id', Auth::user()->id)->get();

        return view('user.member.list', [
            'requests' => $requests
        ]);
    }

    public function showEdit(MemberRegistration $registration)
    {
        if ($registration->user_id != Auth::user()->id) {
            abort(403);
        }

        $cities = Cities::orderBy('city')->get();
        $states = States::orderBy('state')->get();
        $countries = Countries::select(['idx', 'name_ter'])->orderBy('name_ter')->get();
        $genres = Genres::orderBy('des_int_gen')->get();

        return view('user.member.edit', [
            'request' => $registration,
            'cities'    => $cities,
            'countries' => $countries,
            'genres'    => $genres,
            'states'    => $states
        ]);
    }

    public function edit(MemberRequest $request, MemberRegistration $registration)
    {
        if ($registration->user_id != Auth::user()->id) {
            abort(403);
        }

        try {
            $params = $request->only([ 'name', 'birth_date', 'birth_city_id', 'birth_city_text', 'birth_state_id', 'birth_state_text',
                'birth_country_id', 'doc_number', 'doc_country', 'work_code', 'address_street', 'address_number', 'address_floor', 'address_apt',
                'address_zip', 'address_city_id', 'address_city_text', 'address_state_id', 'address_state_text', 'address_country_id', 'landline',
                'mobile', 'email', 'pseudonym', 'band', 'entrance_work', 'genre_id' ]);

            $params['address_floor'] = $request->input('address_floor', null) ?? '';
            $params['address_apt'] = $request->input('address_apt', null) ?? '';

            $registration->fill($params);

            $registration->save();

            return redirect()->action('User\MemberController@showEdit', [
                'registration' => $registration->id
            ])->with([
                'message.type' => 'success',
                'message.data' => 'Los cambios en su solicitud de registro de socio se cargaron correctamente'
            ]);
        } catch (Throwable $t) {
            Log::error("Error actualizando registro de socio",
                [
                    "error" => json_encode($t, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE ),
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return redirect()->action('User\MemberController@showEdit', [
                'registration' => $registration->id
            ])->with([
                'message.type' => 'danger',
                'message.data' => 'Ocurrió un error al editar su solicitud de registro, por favor, intente nuevamente más tarde'
            ])->withInput();
        }
    }

    public function showProfile()
    {
        return view('user.member.profile', [
            'user' => Auth::user()
        ]);
    }

    public function profile(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'required|string|min:10'
            ]);

            $user = Auth::user();

            // Cambió el mail?
            $resend = ($user->email != $request->input('email'));

            // Verificar que sea único
            if ($resend) {
                $validatedData = $request->validate([
                    'email' => 'required|string|email|max:255|unique:users'
                ]);
            }

            // Actualizar datos
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->save();

            // Reenviar correo de verificación
            if ($resend) {
                $user->sendEmailVerificationNotification();
                $user->email_verified_at = null;
                $user->save();

                return redirect()->action('User\MemberController@showProfile')->with([
                    'message.type' => 'warning',
                    'message.data' => 'Sus datos se actualizaron correctamente. Cómo su dirección de correo electrónico cambió deberá realizar la verificación de la misma a través del enlace que recibirá en su casilla.'
                ]);
            }

            return redirect()->action('User\MemberController@showProfile')->with([
                'message.type' => 'success',
                'message.data' => 'Sus datos se actualizaron correctamente'
            ])->withInput();
        } catch (ValidationException $v) {
            throw $v;
        } catch (Throwable $t) {
            Log::error("Error actualizando perfil de usuario",
                [
                    "error" => json_encode($t, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE ),
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return redirect()->action('User\MemberController@showProfile')->with([
                'message.type' => 'danger',
                'message.data' => 'Ocurrió un error al actualizar sus datos, por favor, intente nuevamente más tarde'
            ])->withInput();
        }
    }

    public function showPassword()
    {
        return view('user.member.password');
    }

    public function password(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'old_password' => 'required|string',
                'password'     => 'required|string|min:8|confirmed',
            ]);

            $user = Auth::user();
            $credentials = [
                'email'    => $user->email,
                'password' => $request->input('old_password')
            ];

            if (!Auth::attempt($credentials)) {
                return redirect()->action('User\MemberController@showPassword')->with([
                    'message.type' => 'danger',
                    'message.data' => 'La clave actual que ingresó no es correcta.'
                ])->withInput();
            }

            $user->forceFill([
                'password' => Hash::make($request->input('password'))
            ])->save();

            $user->setRememberToken(Str::random(60));

            event(new PasswordReset($user));

            return redirect()->action('User\MemberController@showPassword')->with([
                'message.type' => 'success',
                'message.data' => 'Su clave se actualizó correctamente'
            ])->withInput();
        } catch (ValidationException $v) {
            throw $v;
        } catch (Throwable $t) {
            Log::error("Error actualizando clave de usuario",
                [
                    "error" => json_encode($t, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE ),
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return redirect()->action('User\MemberController@showPassword')->with([
                'message.type' => 'danger',
                'message.data' => 'Ocurrió un error al actualizar su clave, por favor, intente nuevamente más tarde'
            ])->withInput();
        }
    }

    public function show(MemberRegistration $registration)
    {
        if ($registration->user_id != Auth::user()->id) {
            abort(403);
        }

        $cities = Cities::orderBy('city')->get();
        $states = States::orderBy('state')->get();
        $countries = Countries::select(['idx', 'name_ter'])->orderBy('name_ter')->get();
        $genres = Genres::orderBy('des_int_gen')->get();

        return view('user.member.view', [
            'request'   => $registration,
            'cities'    => $cities,
            'countries' => $countries,
            'genres'    => $genres,
            'states'    => $states
        ]);
    }
}