<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index($carro_id)
    {
        return Modelo::where("carro_id", $carro_id)->get();
    }

    public function show($carro_id, $id)
    {
        return Modelo::find($id);
    }

    public function filter()
    {
        return request();
    }
}
