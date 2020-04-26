<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MarcaSeeder::class);
        $this->call(CarroSeeder::class);
        $this->call(ModeloSeeder::class);

        DB::beginTransaction();

        $this->call(UserSeeder::class);
        $this->call(ClienteSeeder::class);
        $this->call(CaracteristicaSeeder::class);
        $this->call(CarroClienteSeeder::class);
        $this->call(EstoqueSeeder::class);
        $this->call(InteresseSeeder::class);
        $this->call(RegraSeeder::class);

        $this->call(CaracteristicaEstoqueSeeder::class);
        $this->call(CaracteristicaInteresseSeeder::class);

        DB::commit();

        // $this->call(FipeSeeder::class);
    }
}
