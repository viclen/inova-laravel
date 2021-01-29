<?php

namespace App\Http\Controllers;

use App\Caracteristica;
use App\CaracteristicaCarroCliente;
use App\Carro;
use App\CarroCliente;
use App\Cliente;
use App\Marca;
use Illuminate\Http\Request;

class CarroClienteController extends Controller
{
    public function index()
    {
        return redirect('/clientes/' . request()->cliente);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cliente = Cliente::find(request()->cliente);

        return view('pages.cliente.carro.create', [
            'caracteristicas' => Caracteristica::with('opcoes')->get(),
            'marcas' => Marca::all(),
            'carros' => Carro::with('marca')->get(),
            'cliente' => $cliente
        ]);
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
            'caracteristicas' => 'array',
            'carro_id' => 'required',
            'observacoes' => '',
            'id' => ''
        ]);

        if (isset($request['id']) && $request['id']) {
            $carro_cliente = CarroCliente::find($request['id']);
            $carro_cliente->carro_id = $request['carro_id'];
            $carro_cliente->observacoes = $request['observacoes'];

            CaracteristicaCarroCliente::where('carro_cliente_id', $carro_cliente->id)->delete();
        } else {
            $carro_cliente = new CarroCliente([
                'carro_id' => $request['carro_id'],
                'cliente_id' => request()->cliente,
                'observacoes' => $request['observacoes']
            ]);
        }
        $carro_cliente->save();

        $caracteristicas = [];
        foreach ($request['caracteristicas'] as $caracteristica) {
            $caracteristicas[] = [
                'caracteristica_id' => $caracteristica['id'],
                'carro_cliente_id' => $carro_cliente->id,
                'valor' => $caracteristica['valor'],
            ];
        }
        CaracteristicaCarroCliente::insert($caracteristicas);

        return [
            'status' => 1,
            'carro' => $carro_cliente
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $cliente, int $carro)
    {
        $carro_cliente = CarroCliente::with('caracteristicas')->find($carro);

        $cliente = Cliente::find($cliente);

        return view('pages.cliente.carro.edit', [
            'caracteristicas' => Caracteristica::with('opcoes')->get(),
            'marcas' => Marca::all(),
            'carros' => Carro::with('marca')->get(),
            'dados' => $carro_cliente,
            'cliente' => $cliente,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $cliente, int $id)
    {
        $carro_cliente = CarroCliente::find($id);

        CaracteristicaCarroCliente::where('carro_cliente_id', $id)->delete();

        if ($carro_cliente->delete()) {
            return [
                'status' => 1
            ];
        }

        return [
            'status' => 0
        ];
    }
}
