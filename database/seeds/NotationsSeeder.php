<?php

use Illuminate\Database\Seeder;

class NotationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 10)->create();
        factory(App\Http\Model\NotationModel::class, 'notation', 10)->create();
    }
}
