<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FipeCarro extends Model
{
    protected $fillable = [
        'id',
        'nome',
        'fipe_marca_id'
    ];
}
