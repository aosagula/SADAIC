<?php

namespace App\Http\Controllers;

use App\Models\Jingles\Agreement;
use App\Models\Jingles\Log as InternalLog;
use App\Models\Jingles\Media;
use App\Models\Jingles\Meta;
use App\Models\Jingles\Part;
use App\Models\Jingles\Person;
use App\Models\Jingles\Registration;
use App\Models\SADAIC\Cities;
use App\Models\SADAIC\Countries;
use App\Models\SADAIC\States;
use App\Models\SADAIC\Types;
use App\Http\Requests\JingleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JinglesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,members');
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requests = null;

        // Socios
        if (Auth::user()->type == 'member') {
            // Si el socio está entre los registros de SADAIC
            // también buscamos si es parte de una solicitud
            if (Auth::user()->sadaic) {
                $requests = Registration::where('member_id', Auth::user()->id)
                ->orWhere(function($query) {
                    $query->whereHas('agreements', function ($query) {
                        $query->where('member_idx', Auth::user()->sadaic->idx);
                    })
                    ->whereNotNull('status_id');
                })
                ->orderBy('id', 'desc')
                ->get();
            // Si el socio no está entre los registros de SADAIC
            // solo devolvemos las solicitudes que inició
            } else {
                $requests = Registration::where('member_id', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->get();
            }
        // Usuarios
        } elseif (Auth::user()->type == 'user') {
            $requests = Registration::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();
        }

        return view('jingles.index', [
            'requests'  => $requests,
            'user_type' => Auth::user()->type
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = Cities::orderBy('city')->get();
        $countries = Countries::select(['idx', 'name_ter'])->orderBy('name_ter')->get();
        $states = States::orderBy('state')->get();
        $types = Types::all();
        $media = Media::all();
        $broadcast_territories = Registration::BROADCAST_TERRITORY;
        $tariff_payers = Registration::TARIFF_PAYER;

        $registration = new Registration();
        $registration->ads_duration = [0];

        return view('jingles.create', [
            'registration'          => $registration,
            'user_type'             => Auth::user()->type,
            'cities'                => $cities,
            'countries'             => $countries,
            'media'                 => $media,
            'states'                => $states,
            'types'                 => $types,
            'broadcast_territories' => $broadcast_territories,
            'tariff_payers'         => $tariff_payers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JingleRequest $request)
    {
        $params = $this->getParams($request);

        $params[Auth::user()->type . '_id'] = Auth::user()->id;

        // Crear solicitud de inclusión
        $registration = Registration::create($params);

        DB::transaction(function () use ($request, $params, $registration) {
            $parts = $request->only([ 'applicant', 'advertiser', 'agency' ]);

            // Si el aplicante es un socio, cargamos automáticamente sus datos
            if (Auth::user()->type == 'member' && Auth::user()->sadaic) {
                $parts['applicant']['address'] = Auth::user()->sadaic->full_address;
                $parts['applicant']['cuit'] = Auth::user()->sadaic->num_doc;
                $parts['applicant']['editable'] = '0';
                $parts['applicant']['email'] = Auth::user()->sadaic->email;
                $parts['applicant']['name'] = Auth::user()->sadaic->nombre;
                $parts['applicant']['phone'] = '';
            }

            foreach($parts as $type => $personParams) {
                // Crear persona
                $person = Person::create($personParams);

                // Crear relación
                Part::create([
                    'registration_id' => $registration->id,
                    'person_id'       => $person->id,
                    'type'            => $type
                ]);
            }

            $people = $request->input('people');
            foreach($people as $agreementParams) {
                $agreement = Agreement::create([
                    'registration_id' => $registration->id,
                    'type_id'         => $agreementParams['type'] == 'member' ? 1 : 2,
                    'member_idx'      => $agreementParams['type'] == 'member' ? $agreementParams['idx'] : null,
                    'doc_number'      => $agreementParams['doc_number']
                ]);

                if ($agreementParams['type'] == 'no-member') {
                    $agreementParams['agreement_id'] = $agreement->id;
                    $meta = Meta::create($agreementParams);
                }
            }

            InternalLog::create([
                'registration_id' => $registration->id,
                'action_id'       => 1, // REQUEST_CREATED
                'time'            => now()
            ]);
        });

        return $registration;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Jingles\Registration  $jingle
     * @return \Illuminate\Http\Response
     */
    public function show(Registration $jingle)
    {
        return view('jingles.view', [
            'registration'  => $jingle
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Jingles\Registration  $jingle
     * @return \Illuminate\Http\Response
     */
    public function edit(Registration $jingle)
    {
        $cities = Cities::orderBy('city')->get();
        $countries = Countries::select(['idx', 'name_ter'])->orderBy('name_ter')->get();
        $states = States::orderBy('state')->get();
        $types = Types::all();
        $media = Media::all();
        $broadcast_territories = Registration::BROADCAST_TERRITORY;
        $tariff_payers = Registration::TARIFF_PAYER;

        return view('jingles.edit', [
            'user_type'             => Auth::user()->type,
            'registration'          => $jingle,
            'cities'                => $cities,
            'countries'             => $countries,
            'media'                 => $media,
            'states'                => $states,
            'types'                 => $types,
            'broadcast_territories' => $broadcast_territories,
            'tariff_payers'         => $tariff_payers
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jingles\Registration  $jingle
     * @return \Illuminate\Http\Response
     */
    public function update(JingleRequest $request, Registration $jingle)
    {
        $params = $this->getParams($request);

        $jingle->update($params);

        if (Auth::user()->type != 'member' || !Auth::user()->sadaic) {
            $jingle->applicant->update($request->input('applicant'));
            $jingle->applicant->save();
        }

        $jingle->advertiser->update($request->input('advertiser'));
        $jingle->advertiser->save();

        $jingle->agency->update($request->input('agency'));
        $jingle->agency->save();

        $oldAgreements = $jingle->agreements->map(function($item) {
            return $item->id;
        })->toArray();

        $people = $request->input('people');
        foreach($people as $agreementParams) {
            $agreement = Agreement::updateOrCreate([
                'registration_id' => $jingle->id,
                'member_idx'      => $agreementParams['type'] == 'member' ? $agreementParams['idx'] : null,
                'type_id'         => $agreementParams['type'] == 'member' ? 1 : 2,
                'doc_number'      => $agreementParams['doc_number']
            ]);

            // Si el acuerdo está entre los datos enviados, quitamos el id del listado
            if (($key = array_search($agreement->id, $oldAgreements)) !== false) {
                unset($oldAgreements[$key]);
            }

            if ($agreementParams['type'] == 'no-member') {
                $agreementParams['agreement_id'] = $agreement->id;

                // Fix temporal hasta que se actualice la tabla de metadatos de registro de obra
                $agreementParams['doc_type_id'] = $agreementParams['doc_type'];
                unset($agreementParams['type']);
                unset($agreementParams['doc_type']);
                unset($agreementParams['doc_number']);

                $meta = Meta::updateOrCreate($agreementParams);
            }
        }

        // Eliminamos todos lps acierdps que no hayan venido en la nueva distribución
        // pero todavía están en la BBDD
        foreach($oldAgreements as $remainId) {
            $agreement = Agreement::find($remainId);
            $agreement->delete();
        }

        $jingle->push();

        InternalLog::create([
            'registration_id' => $jingle->id,
            'action_id'       => 2, // REQUEST_UPDATED
            'time'            => now()
        ]);

        return $jingle;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Jingles\Registration  $jingle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Registration $jingle)
    {
        //
    }

    private function getParams(Request $request)
    {
        $params = $request->only([
            'ads_duration',
            'agency_type_id',
            'air_date',
            'also_national',
            'authors_agreement',
            'authors_tariff',
            'broadcast_territory_id',
            'is_special',
            'media_id',
            'product_brand',
            'product_name',
            'product_type',
            'request_action_id',
            'subsection_i',
            'tariff_payer_id',
            'tariff_representation',
            'territory_id',
            'validity',
            'work_authors',
            'work_composers',
            'work_dnda',
            'work_editors',
            'work_music_mod',
            'work_original',
            'work_script_mod',
            'work_title',
            'status_id'
        ]);

        // Exportación
        if ($params['request_action_id'] == '4') {
            $params['broadcast_territory_id'] = 3; // Extranjero
        } else {
            $params['also_national'] = null; // Únicamente para Exportación
        }

        if ($request->has('status_id') && $params['status_id'] != '1') {
            unset($params['status_id']);
        }

        // Si el territory_id no viene como array (Extranjero), lo convertimos
        if ($request->has('territory_id') && !is_array($params['territory_id'])) {
            $params['territory_id'] = [$params['territory_id']];
        }

        // Territorio de difusión nacional
        if ($params['broadcast_territory_id'] == '1') {
            $params['territory_id'] = null; // Únicamente Provincial y Exportación
        }

        return $params;
    }

    public function showResponse(Registration $registration)
    {
        // Si el status no es el correcto
        if ($registration->status_id != 2 && $registration->status_id != 3) {
            return redirect('/member/jingles');
        }

        // Si no es una de las partes
        if (!$registration->agreements->contains('member_idx', Auth::user()->sadaic->idx)) {
            abort(403);
        }

        return view('jingles.response', [
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

            $agreement = $registration->agreements->where('member_idx', Auth::user()->sadaic->idx)->first();
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

            DB::transaction(function () use($agreement, $registration, $request) {
                $agreement->response = $request->input('response') == 'accept';
                $agreement->liable_id = null;
                $agreement->save();

                $registration->updated_at = now();
                $registration->save();

                // action_id = 7 -> AGREEMENT_CONFIRMED
                // action_id = 8 -> AGREEMENT_REJECTED
                InternalLog::create([
                    'registration_id' => $registration->id,
                    'agreement_id'    => $agreement->id,
                    'action_id'       => $request->input('response') == 'accept' ? 7 : 8,
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

            });

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
}
