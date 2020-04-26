<?php

namespace App;

class OpcaoCaracteristica extends CompositeKeyModel
{
    protected $primaryKey = ['caracteristica_id', 'ordem'];
    protected $fillable = ['caracteristica_id', 'ordem', 'valor'];
    // protected $table = "opcao_caracteristicas";

    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class);
    }
}
