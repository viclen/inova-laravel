<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caracteristica extends Model
{
    protected $fillable = [
        'nome',
        'valor_padrao',
        'tipo'
    ];

    public function opcaos()
    {
        return $this->hasMany(OpcaoCaracteristica::class)->orderBy("ordem");
    }

    public function opcoes()
    {
        return $this->opcaos();
    }
}
