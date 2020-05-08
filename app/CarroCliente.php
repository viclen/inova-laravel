<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CarroCliente extends Model
{
    protected $fillable = [
        'carro_id',
        'cliente_id'
    ];

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
