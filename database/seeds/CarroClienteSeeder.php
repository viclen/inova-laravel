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
                $modelo = Modelo::where('carro_id', $carro->id)->inRandomOrder()->first();

                if ($modelo) {
                    $carrosclientes[] = [
                        'cliente_id' => $cliente->id,
                        'carro_id' => $carro->id,
                        'modelo_id' => $modelo->id,
                        'valor' => random_int(5, 20) * 1000,
                    ];
                }
            }
        }

        CarroCliente::insert($carrosclientes);
    }
}
