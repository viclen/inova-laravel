<?php

namespace App\Http\Controllers\Api;

use App\Regra;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegraController extends Controller
{
    private $with = 'join';

    public function index()
    {
        if (request()->input($this->with)) {
            return Regra::with(request()->input($this->with))->get();
        }

        return Regra::all();
    }

    public function store(Request $request)
    {
        return [
            'status' => 0,
            'data' => null,
        ];
    }

    public function show(int $id)
    {
        if (request()->input($this->with)) {
            return Regra::with(request()->input($this->with))->find($id);
        }

        return Regra::find($id);
    }

    public function update(Request $request, int $id)
    {
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
