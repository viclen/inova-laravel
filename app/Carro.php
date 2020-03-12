<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    protected $fillable = [
        'nome',
        'fipe_id',
        'marca_id'
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }

    public function interesses()
    {
        return $this->hasMany(Interesse::class);
    }
}
