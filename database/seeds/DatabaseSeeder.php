<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(ClienteSeeder::class);
        $this->call(CarroSeeder::class);
        $this->call(CarroClienteSeeder::class);
        $this->call(MarcaSeeder::class);
        $this->call(EstoqueSeeder::class);
        $this->call(InteresseSeeder::class);
    }
}
