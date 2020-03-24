<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    public function carros()
    {
        $this->hasMany(Carro::class);
    }
}
