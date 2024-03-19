<?php

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Seed insert categories', function () {
    $this->seed();

    $this->assertSame(5, Category::count(), 'Seed insert 5 category');
});

test('Seed insert 2 recurrent categories', function () {
    $this->seed();

    $this->assertSame(2, Category::where('recurrent', true)->count(), 'Seed insert 2 recurrent category');
});
