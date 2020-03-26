<?php

namespace App\Http\Controllers\Api;

use App\Categoria;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    private $with = 'join';

    public function index()
    {
        if (request()->input($this->with)) {
            return Categoria::with(request()->input($this->with))->get();
        }

        return Categoria::all();
    }

    public function store(Request $request)
    {
        $data = new Categoria($request->all());
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
            return Categoria::with(request()->input($this->with))->find($id);
        }

        return Categoria::find($id);
    }

    public function update(Request $request, int $id)
    {
        if (count($request->all())) {
            $data = Categoria::find($id);
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
