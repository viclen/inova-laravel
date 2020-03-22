<?php

use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $max = 100;
        for ($i = 0; $i < $max; $i++) {
            factory(App\Cliente::class)->create();
        }
    }
}
