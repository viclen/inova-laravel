<?php

namespace App\Http\Controllers;

use App\Regra;
use Exception;
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
        return view('pages.regra.index', [
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
        $request->validate([
            'data' => 'required|array'
        ]);

        try {
            if (is_array($request['data'])) {
                foreach ($request['data'] as $data) {
                    if (is_array($data)) {
                        if (isset($data['grupo']) && isset($data['nome']) && isset($data['valor'])) {
                            $this->updateOrCreate($data);
                        }
                    } else {
                        $data = $request['data'];

                        if (isset($data['grupo']) && isset($data['nome']) && isset($data['valor'])) {
                            $this->updateOrCreate($data);
                            return [
                                'status' => 1
                            ];
                        }
                        break;
                    }
                }

                return [
                    'status' => 1
                ];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return [
            'status' => 0
        ];
    }

    function updateOrCreate($data)
    {
        $existe = Regra::where([
            ['grupo', $data['grupo']],
            ['nome', $data['nome']],
        ])->first();

        if ($existe) {
            Regra::where([
                ['grupo', $data['grupo']],
                ['nome', $data['nome']],
            ])->update([
                'valor' => $data['valor']
            ]);
            return 1;
        } else {
            $r = new Regra();
            $r->grupo = $data['grupo'];
            $r->nome = $data['nome'];
            $r->valor = $data['valor'];
            $r->save();
        }

        return 0;
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
