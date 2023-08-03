<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Traits\FileUploaderTrait;
use App\Http\Traits\WorkDraftTrait;
use App\Models\SADAIC\Cities;
use App\Models\SADAIC\Countries;
use App\Models\SADAIC\Genres;
use App\Models\SADAIC\Member;
use App\Models\SADAIC\Role;
use App\Models\SADAIC\Societies;
use App\Models\SADAIC\States;
use App\Models\SADAIC\Types;
use App\Models\Work\Distribution;
use App\Models\Work\File;
use App\Models\Work\Log as InternalLog;
use App\Models\Work\Meta;
use App\Models\Work\Registration;
use App\Http\Requests\WorkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use \Throwable;

class WorkController extends Controller
{
    use WorkDraftTrait, FileUploaderTrait;

    public function __construct()
    {
        $this->middleware('auth:members');
    }

    public function showRegister()
    {
        $roles = Role::all()->sortBy('description');
        $cities = Cities::orderBy('city')->get();
        $states = States::orderBy('state')->get();
        $countries = Countries::select(['idx', 'name_ter'])->orderBy('name_ter')->get();
        $societies = Societies::all();
        $types = Types::all();
        $genres = Genres::orderBy('des_int_gen')->get();

        return view('work.register', [
            'roles'      => $roles,
            'states'     => $states,
            'cities'     => $cities,
            'countries'  => $countries,
            'societies'  => $societies,
            'types'      => $types,
            'genres'     => $genres,
            'max_size'   => $this->getFormattedMaxSize(),
            'max_size_b' => $this->getUploadMaxSize()
        ]);
    }

    public function saveRegister(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'genre_id'            => 'integer|required',

                'dnda_ed_date'        => 'date|nullable',
                'audio_dnda_ed_file'  => 'string|nullable',
                'lyric_dnda_ed_file'  => 'string|nullable',

                'dnda_in_date'        => 'date|nullable',
                'audio_dnda_in_file'  => 'string|nullable',
                'lyric_dnda_in_file'  => 'string|nullable',

                'people.*.type'               => 'string',
                'people.*.fn'                 => 'string|nullable',
                'people.*.member_id'          => 'string|nullable',
                'people.*.doc_number'         => 'string|nullable|required_without:people.*.member_id',
                'people.*.public'             => 'numeric|between:0,100',
                'people.*.mechanic'           => 'numeric|between:0,100',
                'people.*.sync'               => 'numeric|between:0,100',
                'people.*.address_city_text'  => 'string|max:50|nullable',
                'people.*.address_city_id'    => 'integer|nullable',
                'people.*.address_state_text' => 'string|max:50|nullable',
                'people.*.address_state_id'   => 'integer|nullable',
                'people.*.address_country_id' => 'string|max:15|nullable',
                'people.*.address_zip'        => 'string',
                'people.*.apartment'          => 'string|nullable',
                'people.*.birth_country_id'   => 'string',
                'people.*.birth_date'         => 'date',
                'people.*.doc_type'           => 'string',
                'people.*.email'              => 'email',
                'people.*.floor'              => 'string|nullable',
                'people.*.name'               => 'string',
                'people.*.phone_area'         => 'string',
                'people.*.phone_country'      => 'string',
                'people.*.phone_number'       => 'string',
                'people.*.street_name'        => 'string',
                'people.*.street_number'      => 'string'
            ]);

            $registration = $this->saveDraft($request->input());

            return [
                'status'          => 'success',
                'registration_id' => $registration->id,
                'people'          => $registration->distribution
            ];
        } catch (ValidationException $v) {
            throw $v;
        } catch (Throwable $t) {
            Log::error("Error guardando registro de obra incompleto",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'  => 'failed',
                'message' => 'Error desconocido'
            ];
        }
    }

    public function register(WorkRequest $request)
    {
        $registration = $this->saveDraft($request->input());

        if ($registration) {
            // Archivos comunes
            $rules = [
                'audio_file' => 'required',
                'lyric_file' => 'required',
            ];

            // Mensajes comunies
            $messages = [
                'audio_file.required' => 'Se debe incluir el archivo de audio',
                'lyric_file.required' => 'Se debe incluir el archivo de la partitura'
            ];

            $script_required = false;
            $registration->distribution->each(function($item, $key) use ($registration, &$rules, &$messages, &$script_required) {
                // Verificación de autor o coautor
                if ($item->fn == 'A' || $item->fn == 'CA') {
                    $script_required = true;
                }

                // Verificación documentación no socios
                if ($item->type == 'no-member' && !$registration->files->contains('distribution_id', $item->id)) {
                    $rules["documentation_$key"] = 'required';
                    $messages["documentation_$key.required"] = 'Se debe incluir el DNI de ' . $item->meta->name;
                }
            });

            // Verificación de letra
            $rules['script_file'] = $script_required ? 'required' : 'nullable';
            $messages['script_file.required'] = 'Se debe incluir el archivo de la letra';

            // Verificación contrato DNDA
            if ($registration->audio_dnda_ed_file || $registration->lyric_dnda_ed_file || $registration->audio_dnda_in_file || $registration->lyric_dnda_in_file) {
                if (!$registration->files->contains('name', 'file_dnda_contract')) {
                    $rules['file_dnda_contract'] = 'required';
                    $messages['file_dnda_contract.required'] = 'Se debe incluir la constancia DNDA';
                }
            }

            $validator = Validator::make(
                $registration->files->pluck('path', 'name')->toArray(),
                $rules,
                $messages
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->getMessageBag()->toArray()
                ], 422);
            }
        }

        $registration->status_id = 1;
        $registration->entry_date = now();
        $registration->save();

        InternalLog::create([
            'registration_id' => $registration->id,
            'action_id'       => 1, // REGISTRATION_CREATED
            'time'            => now()
        ]);

        return [
            'status' => 'success',
            'id'     => $registration->id
        ];
    }

    public function searchAuthor(Request $request)
    {
        $validatedData = $request->validate([
            'query' => 'required|integer'
        ]);

        $members = Member::where('codanita', $request->input('query'))
        ->orWhere('num_doc', $request->input('query'))
        ->where('codanita', '!=', '')
        ->orderBy('nombre')
        ->get();

        return $members;
    }

    public function showList()
    {
        $requests = Registration::where('member_id', Auth::user()->id)
        ->orWhere(function($query) {
            $query->whereHas('distribution', function ($query) {
                $query->where('member_id', Auth::user()->member_id);
            })
            ->whereNotNull('status_id');
        })
        ->orderBy('id', 'desc')
        ->get();

        return view('member.work.list', [
            'requests' => $requests
        ]);
    }

    public function showEdit(Registration $registration)
    {
        $countries = Countries::select(['idx', 'name_ter'])->orderBy('name_ter')->get();
        $cities = Cities::orderBy('city')->get();
        $genres = Genres::orderBy('des_int_gen')->get();
        $roles = Role::all()->sortBy('description');
        $states = States::orderBy('state')->get();
        $types = Types::all();
        $distribution = Distribution::where('registration_id', $registration->id)->get();

        return view('work.edit', [
            'request'      => $registration,
            'cities'       => $cities,
            'countries'    => $countries,
            'genres'       => $genres,
            'roles'        => $roles,
            'states'       => $states,
            'types'        => $types,
            'distribution' => $distribution,
            'max_size'     => $this->getFormattedMaxSize(),
            'max_size_b'   => $this->getUploadMaxSize()
        ]);
    }

    public function showView(Registration $registration)
    {
        return view('work.view', [
            'registration' => $registration
        ]);
    }

    public function deleteDistribution(Request $request)
    {
        try {
            // Obtenemos la distribución con el id
            $distribution = Distribution::find($request->input('distribution_id'));

            // Si no existe (todavía no se guardó), lo damos por bueno
            if (!$distribution) {
                return [
                    'status' => 'success'
                ];
            }

            // Si la distribución no pertenece al registro
            if ($distribution->registration_id != $request->input('registration_id')) {
                abort(403);
            }

            // Si el registro no pertenece al socio
            if ($distribution->registration->member_id != Auth::user()->id) {
                abort(403);
            }

            // Eliminamos los archivos asociados a la distribución
            foreach($distribution->files as $file) {
                Storage::delete($file->path);
                $file->delete();
            }

            // Eliminamos la metadata asociada a la persona
            if ($distribution->type == 'no-member') {
                $distribution->meta->delete();
            }

            // Eliminamos la distribución
            $distribution->delete();

            return [
                'status' => 'success'
            ];
        } catch (Throwable $t) {
            Log::error("Error eliminando distribución",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'  => 'failed',
                'message' => 'Error desconocido'
            ];
        }
    }

    public function showResponse(Registration $registration)
    {
        // Si el status no es el correcto
        if ($registration->status_id != 2 && $registration->status_id != 3) {
            return redirect()->action('Member\WorkController@showList');
        }

        // Si no es una de las partes
        if (!$registration->distribution->contains('member_id', Auth::user()->member_id)) {
            abort(403);
        }

        return view('member.work.response', [
            'registration' => $registration
        ]);
    }

    public function response(Request $request, Registration $registration)
    {
        try {
            if (!$request->has('response')) {
                abort(403);
            }

            if ($request->input('response') != 'accept' && $request->input('response') != 'reject') {
                abort(403);
            }

            $member_id = Auth::user()->member_id;

            $distribution = $registration->distribution->where('member_id', $member_id)->first();
            // Si el socio no es parte de la distribución del registro
            if (!$distribution) {
                abort(403);
            }

            // Si ya respondió que si, no se puede cambiar
            if ($distribution->response == true) {
                return [
                    'status' => 'failed',
                    'errors' => [
                        'No se puede cambiar la respuesta a una solicitud de registro ya aceptada'
                    ]
                ];
            }

            $distribution->response = $request->input('response') == 'accept';
            $distribution->liable_id = null;
            $distribution->save();

            $registration->updated_at = now();
            $registration->save();

            // action_id = 6 -> DISTRIBUTION_CONFIRMED
            // action_id = 7 -> DISTRIBUTION_REJECTED
            InternalLog::create([
                'registration_id' => $registration->id,
                'distribution_id' => $distribution->id,
                'action_id'       => $request->input('response') == 'accept' ? 6 : 7,
                'time'            => now()
            ]);

            // Chequeamos si todas las partes aprobaron el trámite
            $finished = $registration->distribution->every(function ($current, $key) {
                return !!$current->response;
            });

            // Si el trámite está terminado...
            if ($finished) {
                $registration->status_id = 5; // Aprobado Propietarios
            // Si la respuesta fue negativa
            } elseif (!$distribution->response) {
                $registration->status_id = 3; // Disputa Propietarios
            }

            $registration->save();

            return [
                'status' => 'success'
            ];
        } catch (Throwable $t) {
            Log::error("Error registrando respuesta de socio a un solicitud de registro de obra",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status' => 'failed',
                'errors' => [
                    'Se produjo un error desconocido al momento de registrar su respuesta'
                ]
            ];
        }
    }

    public function deleteRegistration(Registration $registration)
    {
        // Si no se puede borrar una solicitud iniciado por otra persona
        if ($registration->member_id !== Auth::user()->id) {
            abort(403);
        }

        // Si la solicitud ya está en trámite no se borrar
        if($registration->status_id !== null) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($registration) {
                // Datos de los no socios
                foreach($registration->distribution as $distribution) {
                    Meta::where('distribution_id', $distribution->id)->delete();
                }

                File::where('registration_id', $registration->id)->delete();
                InternalLog::where('registration_id', $registration->id)->delete();
                Distribution::where('registration_id', $registration->id)->delete();

                $registration->delete();
            });

            return redirect()->action('Member\WorkController@showList')->with([
                'message.type' => 'success',
                'message.data' => 'Solicitud eliminada correctamente'
            ]);
        } catch (Throwable $t) {
            Log::error("Error eliminando registro obra",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return redirect()->action('Member\WorkController@showList')->with([
                'message.type' => 'danger',
                'message.data' => 'Se produjo un error al intentar eliminar la solicitud'
            ]);
        }
    }
}
