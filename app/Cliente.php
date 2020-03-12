<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public function carros()
    {
        return $this->belongsToMany(Carro::class, 'carro_clientes');
    }

    public function interesses()
    {
        return $this->hasMany(Interesse::class);
    }
}
