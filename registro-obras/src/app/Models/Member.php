<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email', 'member_id', 'heir'
    ];

    protected $dates = [
        'email_verified_at'
    ];

    protected $appends = [
        'type'
    ];

    public function getAuthIdentifier()
    {
        return $this->member_id . '-' . $this->heir;
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('member_id', $this->getAttribute('member_id'))
            ->where('heir', $this->getAttribute('heir'));
    }

    public function profile()
    {
        return $this->hasOne('App\Models\MemberProfile', 'member_id');
    }

    public function sadaic()
    {
        return $this->hasOne('App\Models\SADAIC\Member', 'codanita', 'member_id');
    }

    public function getFullNameAttribute()
    {
        if ($this->sadaic) {
            return ucwords(strtolower($this->sadaic->nombre));
        } elseif ($this->profile) {
            return ucwords(strtolower($this->profile->name));
        }

        return $this->member_id . '/' . $this->heir;
    }

    public function getTypeAttribute() {
        return 'member';
    }
}
