<?php

namespace App\Providers;

use App\Models\SADAIC\Socio;
use App\Models\SADAIC\Member as SADAICMember;
use App\Models\Member;
use App\Models\MemberProfile;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MemberProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $identifier = explode('-', $identifier);
        if (count($identifier) !== 2) {
            return;
        }

        return $this->retrieveByCredentials([
            'member_id' => $identifier[0],
            'heir' => $identifier[1],
        ]);
    }

    public function retrieveByCredentials(array $fields)
    {
        if (empty($fields)) {
            return;
        }

        // Consultamos sobre la BBDD vieja
        $query = Socio::query();

        if (array_key_exists('member_id', $fields)) {
            $query->where('socio', $fields['member_id']);
        }

        if (array_key_exists('heir', $fields)) {
            $query->where('heredero', $fields['heir']);
        }

        if (array_key_exists('email', $fields)) {
            $query->where('email', $fields['email']);
        }

        if (array_key_exists('status', $fields)) {
            $query->where('status', $fields['status']);
        }

        $socio = $query->first();
        if (!$socio) {
            return;
        }

        $member = Member::firstOrCreate(
            [
                'member_id' => $socio->socio,
                'heir' => $socio->heredero
            ],
            [
                'email' => $socio->email
            ]
        );

        $memberParams = [];

        $query = $socio->socio;
        if ($socio->heredero != 0) {
            $query .= '/' . str_pad($socio->heredero, 2, '0', STR_PAD_LEFT);
        }

        $datosSADAIC = SADAICMember::where('codanita', $query)->first();
        if ($datosSADAIC) {
            $nombre = data_get($datosSADAIC, 'nombre', '');
            if ($nombre == '') {
                $nombre = Arr::get($socio, 'apellidoNombre', '');
            }
            $nombre = ucwords(strtolower($nombre));
            $memberParams['name'] = $nombre;
        } else {
            $memberParams['name'] = data_get($socio, 'apellidoNombre', '');
        }
        
        $memberParams['address_type'] = data_get($socio, 'tipo_direccion', '');
        $memberParams['address'] = data_get($socio, 'direccion', '');
        $memberParams['address_zip'] = data_get($socio, 'cp', '');
        $memberParams['address_city'] = data_get($socio, 'localidad', '');
        $memberParams['address_state'] = data_get($socio, 'provincia', '');
        $memberParams['address_country'] = data_get($socio, 'pais', '');
        $memberParams['phone_country'] = intval($socio->tel_pais);
        $memberParams['phone_area'] = intval($socio->tel_cod_area);
        $memberParams['phone_number'] = intval($socio->tel);
        $memberParams['cell_country'] = intval($socio->celular_pais);
        $memberParams['cell_area'] = intval($socio->celular_cod_area);
        $memberParams['cell_number'] = intval($socio->celular);

        MemberProfile::firstOrCreate(
            ['member_id' => $member->id],
            $memberParams
        );

        return $member;
    }

    public function validateCredentials(Authenticatable $member, array $credentials)
    {
        // Recibimos un objeto del tipo Member pero lo validamos contra
        // Socio
        $socio = Socio::where('socio', $member->member_id)
            ->where('heredero', $member->heir)
            ->whereBetween('status', [0, 3])
            ->first(['socio', 'heredero', 'clave']);

        if ($socio->socio != $credentials['member_id']) {
            return false;
        }

        if ($socio->heredero != $credentials['heir']) {
            return false;
        }

        $passwordHash = hash('sha512', $credentials['password'] . env('SADAIC_HASH'));

        if ($passwordHash != $socio->clave) {
            return false;
        }

        return true;
    }
}