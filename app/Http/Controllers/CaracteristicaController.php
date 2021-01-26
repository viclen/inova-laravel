<?php

namespace App\Http\Controllers;

use App\Caracteristica;
use App\CaracteristicaCarroCliente;
use App\CaracteristicaEstoque;
use App\CaracteristicaInteresse;
use App\OpcaoCaracteristica;
use App\Regra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Environment\Console;
use Throwable;

class CaracteristicaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $caracteristicas = Caracteristica::with('opcoes')->get();

        return view('pages.caracteristicas.index', [
            'caracteristicas' => $caracteristicas,
            'regras' => Regra::all(),
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
            "id" => '',
            "nome" => 'required_with:id',
            "valor_padrao" => '',
            "tipo" => 'integer',
            "opcoes" => 'array',
            "exclusoria" => 'boolean',
            "peso" => '',
        ]);

        if (isset($request['id']) && $request['id']) {
            $caracteristica = Caracteristica::find($request['id']);
            $caracteristica->id = $request['id'];
            $caracteristica->nome = $request['nome'];
            $caracteristica->valor_padrao = $request['valor_padrao'];
            $caracteristica->tipo = $request['tipo'];
            $caracteristica->exclusoria = $request['exclusoria'] ?: 0;
            $caracteristica->peso = $request['peso'];
            $caracteristica->save();

            if ($caracteristica->tipo == 3) {
                $opcoes_atuais = OpcaoCaracteristica::where('caracteristica_id', $caracteristica->id)->get();
                $opcoes_novas = $request['opcoes'];

                foreach ($opcoes_novas as &$opcao_nova) {
                    if (!isset($opcao_nova['id']) || !$opcao_nova['id']) {
                        $opcao_nova['id'] = DB::table('opcao_caracteristicas')->insertGetId($opcao_nova);
                    }
                }

                foreach ($opcoes_atuais as $opcao_atual) {
                    $found = false;
                    foreach ($opcoes_novas as $opcao_nova) {
                        if (isset($opcao_nova['id']) && $opcao_atual->id == $opcao_nova['id']) {
                            $found = true;
                            unset($opcao_nova['id']);
                            DB::table('opcao_caracteristicas')->where('id', $opcao_atual->id)->update($opcao_nova);
                        }
                    }
                    if (!$found) {
                        $opcao_atual->delete();
                    }
                }
            }

            $caracteristica->load('opcoes');
        } else {
            $caracteristica = new Caracteristica([
                'nome' => $request['nome'] ?: '',
                'valor_padrao' => $request['valor_padrao'],
                'tipo' => $request['tipo'],
            ]);

            $caracteristica->save();
            $caracteristica->opcoes = [];
        }

        return $caracteristica;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Caracteristica  $caracteristica
     * @return \Illuminate\Http\Response
     */
    public function show(Caracteristica $caracteristica)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Caracteristica  $caracteristica
     * @return \Illuminate\Http\Response
     */
    public function edit(Caracteristica $caracteristica)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Caracteristica  $caracteristica
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caracteristica $caracteristica)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Caracteristica  $caracteristica
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $int = CaracteristicaInteresse::where('caracteristica_id', $id)->first();
        if ($int) {
            return [
                'status' => 0,
                'error' => 'Essa característica não pode ser removida pois está sendo usada em um interesse'
            ];
        }

        $est = CaracteristicaEstoque::where('caracteristica_id', $id)->first();
        if ($est) {
            return [
                'status' => 0,
                'error' => 'Essa característica não pode ser removida pois está sendo usada em um estoque'
            ];
        }

        $carr = CaracteristicaCarroCliente::where('caracteristica_id', $id)->first();
        if ($carr) {
            return [
                'status' => 0,
                'error' => 'Essa característica não pode ser removida pois está sendo usada em um carro de cliente'
            ];
        }

        OpcaoCaracteristica::where('caracteristica_id', $id)->delete();
        Caracteristica::where('id', $id)->delete();

        return [
            'status' => 1
        ];
    }
}
