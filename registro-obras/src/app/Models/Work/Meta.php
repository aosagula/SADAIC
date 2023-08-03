<?php

namespace App\Models\Work;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = 'works_meta';

    protected $fillable = [
        'distribution_id',
        'address_country_id',
        'address_state_id',
        'address_state_text',
        'address_city_id',
        'address_city_text',
        'address_zip',
        'apartment',
        'birth_country_id',
        'birth_date',
        'doc_type',
        'email',
        'floor',
        'name',
        'phone_area',
        'phone_country',
        'phone_number',
        'street_name',
        'street_number'
    ];
}
