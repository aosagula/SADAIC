<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JingleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_special'             => 'boolean|required',
            'request_action_id'      => 'integer|required',
            'validity'               => 'integer|required',
            'air_date'               => 'date|nullable|required_if:status_id,1',

            'ads_duration'           => 'array|min:1|required_if:status_id,1',
            'ads_duration.*'         => 'integer|nullable|required_if:status_id,1',

            'broadcast_territory_id' => 'integer|required',
            'territory_id'           => 'nullable',
            'also_national'          => 'boolean|nullable',

            'media_id'               => 'integer|required',
            'agency_type_id'         => 'integer|required',

            'subsection_i'           => 'boolean|nullable',

            'product_brand'          => 'string|nullable|required_if:status_id,1',
            'product_type'           => 'string|nullable|required_if:status_id,1',
            'product_name'           => 'string|nullable|required_if:status_id,1',

            'work_title'             => 'string|nullable|required_if:status_id,1',
            'work_original'          => 'boolean|required',
            'work_dnda'              => 'string|nullable',
            'work_authors'           => 'string|required',
            'work_composers'         => 'string|nullable',
            'work_editors'           => 'string|nullable',
            'work_music_mod'         => 'boolean|required',
            'work_script_mod'        => 'boolean|required',

            'authors_agreement'      => 'boolean|required',
            'authors_tariff'         => 'numeric|required',

            'tariff_payer_id'        => 'integer|required',
            'tariff_representation'  => 'string|nullable',

            'advertiser.cuit'        => 'string|nullable|required_if:status_id,1',
            'applicant.cuit'         => [
                'string',
                'nullable',
                Rule::requiredIf(function () {
                    return Auth::user()->type !== 'member' || (!Auth::user()->sadaic && request()->input('status_id') === 1);
                })
            ],
        ];
    }

    public function attributes()
    {
        return [
            'is_special'             => 'tipo de solicitud',
            'request_action_id'      => 'tipo de solicitud',
            'validity'               => 'vigencia',
            'air_date'               => 'fecha de salida al aire',
            'ads_duration'           => 'duración del aviso',
            'broadcast_territory_id' => 'territorio de difusión',
            'territory_id'           => 'territorio de difusión',
            'media_id'               => 'medios de comunicación',
            'agency_type_id'         => 'agencia o productora',
            'subsection_i'           => 'inciso i',
            'product_brand'          => 'marca del producto',
            'product_type'           => 'característica y tipo del producto',
            'product_name'           => 'identicación y nombre del producto',
            'work_title'             => 'título de la obra',
            'work_original'          => 'originalidad de la obra',
            'work_dnda'              => 'expediente DNDA',
            'work_authors'           => 'autores de la obra',
            'work_composers'         => 'compositores de la obra',
            'work_editors'           => 'editores de la obra',
            'work_music_mod'         => 'modificación de la música de la obra',
            'work_script_mod'        => 'modificación de la letra de la obra',

            'authors_tariff'         => 'arancel',

            'tariff_payer_id'        => 'la persona que abona el derecho de autor',
            'tariff_representation'  => 'a cuenta de quién'
        ];
    }

    public function messages()
    {
        return [
            'air_date.required_if'        => 'La solicitud tiene que incluir la fecha de incio de emisión del aviso',
            'ads_duration.required_if'    => 'La solicitud tiene que incluir la duración de los avisos',
            'ads_duration.*.required_if'  => 'La solicitud tiene que incluir la duración de los avisos',
            'product_brand.required_if'   => 'La solicitud tiene que incluir la marca del producto',
            'product_type.required_if'    => 'La solicitud tiene que incluir el tipo del producto',
            'product_name.required_if'    => 'La solicitud tiene que incluir el nombre del producto',
            'work_title.required_if'      => 'La solicitud tiene que incluir el título de la obra a incluir',
            'work_authors.required'      => 'La solicitud tiene que incluir un autor',
            'advertiser.cuit.required_if' => 'La solicitud tiene que incluir los datos del anunciante',
            'agency.cuit.required_if'     => 'La solicitud tiene que incluir los datos de la agencia / productora',
            'applicant.cuit.required'     => 'La solicitud tiene que incluir los datos del solicitante',
            'authors_tariff.required'     => 'La solicitud tiene que incluir un arancel'
            
        ];
    }
}
