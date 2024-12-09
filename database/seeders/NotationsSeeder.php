<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notation\NotationModel;
use App\Models\Notation\NotationViewModel;

class NotationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        NotationModel::factory()
            ->has(
                NotationViewModel::factory()->viewRandomDateState(),
                'notationViews'
            )
            ->count(70)
        ->create();
    }
}
