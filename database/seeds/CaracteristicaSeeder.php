<?php

use App\Caracteristica;
use App\Categoria;
use App\OpcaoCaracteristica;
use Illuminate\Database\Seeder;

class CaracteristicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $padroes = [
            'cor' => [
                'branco',
                'preto',
                'prata',
                'vermelho',
                'azul',
                'verde',
                'amarelo',
                'dourado',
                'outra'
            ],
            'categoria' => [
                "Standard",
                "Mini",
                "Econômico",
                "Compacto",
                "Intermediário",
                "Premium",
                "Full-Size",
                "SUV",
                "Especial",
                "Luxo",
                "Conversível",
                "Minivan",
                "Van",
                "Utilitário",
                "Pick-Up",
                "Esportivo",
                "Hatch",
                "Sedan",
                "Perua",
                "Cupê",
            ],
            'financiado' => 4,
            'automatico' => 4,
            'ano' => 1,
            'valor' => 2,
            'km' => 1,
            'placa' => 0,
            'fipe' => 2,
            'chassi' => 0,
            'combustivel' => [
                'gasolina',
                'diesel',
                'alcool'
            ],
        ];

        $caracteristicas = [];
        $opcoes = [];

        $id = 1;
        foreach ($padroes as $nome => $tipo) {
            if (is_array($tipo)) {
                $caracteristicas[] = [
                    'id' => $id,
                    'nome' => $nome,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tipo' => 3
                ];

                foreach ($tipo as $ordem => $valor) {
                    $opcoes[] = [
                        'caracteristica_id' => $id,
                        'ordem' => $ordem,
                        'valor' => $valor
                    ];
                }
            } else {
                $caracteristicas[] = [
                    'id' => $id,
                    'nome' => $nome,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tipo' => $tipo
                ];
            }
            $id++;
        }

        // Caracteristica::insert($caracteristicas);
        OpcaoCaracteristica::truncate();
        OpcaoCaracteristica::insert($opcoes);
    }
}
