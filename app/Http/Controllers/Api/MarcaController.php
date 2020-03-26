<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    private $with = 'join';

    public function index()
    {
        if (request()->input($this->with)) {
            return Marca::with(request()->input($this->with))->get();
        }

        return Marca::all();
    }

    public function store(Request $request)
    {
        $data = new Marca($request->all());
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
            return Marca::with(request()->input($this->with))->find($id);
        }

        return Marca::find($id);
    }

    public function update(Request $request, int $id)
    {
        if (count($request->all())) {
            $data = Marca::find($id);
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
