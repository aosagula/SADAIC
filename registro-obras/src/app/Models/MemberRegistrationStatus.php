<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRegistrationStatus extends Model
{
    protected $table = 'members_registration_status';

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;
}
