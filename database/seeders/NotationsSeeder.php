<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Model\NotationModel;
use App\Http\Model\Notation\NotationViewModel;

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
