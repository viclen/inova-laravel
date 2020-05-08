<?php

use App\Carro;
use App\CarroCliente;
use App\Cliente;
use App\Modelo;
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

        $carrosclientes = [];
        foreach ($clientes as $i => $cliente) {
            if (random_int(0, 10) > 6) {
                $carro = Carro::inRandomOrder()->first();

                $carrosclientes[] = [
                    'cliente_id' => $cliente->id,
                    'carro_id' => $carro->id,
                ];
            }
        }

        CarroCliente::insert($carrosclientes);
    }
}
