<?php

namespace App\Http\Controllers\Api;

use App\Interesse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InteresseController extends Controller
{
    private $with = 'join';

    public function index()
    {
        if (request()->input($this->with)) {
            return Interesse::with(request()->input($this->with))->get();
        }

        return Interesse::all();
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
        if (request()->input($this->with)) {
            return Interesse::with(request()->input($this->with))->find($id);
        }

        return Interesse::find($id);
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
