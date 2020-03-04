<?php

use App\Carro;
use App\CarroCliente;
use App\Cliente;
use Illuminate\Database\Seeder;

class CarroClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientes = Cliente::all();

        $max_carro = Carro::orderByDesc("id")->first()->id;

        $carrosclientes = [];
        foreach ($clientes as $i => $cliente) {
            if (random_int(0, 10) > 3) {
                $carrosclientes[] = [
                    'cliente_id' => $cliente->id,
                    'carro_id' => random_int(1, $max_carro),
                    'valor' => random_int(5, 20) * 1000,
                ];
            }
        }

        CarroCliente::insert($carrosclientes);
    }
}
