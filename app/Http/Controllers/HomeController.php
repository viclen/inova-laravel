<?php

namespace App\Http\Controllers;

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

        // dd($matches);

        return view('home', [
            'matches' => $matches,
        ]);
    }
}
