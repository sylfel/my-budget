<?php

namespace Database\Seeders;

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
        // \App\Models\User::factory(10)->create();
        DB::table('categories')->truncate();
        DB::table('categories')->insert([[
            'label' => 'Alimentation',
            'credit' => false,
        ], [
            'label' => 'Carburant',
            'credit' => false,
        ], [
            'label' => 'Revenue',
            'credit' => true,
        ], [
            'label' => 'Dépense fixe',
            'credit' => false,
        ], [
            'label' => 'Restaurant',
            'credit' => false,
        ], [
            'label' => 'Maison',
            'credit' => false,
        ], [
            'label' => 'Santé',
            'credit' => false,
        ], [
            'label' => 'Habillement',
            'credit' => false,
        ], [
            'label' => 'Perso / Beauté',
            'credit' => false,
        ], [
            'label' => 'Voiture',
            'credit' => false,
        ], [
            'label' => 'Librairie',
            'credit' => false,
        ], [
            'label' => 'Cadeaux',
            'credit' => false,
        ], [
            'label' => 'Jardinerie',
            'credit' => false,
        ], [
            'label' => 'Travail',
            'credit' => false,
        ], [
            'label' => 'Autres',
            'credit' => false,
        ],

        ]);
    }
}
