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

        $ordem = ['ano', 'cor', 'valor', 'categoria', 'marca', 'carro'];

        $regras = [];

        foreach ($ordem as $i => $item) {
            $regras[] = [
                'grupo' => "ordem",
                'nome' => "$item",
                'valor' => "$i",
            ];
        }

        $regras[] = [
            'grupo' => "valor",
            'nome' => "porcentagem",
            'valor' => "20",
        ];

        Regra::insert($regras);
    }
}
