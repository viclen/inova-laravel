<?php

use App\Carro;
use App\Cliente;
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

        $interesses = [];
        foreach ($clientes as $cliente) {
            $carro = Carro::with('estoques')->find(random_int(1, $max_carro));

            $interesses[] = [
                'ano' => count($carro->estoques) ? $carro->estoques[0]->ano + random_int(-3, 3) : random_int(1990, 2020),
                'cor' => $cores[random_int(0, count($cores) - 1)],
                'financiado' => random_int(0, 1),
                'cliente_id' => $cliente->id,
                'carro_id' => $carro->id,
            ];
        }

        echo count($interesses);

        Interesse::insert($interesses);
    }
}
