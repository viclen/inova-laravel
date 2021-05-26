<?php

use App\CaracteristicaCarroCliente;
use App\CaracteristicaEstoque;
use App\CaracteristicaInteresse;
use App\CarroCliente;
use App\Cliente;
use App\Estoque;
use App\Interesse;
use App\Match;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

        Interesse::truncate();
        Estoque::truncate();
        Cliente::truncate();
        CaracteristicaEstoque::truncate();
        CaracteristicaInteresse::truncate();
        CarroCliente::truncate();
        CaracteristicaCarroCliente::truncate();
        Match::truncate();

        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
    }
}
