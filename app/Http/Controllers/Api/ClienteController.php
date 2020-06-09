<?php

namespace App\Http\Controllers\Api;

use App\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    private $with = 'join';

    public function index()
    {
        $qtd = request()->input('qtd', 100000);

        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $dados = Cliente::with($relations)->paginate($qtd);
        } else {
            $dados = Cliente::paginate($qtd);
        }

        return $dados->items();
    }

    public function store(Request $request)
    {
        $data = new Cliente($request->all());
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
            $cliente = Cliente::with($relations)->find($id);
        } else {
            $cliente = Cliente::find($id);
        }

        return $cliente;
    }

    public function update(Request $request, int $id)
    {
        if (count($request->all())) {
            $data = Cliente::find($id);
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
