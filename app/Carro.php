<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Carro extends Model
{
    protected $fillable = [
        'nome',
        'fipe_id',
        'marca_id',
        'categoria_id'
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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function getTypes()
    {
        $types = [];
        $items = DB::select('describe ' . $this->getTable());

        foreach ($items as $item) {
            if (strpos($item->Field, "fipe") === false) {
                $types[$item->Field] = $item->Type;
            }
        }

        return $types;
    }
}
