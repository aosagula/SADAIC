<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'               => 'string|max:255|required',
            'birth_date'         => 'date|required',
            'birth_city_text'    => 'string|max:50|nullable',
            'birth_city_id'      => 'integer',
            'birth_state_text'   => 'string|max:50|nullable',
            'birth_state_id'     => 'integer',
            'birth_country_id'   => 'string|max:15|required',
            'doc_number'         => 'string|max:50|required',
            'doc_country'        => 'string|max:50|required',
            'work_code'          => 'string|max:20|required',
            'address_street'     => 'string|max:255|required',
            'address_number'     => 'string|max:20|required',
            'address_floor'      => 'string|max:10|nullable',
            'address_apt'        => 'string|max:10|nullable',
            'address_zip'        => 'string|max:10|required',
            'address_city_text'  => 'string|max:50|nullable',
            'address_city_id'    => 'integer',
            'address_state_text' => 'string|max:50|nullable',
            'address_state_id'   => 'integer',
            'address_country_id' => 'string|max:15|required',
            'landline'           => 'string|max:15|nullable',
            'mobile'             => 'string|max:15|required',
            'email'              => 'email|required',
            'pseudonym'          => 'string|max:255|required',
            'band'               => 'string|max:255',
            'entrance_work'      => 'string|max:255|required',
            'genre_id'           => 'integer|required'
        ];
    }

    public function attributes()
    {
        return [
            'name'                => 'nombre',
            'birth_date'          => 'fecha de nacimiento',
            'birth_city_id'       => 'localidad',
            'birth_city_text'     => 'localidad',
            'birth_state_id'      => 'provincia',
            'birth_state_text'    => 'provincia',
            'birth_country_id'    => 'país',
            'doc_number'          => 'número de documento o pasaporte',
            'doc_country'         => 'nacionalidad',
            'work_code'           => 'cuit / cuil',
            'address_street'      => 'calle',
            'address_number'      => 'número',
            'address_floor'       => 'piso',
            'address_apt'         => 'deptartamento',
            'address_city_id'     => 'localidad',
            'address_city_text'   => 'localidad',
            'address_zip'         => 'código postal',
            'address_state_id'    => 'provincia',
            'address_state_state' => 'provincia',
            'landline'            => 'teléfono',
            'mobile'              => 'celular',
            'email'               => 'correo electrónico',
            'pseudonym'           => 'seudónimo',
            'band'                => 'grupo / banda',
            'entrance_work'       => 'trabajo de entrada',
            'genre_id'            => 'género'
        ];
    }
/////Agregado 24102022
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
            'advertiser.cuit.required_if' => 'La solicitud tiene que incluir los datos del anunciante',
            'agency.cuit.required_if'     => 'La solicitud tiene que incluir los datos de la agencia / productora',
            'applicant.cuit.required'     => 'La solicitud tiene que incluir los datos del solicitante'
        ];
    }    
}
