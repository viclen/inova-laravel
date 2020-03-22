<?php

use App\Carro;
use App\Cliente;
use App\Estoque;
use App\Interesse;
use Illuminate\Database\Seeder;

class InteresseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientes = Cliente::all();
        $cores = ['branco', 'preto', 'prata', 'vermelho', 'azul', 'verde', 'amarelo', 'dourado'];

        $max_carro = Carro::count();
        $max_estoque = Estoque::count();

        $interesses = [];
        foreach ($clientes as $cliente) {
            $carro = Carro::with('estoques')->find(random_int(1, $max_carro));

            $interesses[] = [
                'ano' => count($carro->estoques) ? $carro->estoques[0]->ano + random_int(-3, 3) : random_int(1990, 2020),
                'cor' => $cores[random_int(0, count($cores) - 1)],
                'valor' => count($carro->estoques) ? $carro->estoques[0]->valor + random_int(-$carro->estoques[0]->valor * 0.2, $carro->estoques[0]->valor * 0.2) : random_int(3000, 20000),
                'financiado' => random_int(0, 1),
                'cliente_id' => $cliente->id,
                'carro_id' => $carro->id,
                'created_at' => now(),
            ];

            $estoque = Estoque::find(random_int(1, $max_estoque));
            $interesses[] = [
                'ano' => $estoque->ano + random_int(-3, 3),
                'valor' => $estoque->valor + random_int(-$estoque->valor * 0.2, $estoque->valor * 0.2),
                'cor' => $cores[random_int(0, count($cores) - 1)],
                'financiado' => random_int(0, 1),
                'cliente_id' => $cliente->id,
                'carro_id' => $estoque->carro_id,
                'created_at' => now(),
            ];
        }

        Interesse::insert($interesses);
    }
}
