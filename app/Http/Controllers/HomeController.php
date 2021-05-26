<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Match;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $matches = DB::table('matches')
            ->join('interesses', 'interesses.id', 'interesse_id')
            ->join('clientes', 'clientes.id', 'interesses.cliente_id')
            ->join('estoques', 'estoques.id', 'estoque_id')
            ->join('carros as carro_estoque', 'estoques.carro_id', 'carro_estoque.id')
            ->join('carros as carro_interesse', 'interesses.carro_id', 'carro_interesse.id')
            ->where(function ($query) {
                $query->where('estoques.created_at', '>', today()->subWeek());
                $query->orWhere('interesses.created_at', '>', today()->subWeek());
            })
            ->where('prioridade', '>', 3)
            ->selectRaw('
                clientes.nome as cliente,
                carro_estoque.nome as carro_estoque,
                carro_interesse.nome as carro_interesse,
                estoques.id as estoque_id,
                prioridade,
                matches.caracteristicas
            ')
            ->limit(10)
            ->groupBy('clientes.id')
            ->orderByDesc('prioridade')
            ->get();

        $grafico_carros = DB::table('carros')
            ->join('marcas', 'marcas.id', 'carros.marca_id')
            ->join('interesses', 'interesses.carro_id', 'carros.id')
            ->selectRaw('carros.*, marcas.nome as marca, COUNT(interesses.carro_id) as count_interesses')
            ->groupBy('carros.id')
            ->orderByDesc('count_interesses')
            ->limit(30)
            ->get();

        $grafico_marcas = DB::table('marcas')
            ->join('carros', 'marcas.id', 'carros.marca_id')
            ->join('interesses', 'interesses.carro_id', 'carros.id')
            ->selectRaw('marcas.*, COUNT(interesses.id) as count_interesses')
            ->groupBy('marcas.id')
            ->orderByDesc('count_interesses')
            ->limit(30)
            ->get();

        $grafico_categorias = DB::table('caracteristica_interesses')
            ->join('interesses', 'interesses.id', 'caracteristica_interesses.interesse_id')
            ->join('opcao_caracteristicas', function ($join) {
                $join->on('opcao_caracteristicas.ordem', 'caracteristica_interesses.valor')
                    ->on('opcao_caracteristicas.caracteristica_id', 'caracteristica_interesses.caracteristica_id');
            })
            ->selectRaw('opcao_caracteristicas.valor as nome, COUNT(interesses.id) as count_interesses')
            ->groupBy('opcao_caracteristicas.ordem')
            ->orderByDesc('count_interesses')
            ->whereRaw('opcao_caracteristicas.caracteristica_id = 2')
            ->limit(30)
            ->get();

        return view('home', [
            'matches' => $matches,
            'graficos_data' => [
                'grafico_carros' => $grafico_carros,
                'grafico_marcas' => $grafico_marcas,
                'grafico_categorias' => $grafico_categorias,
            ]
        ]);
    }
}
