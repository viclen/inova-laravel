<?php

namespace App\Http\Controllers\Api;

use App\Estoque;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    private $with = 'join';

    public function index()
    {
        $qtd = request()->input('qtd', 100000);

        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $dados = Estoque::with($relations)->paginate($qtd);
        } else {
            $dados = Estoque::paginate($qtd);
        }

        return $dados->items();
    }

    public function store(Request $request)
    {
        $data = new Estoque($request->all());
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
            $estoque = Estoque::with($relations)->find($id);
        } else {
            $estoque = Estoque::find($id);
        }

        if (strpos($with, "caracteristicas") !== false) {
            foreach ($estoque->caracteristicas as $i => $_) {
                $estoque->caracteristicas[$i]->valor_opcao;
            }
        }

        return $estoque;
    }

    public function update(Request $request, int $id)
    {
        if (count($request->all())) {
            $data = Estoque::find($id);
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
