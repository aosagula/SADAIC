<?php

namespace App\Models\Jingles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Part extends Pivot
{
    protected $table = 'jingles_parts';

    public const TYPE = [
        1 => 'applicant',
        2 => 'advertiser',
        3 => 'agency'
    ];

    protected $fillable = [
        'registration_id',
        'person_id',
        'type'
    ];

    public $timestamps = false;

    public function type()
    {
        if (!$this->type_id) {
            return null;
        }

        if (!array_key_exists($this->type_id, $this->TYPE)) {
            return null;
        }

        return [
            'id'   => $this->type_id,
            'name' => $this->TYPE[$this->type_id]
        ];
    }
}
