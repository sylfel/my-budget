<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('notes')->truncate();
        DB::table('postes')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();

        $user = User::factory()->create();

        DB::table('categories')->insert([
            [
                'id' => 1,
                'label' => 'Alimentation',
                'credit' => false,
                'recurrent' => false,
                'extra' => false,
            ], [
                'id' => 2,
                'label' => 'Carburant',
                'credit' => false,
                'recurrent' => false,
                'extra' => false,
            ], [
                'id' => 3,
                'label' => 'Revenue',
                'credit' => true,
                'recurrent' => true,
                'extra' => false,
            ], [
                'id' => 4,
                'label' => 'Dépense fixe',
                'credit' => false,
                'recurrent' => false,
                'recurrent' => true,
                'extra' => false,
            ], [
                'id' => 5,
                'label' => 'Restaurant',
                'credit' => false,
                'recurrent' => false,
                'extra' => true,
            ],
        ]);

        DB::table('postes')->insert([
            [
                'id' => 1,
                'label' => 'fromage',
                'category_id' => 1,
            ],
            [
                'id' => 2,
                'label' => 'boulangerie',
                'category_id' => 1,
            ],
            [
                'id' => 3,
                'label' => 'voiture1',
                'category_id' => 2,
            ],
        ]);

        DB::table('notes')->insert([
            [
                'category_id' => 1,
                'poste_id' => 1,
                'price' => 5000,
                'year' => 2024,
                'month' => 3,
                'user_id' => $user->id,
            ],
            [
                'category_id' => 1,
                'poste_id' => 1,
                'price' => 4500,
                'year' => 2024,
                'month' => 2,
                'user_id' => $user->id,
            ],
            [
                'category_id' => 1,
                'poste_id' => 1,
                'price' => 4000,
                'year' => 2024,
                'month' => 1,
                'user_id' => $user->id,
            ],
            [
                'category_id' => 1,
                'poste_id' => 1,
                'price' => 3500,
                'year' => 2024,
                'month' => 0,
                'user_id' => $user->id,
            ],
            [
                'category_id' => 1,
                'poste_id' => 1,
                'price' => 3000,
                'year' => 2023,
                'month' => 11,
                'user_id' => $user->id,
            ],
            [
                'category_id' => 2,
                'poste_id' => 3,
                'price' => 2500,
                'year' => 2023,
                'month' => 10,
                'user_id' => $user->id,
            ],
        ]);
    }
}
