<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $fillable = [
        'valor',
        'fipe',
        'ano',
        'cor',
        'chassi',
        'observacoes',
        'carro_id',
    ];

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }
}
