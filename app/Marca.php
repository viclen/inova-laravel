<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Marca extends Model
{
    protected $fillable = [
        'nome',
        'fipe_id'
    ];

    protected $hidden = [
        'key'
    ];

    public function carros()
    {
        return $this->hasMany(Carro::class);
    }

    public function getTypes()
    {
        $types = [];
        $items = DB::select('describe ' . $this->getTable());

        foreach ($items as $item) {
            $types[$item->Field] = $item->Type;
        }

        return $types;
    }
}
