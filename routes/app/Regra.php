<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Regra extends Model
{
    protected $fillable = ['grupo', 'nome', 'valor'];

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
