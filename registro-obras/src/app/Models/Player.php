<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class Player extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email', 'player_id'
    ];

    protected $dates = [
        'email_verified_at'
    ];
}
