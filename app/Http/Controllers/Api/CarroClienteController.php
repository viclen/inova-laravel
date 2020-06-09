<?php

namespace App\Http\Controllers\Api;

use App\CarroCliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarroClienteController extends Controller
{
    private $with = 'join';

    public function show(int $id)
    {
        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $carro = CarroCliente::with($relations)->find($id);
        } else {
            $carro = CarroCliente::find($id);
        }

        if (strpos($with, "caracteristicas") !== false) {
            foreach ($carro->caracteristicas as $i => $_) {
                $carro->caracteristicas[$i]->valor_opcao;
            }
        }

        return $carro;
    }
}
