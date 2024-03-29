<?php

namespace App\Http\Controllers\Api;

use App\Carro;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    private $with = 'join';

    public function index()
    {
        if (request()->input($this->with)) {
            return Carro::with(request()->input($this->with))->orderByDesc('uso')->orderBy('nome')->get();
        }

        return Carro::orderByDesc('uso')->orderBy('nome')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'marca_id' => 'required',
        ]);

        $data = new Carro($request->all());
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
            return Carro::with(request()->input($this->with))->find($id);
        }

        return Carro::find($id);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nome' => 'required',
            'marca_id' => 'required',
        ]);

        if (count($request->all())) {
            $data = Carro::find($id);
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
