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
        factory(App\User::class, 5)->create();
        factory(App\Http\Model\NotationModel::class, 'notation', 70)->create();
        factory(App\Http\Model\Notation\NotationViewModel::class, 'views_notation', 70)->create();
    }
}
