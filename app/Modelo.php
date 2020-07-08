<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $fillable = [
        'id',
        'nome',
        'ano',
        'preco',
        'combustivel',
        'fipe_id',
        'dados',
        'carro_id',
    ];

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }
}
