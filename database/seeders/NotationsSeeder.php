<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotationModel;
use App\Models\Notation\NotationViewModel;

class NotationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        NotationModel::factory()->count(70)->create();
        factory(NotationViewModel::class, 70)->create();
    }
}
