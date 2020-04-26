<?php

use App\Carro;
use App\Cliente;
use App\Estoque;
use App\Interesse;
use App\Modelo;
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

        $max_carro = Carro::count();
        $max_estoque = Estoque::count();

        $interesses = [];
        foreach ($clientes as $cliente) {
            $carro = Carro::inRandomOrder()->first();

            $modelo = Modelo::where('carro_id', $carro->id)->inRandomOrder()->first();

            $interesses[] = [
                'cliente_id' => $cliente->id,
                'carro_id' => $carro->id,
                'modelo_id' => $modelo ? $modelo->id : null,
                'origem' => random_int(0, count(Interesse::ORIGENS) - 1),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $estoque = Estoque::find(random_int(1, $max_estoque));
            $interesses[] = [
                'cliente_id' => $cliente->id,
                'carro_id' => $estoque->carro_id,
                'modelo_id' => null,
                'origem' => random_int(0, count(Interesse::ORIGENS) - 1),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Interesse::insert($interesses);
    }
}
