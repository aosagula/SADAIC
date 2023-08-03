<?php

namespace App\Models\SADAIC;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'source_members';
    protected $primaryKey = ['codanita', 'num_doc'];

    public $incrementing = false;
    public $timestamps = false;

    public function getFullAddressAttribute()
    {
        $output = $this->domicilio;
        if ($this->piso) { $output .= ' ' . $this->piso; }
        if ($this->dpto) { $output .= ' ' . $this->dpto; }
        if ($this->cod_postal) { $output .= ', ' . $this->cod_postal; }

        return $output;
    }
}
