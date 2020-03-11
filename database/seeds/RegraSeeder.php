<?php

use App\Regra;
use Illuminate\Database\Seeder;

class RegraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 1;
        $regras = [
            [
                'coluna_carro' => 'carros.id',
                'coluna_interesse' => 'carro_id',
                'prioridade' => 50,
            ],
            [
                'coluna_carro' => 'estoques.ano',
                'coluna_interesse' => 'ano',
                'prioridade' => 30,
            ],
            [
                'coluna_carro' => 'estoques.cor',
                'coluna_interesse' => 'cor',
                'prioridade' => 15,
            ],
            // [
            //     'coluna_carro' => 'financiado',
            //     'coluna_interesse' => 'financiado',
            //     'prioridade' => 5,
            // ],
        ];

        Regra::insert($regras);
    }
}
