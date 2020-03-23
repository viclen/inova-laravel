<?php

namespace App\Http\Controllers;

use App\Regra;
use Illuminate\Http\Request;

class RegraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.regra.index',[
            'dados' => Regra::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Regra  $regra
     * @return \Illuminate\Http\Response
     */
    public function show(Regra $regra)
    {
        return $regra;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Regra  $regra
     * @return \Illuminate\Http\Response
     */
    public function edit(Regra $regra)
    {
        return $regra;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Regra  $regra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Regra $regra)
    {
        return $regra;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Regra  $regra
     * @return \Illuminate\Http\Response
     */
    public function destroy(Regra $regra)
    {
        return [
            'status' => 1,
        ];
    }

    public function list()
    {
        return Regra::all();
    }
}
