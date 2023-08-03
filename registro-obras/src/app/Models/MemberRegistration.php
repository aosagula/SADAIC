<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRegistration extends Model
{
    protected $table = 'members_registration';

    protected $fillable = [
        'status_id',
        'user_id',
        'name',
        'birth_date',
        'birth_city_text',
        'birth_city_id',
        'birth_state_text',
        'birth_state_id',
        'birth_country_id',
        'doc_number',
        'doc_country',
        'work_code',
        'address_street',
        'address_number',
        'address_floor',
        'address_apt',
        'address_zip',
        'address_city_text',
        'address_city_id',
        'address_state_text',
        'address_state_id',
        'address_country_id',
        'landline',
        'mobile',
        'email',
        'pseudonym',
        'band',
        'entrance_work',
        'genre_id',
        'entry_date'
    ];

    protected $dates = [
        'birth_date'
    ];

    public function status()
    {
        return $this->hasOne('App\Models\MemberRegistrationStatus', 'id', 'status_id');
    }

    public function birth_city()
    {
        return $this->hasOne('App\Models\SADAIC\Cities', 'id', 'birth_city_id');
    }

    public function birth_state()
    {
        return $this->hasOne('App\Models\SADAIC\States', 'id', 'birth_state_id');
    }

    public function birth_country()
    {
        return $this->hasOne('App\Models\SADAIC\Countries', 'idx', 'birth_country_id');
    }

    public function address_city()
    {
        return $this->hasOne('App\Models\SADAIC\Cities', 'id', 'address_city_id');
    }

    public function address_state()
    {
        return $this->hasOne('App\Models\SADAIC\States', 'id', 'address_state_id');
    }

    public function address_country()
    {
        return $this->hasOne('App\Models\SADAIC\Countries', 'idx', 'address_country_id');
    }

    public function genre()
    {
        return $this->hasOne('App\Models\SADAIC\Genres', 'cod_int_gen', 'genre_id');
    }
}
