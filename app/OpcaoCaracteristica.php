<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpcaoCaracteristica extends Model
{
    protected $fillable = ['id', 'caracteristica_id', 'ordem', 'valor'];
    // protected $table = "opcao_caracteristicas";

    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class);
    }
}
