<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $fillable = [
        'nome',
        'fipe_id'
    ];

    public function carros()
    {
        return $this->hasMany(Carro::class);
    }
}
