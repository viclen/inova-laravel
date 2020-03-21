<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
