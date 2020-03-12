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
        if (request()->input($this->with)) {
            return Estoque::with(request()->input($this->with))->get();
        }

        return Estoque::all();
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
        if (request()->input($this->with)) {
            return Estoque::with(request()->input($this->with))->find($id);
        }

        return Estoque::find($id);
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
