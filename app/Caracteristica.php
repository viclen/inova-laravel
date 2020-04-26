<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caracteristica extends Model
{
    protected $fillable = [
        'nome',
        'valor_padrao'
    ];

    public function opcaos()
    {
        return $this->hasMany(OpcaoCaracteristica::class);
    }

    public function opcoes()
    {
        return $this->opcaos();
    }
}
