<?php

use App\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            "Mini",
            "Econômico",
            "Compacto",
            "Standard",
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
        ];

        asort($tipos, SORT_ASC);

        $categorias = [];
        foreach ($tipos as $tipo) {
            $categorias[] = [
                'nome' => $tipo
            ];
        }

        Categoria::insert($categorias);
    }
}
