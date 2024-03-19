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
        DB::table('categories')->truncate();
        DB::table('categories')->insert([
            [
                'label' => 'Alimentation',
                'credit' => false,
                'recurrent' => false,
            ], [
                'label' => 'Carburant',
                'credit' => false,
                'recurrent' => false,
            ], [
                'label' => 'Revenue',
                'credit' => true,
                'recurrent' => true,
            ], [
                'label' => 'Dépense fixe',
                'credit' => false,
                'recurrent' => false,
                'recurrent' => true,
            ], [
                'label' => 'Restaurant',
                'credit' => false,
                'recurrent' => false,
            ],
        ]);
    }
}
