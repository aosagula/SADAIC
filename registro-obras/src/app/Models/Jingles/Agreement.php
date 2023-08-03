<?php

namespace App\Models\Jingles;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $table = 'jingles_registration_agreements';

    public const TYPE = [
        [ 'id' => 1, 'name' => 'member' ],
        [ 'id' => 2, 'name' => 'no-member' ]
    ];

    protected $fillable = [
        'registration_id',
        'type_id',
        'member_idx',
        'doc_number',
        'response'
    ];

    protected $casts = [
        'response' => 'boolean'
    ];

    public function member()
    {
        return $this->hasOne('App\Models\SADAIC\Member', 'idx', 'member_idx');
    }

    public function meta()
    {
        return $this->hasOne('App\Models\Jingles\Meta', 'agreement_id');
    }
}
