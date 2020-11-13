<?php

use Illuminate\Database\Seeder;

class EstoquesInteresses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ClienteSeeder::class);
        $this->call(CarroClienteSeeder::class);
        $this->call(EstoqueSeeder::class);
        $this->call(InteresseSeeder::class);
    }
}
