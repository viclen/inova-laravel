<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imagem extends Model
{
    protected $fillable = [
        'id',
        'arquivo',
        'descricao',
    ];
}
