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
        if (request()->input($this->with)) {
            try {
                return Cliente::with(request()->input($this->with))->get();
            } catch (\Throwable $th) {
                return [
                    'status' => 0,
                    'message' => 'Relacao nao encontrada',
                ];
            }
        }

        return Cliente::all();
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
        if (request()->input($this->with)) {
            return Cliente::with(request()->input($this->with))->find($id);
        }

        return Cliente::find($id);
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
