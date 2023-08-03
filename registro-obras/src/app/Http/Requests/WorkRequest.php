<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class WorkRequest extends FormRequest
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

    public function rules()
    {
        return [
            'title'               => 'required',
            'dnda_title'          => 'required',
            'genre_id'            => 'integer|required',
            'duration'            => 'string|required',

            'dnda_ed_date'        => 'date|nullable',
            'audio_dnda_ed_file'  => 'string|nullable',
            'lyric_dnda_ed_file'  => 'string|nullable',

            'dnda_in_date'        => 'date|nullable',
            'audio_dnda_in_file'  => 'string|nullable',
            'lyric_dnda_in_file'  => 'string|nullable',

            'people'                      => 'array|min:1',
            'people.*.type'               => 'string|required',
            'people.*.fn'                 => 'string|nullable|required',
            'people.*.member_id'          => 'string|nullable',
            'people.*.doc_number'         => 'string|nullable|required_without:people.*.member_id',
            'people.*.public'             => 'numeric|required|between:0,100',
            'people.*.mechanic'           => 'numeric|required|between:0,100',
            'people.*.sync'               => 'numeric|required|between:0,100',
            'people.*.address_city_text'  => 'string|max:50|nullable',
            'people.*.address_city_id'    => 'integer|nullable',
            'people.*.address_state_text' => 'string|max:50|nullable',
            'people.*.address_state_id'   => 'integer|nullable',
            'people.*.address_country_id' => 'string|max:15|nullable',
            'people.*.address_zip'        => 'string|nullable|required_if:people.*.type,no-member',
            'people.*.apartment'          => 'string|nullable',
            'people.*.birth_country_id'   => 'string|required_if:people.*.type,no-member',
            'people.*.birth_date'         => 'date|required_if:people.*.type,no-member',
            'people.*.doc_type'           => 'string|required_if:people.*.type,no-member',
            'people.*.email'              => 'email|required_if:people.*.type,no-member',
            'people.*.floor'              => 'string|nullable',
            'people.*.name'               => 'string|nullable|required_if:people.*.type,no-member',
            'people.*.phone_area'         => 'string|nullable|required_if:people.*.type,no-member',
            'people.*.phone_country'      => 'string|nullable|required_if:people.*.type,no-member',
            'people.*.phone_number'       => 'string|nullable|required_if:people.*.type,no-member',
            'people.*.street_name'        => 'string|nullable|required_if:people.*.type,no-member',
            'people.*.street_number'      => 'string|nullable|required_if:people.*.type,no-member'
        ];
    }

    public function attributes()
    {
        return [
            'duration'                  => 'duración',
            'title'                     => 'título',
            'genre_id'                  => 'género',
            'people.*.address_zip'      => 'código postal',
            'people.*.birth_country_id' => 'nacionalidad',
            'people.*.birth_date'       => 'fecha de nacimiento',
            'people.*.doc_type'         => 'tipo de documento',
            'people.*.email'            => 'correo electrónico',
            'people.*.name'             => 'nombre y apellido',
            'people.*.phone_area'       => 'teléfono',
            'people.*.phone_country'    => 'teléfono',
            'people.*.phone_number'     => 'teléfono',
            'people.*.street_name'      => 'dirección',
            'people.*.street_number'    => 'dirección',
            'people.*.public'           => 'ejecución pública',
            'people.*.mechanic'         => 'reproducción mecánica',
            'people.*.sync'             => 'sincronización',
            'people.*.fn'               => 'rol'
        ];
    }

    public function messages()
    {
        return [
            'people.*.name.required_if'             => 'Todos los derechohabientes que no sean socios tienen que tener indicado el Apellido y Nombre',
            'title.required'                        => 'El campo Título es obligatorio',
            'dnda_title.required'                   => 'El campo Título Álbum es obligatorio',
            'people.min'                            => 'La solicitud tiene que incluir al menos un derechohabiente',
            'people.*.fn.required'                  => 'Todos los derechohabientes tienen que tener asignado un rol',
            'people.*.address_zip.required_if'      => 'Todos los derechohabientes que no sean socios tienen que tener indicado el código postal',
            'people.*.birth_country_id.required_if' => 'Todos los derechohabientes que no sean socios tienen que tener indicado la nacionalidad',
            'people.*.birth_date.required_if'       => 'Todos los derechohabientes que no sean socios tienen que tener indicado la fecha de nacimiento',
            'people.*.doc_type.required_if'         => 'Todos los derechohabientes que no sean socios tienen que tener indicado el tipo de documento',
            'people.*.email.required_if'            => 'Todos los derechohabientes que no sean socios tienen que tener indicado la dirección de correo electrónico',
            
            'people.*.phone_area.required_if'       => 'Todos los derechohabientes que no sean socios tienen que tener indicado el número de teléfono',
            'people.*.phone_country.required_if'    => 'Todos los derechohabientes que no sean socios tienen que tener indicado el número de teléfono',
            'people.*.phone_number.required_if'     => 'Todos los derechohabientes que no sean socios tienen que tener indicado el número de teléfono',
            'people.*.street_name.required_if'      => 'Todos los derechohabientes que no sean socios tienen que tener indicado la dirección',
            'people.*.street_number.required_if'    => 'Todos los derechohabientes que no sean socios tienen que tener indicado la dirección'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $public = 0;
            $mechanic = 0;
            $sync = 0;

            foreach($this->people as $person) {
                $public += floatval($person['public']);
                $mechanic += floatval($person['mechanic']);
                $sync += floatval($person['sync']);
            }

            if ($public != 100) {
                $validator->errors()->add('people.*.public', 'La suma de porcentajes de ejecución pública tiene que dar 100%');
            }

            if ($mechanic != 100) {
                $validator->errors()->add('people.*.mechanic', 'La suma de porcentajes de reproducción mecánica tiene que dar 100%');
            }

            if ($sync != 100) {
                $validator->errors()->add('people.*.sync', 'La suma de porcentajes de sincronización tiene que dar 100%');
            }
        });
    }
}
