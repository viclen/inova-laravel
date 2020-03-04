<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }
}
