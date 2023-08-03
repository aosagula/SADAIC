<?php

namespace App\Http\Traits;

use App\Models\Work\Registration;
use App\Models\Work\Distribution;
use App\Models\Work\Meta;
use App\Models\Work\Title;
use Illuminate\Support\Facades\Auth;

trait WorkDraftTrait {
    public function saveDraft($data)
    {
        $registration = null;
        if (data_get($data, 'registration_id', false)) {
            $registration = Registration::find(data_get($data, 'registration_id'));

            // Únicamente puede editar las solicitudes que inició
            if ($registration->{Auth::user()->type . '_id'} != Auth::user()->id) {
                abort(403);
            }

            if (data_get($data, 'title', false))
                $registration->title = data_get($data, 'title');
            if (data_get($data, 'dnda_title', false))
                $registration->dnda_title = data_get($data, 'dnda_title');
            if (data_get($data, 'duration', false))
                $registration->duration = data_get($data, 'duration');
            if (data_get($data, 'dnda_ed_date', false))
                $registration->dnda_ed_date = data_get($data, 'dnda_ed_date');
            if (data_get($data, 'audio_dnda_ed_file', false))
                $registration->audio_dnda_ed_file = data_get($data, 'audio_dnda_ed_file');
            if (data_get($data, 'lyric_dnda_ed_file', false))
                $registration->lyric_dnda_ed_file = data_get($data, 'lyric_dnda_ed_file');
            if (data_get($data, 'dnda_in_date', false))
                $registration->dnda_in_date = data_get($data, 'dnda_in_date');
            if (data_get($data, 'audio_dnda_in_file', false))
                $registration->audio_dnda_in_file = data_get($data, 'audio_dnda_in_file');
            if (data_get($data, 'lyric_dnda_in_file', false))
                $registration->lyric_dnda_in_file = data_get($data, 'lyric_dnda_in_file');
            if (data_get($data, 'genre_id', false))
                $registration->genre_id = data_get($data, 'genre_id');

            // Los checkbox no se envían cuando no está seleccionados por lo que
            // por defecto actualizamos los valores a falso
            $registration->is_jingle = false;
            $registration->is_movie = false;
            $registration->is_regular = false;

            if (data_get($data, 'is_jingle') == 1) {
                $registration->is_jingle = true;
            }

            if (data_get($data, 'is_jingle') == 2) {
                $registration->is_movie = true;
            }

            if (data_get($data, 'is_jingle') == 3) {
                $registration->is_regular = true;
            }

            $registration->save();
        } else {
            $registrationParams[Auth::user()->type . '_id'] = Auth::user()->id;
            $registrationParams['status_id'] = null;
            $registrationParams['title'] = data_get($data, 'title', null) ?? '';
            $registrationParams['dnda_title'] = data_get($data, 'dnda_title', null) ?? '';
            $registrationParams['duration'] = data_get($data, 'duration', null) ?? '0:00';

            $registrationParams['dnda_ed_date'] = data_get($data, 'dnda_ed_date', null);
            $registrationParams['audio_dnda_ed_file'] = data_get($data, 'audio_dnda_ed_file', null);
            $registrationParams['lyric_dnda_ed_file'] = data_get($data, 'lyric_dnda_ed_file', null);

            $registrationParams['dnda_in_date'] = data_get($data, 'dnda_in_date', null);
            $registrationParams['audio_dnda_in_file'] = data_get($data, 'audio_dnda_in_file', null);
            $registrationParams['lyric_dnda_in_file'] = data_get($data, 'lyric_dnda_in_file', null);

            $registrationParams['genre_id'] = data_get($data, 'genre_id', null);

            $registrationParams['is_jingle'] = false;
            $registrationParams['is_movie'] = false;
            $registrationParams['is_regular'] = false;

            if (data_get($data, 'is_jingle') == 1) {
                $registrationParams['is_jingle'] = true;
            }

            if (data_get($data, 'is_jingle') == 2) {
                $registrationParams['is_movie'] = true;
            }

            if (data_get($data, 'is_jingle') == 3) {
                $registrationParams['is_regular'] = true;
            }

            $registrationParams['rejection_reason'] = data_get($data, 'rejection_reason', null) ?? '';

            $registrationParams['updated_at'] = now();

            $registration = Registration::create($registrationParams);
        }

        if (isset($data['alternative_titles'])) {
            // Borramos los títulos actuales
            Title::where('registration_id', $registration->id)->delete();

            // Cargamos los nuevos
            foreach(data_get($data, 'alternative_titles') as $title) {
                Title::create([
                    'registration_id' => $registration->id,
                    'title'           => $title
                ]);
            }
        }

        $people = [];
        $oldDistribution = [];

        if (data_get($data, 'person', false)) {
            $people = [ json_decode(data_get($data, 'person')) ];
        } elseif (data_get($data, 'people', false)) {
            $people = data_get($data, 'people');

            // Como viene una nueva distribución completa, guardamos la información del la original
            // para detectar distribuciones que no se hayan eliminado correctamente
            $oldDistribution = $registration->distribution->map(function($item) {
                return $item->id;
            })->toArray();
        }

        // Distribución
        foreach($people as $person)  {
            $distribution = null;
            if (data_get($person, 'distribution_id', false)) {
                $distribution = Distribution::find(data_get($person, 'distribution_id'));

                // Si la distribución está entre los datos enviados, quitamos el id del listado
                if (($key = array_search($distribution->id, $oldDistribution)) !== false) {
                    unset($oldDistribution[$key]);
                }

                if (data_get($person, 'public', false))
                    $distribution->public = data_get($person, 'public');
                if (data_get($person, 'mechanic', false))
                    $distribution->mechanic = data_get($person, 'mechanic');
                if (data_get($person, 'sync', false))
                    $distribution->sync = data_get($person, 'sync');
                if (data_get($person, 'doc_number', false))
                    $distribution->doc_number = data_get($person, 'doc_number');
                if (data_get($person, 'member_id', false))
                    $distribution->member_id = data_get($person, 'member_id');

                $distribution->fn = data_get($person, 'fn', null) ?? -1;

                $distribution->save();
            } else {
                $peopleIdParams['registration_id'] = $registration->id;
                $peopleIdParams['doc_number'] = data_get($person, 'doc_number', null) ?? '';
                $peopleIdParams['member_id'] = data_get($person, 'member_id', null) ?? '';

                $peopleParams['type'] = data_get($person, 'member_id', false) ? 'member' : 'no-member';
                $peopleParams['fn'] = data_get($person, 'fn', null) ?? -1;
                $peopleParams['public'] = data_get($person, 'public', 0);
                $peopleParams['mechanic'] = data_get($person, 'mechanic', 0);
                $peopleParams['sync'] = data_get($person, 'sync', 0);

                $distribution = Distribution::updateOrCreate($peopleIdParams, $peopleParams);
            }

            // Meta datos no socios
            if (data_get($person, 'type') == 'no-member') {
                if (data_get($person, 'distribution_id', false)) {
                    $meta = Meta::where('distribution_id', data_get($person, 'distribution_id'))->first();

                    if (data_get($person, 'distribution_id', false))
                        $meta->distribution_id = data_get($person, 'distribution_id');
                    if (data_get($person, 'address_country_id', false))
                        $meta->address_country_id = data_get($person, 'address_country_id');
                    if (data_get($person, 'address_state_id', false))
                        $meta->address_state_id = data_get($person, 'address_state_id');
                    if (data_get($person, 'address_state_text', false))
                        $meta->address_state_text = data_get($person, 'address_state_text');
                    if (data_get($person, 'address_city_id', false))
                        $meta->address_city_id = data_get($person, 'address_city_id');
                    if (data_get($person, 'address_city_text', false))
                        $meta->address_city_text = data_get($person, 'address_city_text');
                    if (data_get($person, 'address_zip', false))
                        $meta->address_zip = data_get($person, 'address_zip');
                    if (data_get($person, 'apartment', false))
                        $meta->apartment = data_get($person, 'apartment');
                    if (data_get($person, 'birth_country_id', false))
                        $meta->birth_country_id = data_get($person, 'birth_country_id');
                    if (data_get($person, 'birth_date', false))
                        $meta->birth_date = data_get($person, 'birth_date');
                    if (data_get($person, 'doc_type', false))
                        $meta->doc_type = data_get($person, 'doc_type');
                    if (data_get($person, 'email', false))
                        $meta->email = data_get($person, 'email');
                    if (data_get($person, 'floor', false))
                        $meta->floor = data_get($person, 'floor');
                    if (data_get($person, 'name', false))
                        $meta->name = data_get($person, 'name');
                    if (data_get($person, 'phone_area', false))
                        $meta->phone_area = data_get($person, 'phone_area');
                    if (data_get($person, 'phone_country', false))
                        $meta->phone_country = data_get($person, 'phone_country');
                    if (data_get($person, 'phone_number', false))
                        $meta->phone_number = data_get($person, 'phone_number');
                    if (data_get($person, 'street_name', false))
                        $meta->street_name = data_get($person, 'street_name');
                    if (data_get($person, 'street_number', false))
                        $meta->street_number = data_get($person, 'street_number');

                    $meta->save();
                } else {
                    $metaIdParams['distribution_id'] = $distribution->id;

                    $metaParams['address_country_id'] = data_get($person, 'address_country_id', null);
                    $metaParams['address_state_id'] = data_get($person, 'address_state_id', null);
                    $metaParams['address_state_text'] = data_get($person, 'address_state_text', null);
                    $metaParams['address_city_id'] = data_get($person, 'address_city_id', null);
                    $metaParams['address_city_text'] = data_get($person, 'address_city_text', null);
                    $metaParams['address_zip'] = data_get($person, 'address_zip', null) ?? '';
                    $metaParams['apartment'] = data_get($person, 'apartment', null) ?? '';
                    $metaParams['birth_country_id'] = data_get($person, 'birth_country_id', null);
                    $metaParams['birth_date'] = data_get($person, 'birth_date', null);
                    $metaParams['doc_type'] = data_get($person, 'doc_type', null);
                    $metaParams['email'] = data_get($person, 'email', null) ?? '';
                    $metaParams['floor'] = data_get($person, 'floor', null) ?? '';
                    $metaParams['name'] = data_get($person, 'name', null) ?? '';
                    $metaParams['phone_area'] = data_get($person, 'phone_area', null) ?? '';
                    $metaParams['phone_country'] = data_get($person, 'phone_country', null) ?? '';
                    $metaParams['phone_number'] = data_get($person, 'phone_number', null) ?? '';
                    $metaParams['street_name'] = data_get($person, 'street_name', null) ?? '';
                    $metaParams['street_number'] = data_get($person, 'street_number', null) ?? '';

                    Meta::updateOrCreate($metaIdParams, $metaParams);
                }
            }
        }

        // Eliminamos todas las distribuciones que no hayan venido en la nueva distribución
        // pero todavía están en la BBDD
        foreach($oldDistribution as $remainId) {
            $distribution = Distribution::find($remainId);
            if ($distribution->type == 'no-member') {
                $distribution->meta->delete();
            }
            $distribution->delete();
        }

        return $registration->refresh();
    }
}
