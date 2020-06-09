<?php

namespace App\Http\Controllers\Api;

use App\Caracteristica;
use App\CaracteristicaInteresse;
use App\Interesse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InteresseController extends Controller
{
    private $with = 'join';

    public function index()
    {
        $qtd = request()->input('qtd', 100000);

        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $dados = Interesse::with($relations)->paginate($qtd);
        } else {
            $dados = Interesse::paginate($qtd);
        }

        return $dados->items();
    }

    public function store(Request $request)
    {
        $data = new Interesse($request->all());
        if ($data->save()) {
            return [
                'status' => 1,
                'data' => $data,
            ];
        }

        return [
            'status' => 0,
            'data' => null,
        ];
    }

    public function show(int $id)
    {
        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $interesse = Interesse::with($relations)->find($id);
        } else {
            $interesse = Interesse::find($id);
        }

        if (strpos($with, "caracteristicas") !== false) {
            foreach ($interesse->caracteristicas as $i => $_) {
                $interesse->caracteristicas[$i]->valor_opcao;
            }
        }

        return $interesse;
    }

    public function update(Request $request, int $id)
    {
        if (count($request->all())) {
            $data = Interesse::find($id);
            $data->update($request->all());
            return [
                'status' => 1,
                'data' => $data,
            ];
        }

        return [
            'status' => 0,
            'data' => null,
        ];
    }

    public function destroy(int $id)
    {
        return [
            'status' => 0,
        ];
    }
}
