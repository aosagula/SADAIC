<?php

namespace App\Http\Controllers;

use App\Mail\NotifyAgreement;
use App\Mail\NotifyRequestFinalization;
use App\Mail\NotifyRequestRejection;
use App\Mail\NotifyRequestSendToInternal;
use App\Mail\NotifyJingleApproval;
use App\Mail\NotifyJingleRejection;
use App\Models\Jingles\Log as InternalLog;
use App\Models\Jingles\Registration;
use App\Models\Jingles\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class JinglesController extends Controller
{
    public $datatablesModel = Registration::class;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('datatables')->only('datatables');
    }

    public function index()
    {
        if (!Auth::user()->can('nb_obras', 'lee')) {
            abort(403);
        }

        $requests = Registration::where('status_id', 1)->get();
        $status = Status::all();

        return view('jingles.index', [
            'requests' => $requests,
            'status'   => $status
        ]);
    }

    public function showView(Registration $registration)
    {
        if (!Auth::user()->can('nb_obras', 'lee')) {
            abort(403);
        }

        return view('jingles.view', ['registration' => $registration]);
    }

    public function datatables(Request $request)
    {
        if (!Auth::user()->can('nb_obras', 'lee')) {
            abort(403);
        }

        $query = $request->datatablesQuery;
        $query->with('status');
        $requests = $query->get();

        $response = response(null);
        $response->datatablesOutput = $requests;
        return $response;
    }

    public function changeStatus(Request $request, Registration $registration)
    {
        if (!Auth::user()->can('nb_obras', 'homologa')) {
            abort(403);
        }

        switch($request->input('status')) {
            case 'beginAction':
                return $this->beginAction($registration);
            break;
            case 'rejectAction':
                return $this->rejectAction($registration);
            break;
            case 'sendToInternal':
                return $this->sendToInternal($registration);
            break;
            case 'approveRequest':
                return $this->approveRequest($registration);
            break;
            case 'rejectRequest':
                return $this->rejectRequest($registration);
            break;
            case 'finishRequest':
                return $this->finishRequest($registration);
            break;
            default:
                abort(403);
        }
    }

    private function beginAction(Registration $registration)
    {
        try {
            if (!Auth::user()->can('nb_obras', 'homologa')) {
                abort(403);
            }

            $errors = [];

            foreach($registration->agreements as $agreement) {
                if ($agreement->type['name'] == 'member') {
                    if (trim($agreement->member->email) != "" && filter_var($agreement->member->email, FILTER_VALIDATE_EMAIL)) {
                        // Si tiene dirección válida, notificamos
                        Mail::to($agreement->member->email)->queue(new NotifyAgreement(optional($agreement->member)->nombre, $registration->id));
                    } else {
                        // Si no, logeamos
                        InternalLog::create([
                            'registration_id' => $registration->id,
                            'agreement_id'    => $agreement->id,
                            'action_id'       => 12, // NOT_NOTIFIED
                            'time'            => now(),
                            'action_data'     => ['member' => $agreement->member_id]
                        ]);

                        // Mail seteado
                        if (trim($agreement->member->email) == "") {
                            $errors[] = optional($agreement->member)->nombre . " no tiene una dirección de correo electrónica configurada";
                        } else {
                            // Mail válido
                            if (!filter_var($agreement->member->email, FILTER_VALIDATE_EMAIL)) {
                                $errors[] = optional($agreement->member)->nombre . " tiene una dirección de correo electrónica errónea: " . $agreement->member->email;
                            }
                        }
                    }
                }
            }

            // Chequeamos si todas las partes aprobaron el trámite
            $finished = $registration->agreements->every(function ($current, $key) {
                return !!$current->response;
            });

            if ($finished) {
                $registration->status_id = 5; // Aprobado Propietarios
            } else {
                $registration->status_id = 2; // En proceso
            }

            $registration->save();

            InternalLog::create([
                'registration_id' => $registration->id,
                'action_id'       => 3, // REQUEST_ACCEPTED
                'time'            => now()
            ]);

            return [
                'status' => 'success',
                'errors' => $errors
            ];
        } catch (Throwable $t) {
            Log::error("Error iniciando trámite de registro de obra",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'   => 'failed',
                'continue' => false
            ];
        }
    }

    private function rejectAction(Registration $registration)
    {
        if (!Auth::user()->can('nb_obras', 'homologa')) {
            abort(403);
        }

        // Cambio estado en la BBDD
        $registration->status_id = 9; // Rechazado
        $registration->save();

        InternalLog::create([
            'registration_id' => $registration->id,
            'action_id'       => 4, // REQUEST_REJECTED
            'time'            => now()
        ]);

        $errors = $this->notifyMembers($registration, NotifyRequestRejection::class);

        return [
            'status' => 'success',
            'errors' => $errors
        ];
    }

    private function sendToInternal(Registration $registration)
    {
        try {
            if (!Auth::user()->can('nb_obras', 'homologa')) {
                abort(403);
            }

            $registration->status_id = 6; // Para pasar a PI
            $registration->save();

            InternalLog::create([
                'registration_id' => $registration->id,
                'action_id'       => 9, // SEND_TO_INTERNAL
                'action_data'     => ['operator_id' => Auth::user()->usuarioid],
                'time'            => now()
            ]);

            $errors = $this->notifyMembers($registration, NotifyRequestSendToInternal::class);

            return [
                'status' => 'success',
                'errors' => $errors
            ];
        } catch (Throwable $t) {
            Log::error("Error enviando trámite de registro de obra al sistema interno",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'   => 'failed'
            ];
        }
    }

    private function approveRequest(Registration $registration)
    {
        try {
            if (!Auth::user()->can('nb_obras', 'homologa')) {
                abort(403);
            }

            $registration->status_id = 8; // Aprobado
            $registration->approved = true;
            $registration->save();

            InternalLog::create([
                'registration_id' => $registration->id,
                'action_id'       => 10, // REQUEST_ACCEPTED
                'action_data'     => ['operator_id' => Auth::user()->usuarioid],
                'time'            => now()
            ]);

            $errors = $this->notifyMembers($registration, NotifyJingleApproval::class);

            return [
                'status' => 'success',
                'errors' => $errors
            ];
        } catch (Throwable $t) {
            Log::error("Error guardando aprobación del trámite",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'   => 'failed'
            ];
        }
    }

    private function rejectRequest(Registration $registration)
    {
        try {
            if (!Auth::user()->can('nb_obras', 'homologa')) {
                abort(403);
            }

            $registration->status_id = 9; // Rechazado
            $registration->approved = false;
            $registration->save();

            InternalLog::create([
                'registration_id' => $registration->id,
                'action_id'       => 11, // REQUEST_REJECTED
                'action_data'     => ['operator_id' => Auth::user()->usuarioid],
                'time'            => now()
            ]);

            $errors = $this->notifyMembers($registration, NotifyJingleRejection::class);

            return [
                'status' => 'success',
                'errors' => $errors
            ];
        } catch (Throwable $t) {
            Log::error("Error guardando rechazo del trámite",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'   => 'failed'
            ];
        }
    }

    private function finishRequest(Registration $registration)
    {
        try {
            if (!Auth::user()->can('nb_obras', 'homologa')) {
                abort(403);
            }

            $registration->status_id = 10; // Finalizado
            $registration->save();

            InternalLog::create([
                'registration_id' => $registration->id,
                'action_id'       => 6, // REQUEST_FINISHED
                'action_data'     => ['operator_id' => Auth::user()->usuarioid],
                'time'            => now()
            ]);

            $errors = $this->notifyMembers($registration, NotifyRequestFinalization::class);

            return [
                'status' => 'success',
                'errors' => $errors
            ];
        } catch (Throwable $t) {
            Log::error("Error guardando finalización del trámite",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'   => 'failed'
            ];
        }
    }

    public function response(Request $request, Registration $registration)
    {
        try {
            if (!Auth::user()->can('nb_obras', 'carga')) {
                abort(403);
            }

            if (!$request->has('response') || !$request->has('agreement_id')) {
                abort(403);
            }

            if ($request->input('response') != 'accept' && $request->input('response') != 'reject') {
                abort(403);
            }

            $agreement_id = $request->input('agreement_id');

            $agreement = $registration->agreements->where('id', $agreement_id)->first();

            // Si el socio no es parte de la distribución del registro
            if (!$agreement) {
                abort(403);
            }

            // Si ya respondió que si, no se puede cambiar
            if ($agreement->response == true) {
                return [
                    'status' => 'failed',
                    'errors' => [
                        'No se puede cambiar la respuesta a una solicitud de registro ya aceptada'
                    ]
                ];
            }

            $agreement->response = $request->input('response') == 'accept';
            $agreement->liable_id = Auth::user()->usuarioid;
            $agreement->save();

            // action_id = 7 -> AGREEMENT_CONFIRMED
            // action_id = 8 -> AGREEMENT_REJECTED
            InternalLog::create([
                'registration_id' => $registration->id,
                'agreement_id'    => $agreement->id,
                'action_id'       => $request->input('response') == 'accept' ? 7 : 8,
                'action_data'     => ['operator_id' => Auth::user()->usuarioid],
                'time'            => now()
            ]);

            // Chequeamos si todas las partes aprobaron el trámite
            $finished = $registration->agreements->every(function ($current, $key) {
                return !!$current->response;
            });

            // Si el trámite está terminado...
            if ($finished) {
                $registration->status_id = 5; // Aprobado Propietarios
            // Si la respuesta fue negativa
            } elseif (!$agreement->response) {
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

    public function saveObservations(Request $request, Registration $registration)
    {
        try {
            if (!Auth::user()->can('nb_obras', 'carga')) {
                abort(403);
            }

            $registration->observations = $request->input('content', null) ?? '';
            $registration->save();

            return [
                'status'   => 'success'
            ];
        } catch (Throwable $t) {
            Log::error("Error guardando finalización del trámite",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'   => 'failed'
            ];
        }
    }

    private function notifyMembers(Registration $registration, string $mail)
    {
        $errors = [];

        foreach($registration->agreements as $agreement) {
            if ($agreement->type['name'] == 'member') {
                // Si el trámite lo inició un socio y la distribución lo refiere, se acepta directamente
                if ($registration->member_id && $registration->initiator->member_id == $agreement->member_id) {
                    $agreement->response = 1;
                    $agreement->liable_id = null;
                    $agreement->save();

                    InternalLog::create([
                        'registration_id' => $registration->id,
                        'agreement_id'    => $agreement->id,
                        'action_id'       => 7, // AGREEMENT_CONFIRMED
                        'time'            => now()
                    ]);

                    continue;
                }

                if (trim($agreement->member->email) != "" && filter_var($agreement->member->email, FILTER_VALIDATE_EMAIL)) {
                    // Si tiene dirección válida, notificamos
                    Mail::to($agreement->member->email)->queue(new $mail(optional($agreement->member)->nombre, $registration->id));
                } else {
                    // Si no, logeamos
                    InternalLog::create([
                        'registration_id' => $registration->id,
                        'agreement_id'    => $agreement->id,
                        'action_id'       => 12, // NOT_NOTIFIED
                        'time'            => now(),
                        'action_data'     => ['member' => $agreement->member_id]
                    ]);

                    // Mail seteado
                    if (trim($agreement->member->email) == "") {
                        $errors[] = optional($agreement->member)->nombre . " no tiene una dirección de correo electrónica configurada";
                    } else {
                        // Mail válido
                        if (!filter_var($agreement->member->email, FILTER_VALIDATE_EMAIL)) {
                            $errors[] = optional($agreement->member)->nombre . " tiene una dirección de correo electrónica errónea: " . $agreement->member->email;
                        }
                    }
                }
            }
        }

        if (trim($registration->initiator->email) == "") {
            $errors[] = 'No se pudo notificar al iniciador del trámite porque no tiene configurada dirección de correo electrónico';
        } else {
            if (!filter_var($registration->initiator->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'No se pudo notificar al iniciador del trámite porque tiene una dirección de correo electrónica errónea: ' . $registration->initiator->email;
            } else {
                if ($registration->initiator->member_id) {
                    Mail::to($registration->initiator->email)->queue(new $mail($registration->initiator->full_name, $registration->id));
                } else {
                    Mail::to($registration->initiator->email)->queue(new $mail($registration->initiator->name, $registration->id));
                }
            }
        }

        return $errors;
    }
}
