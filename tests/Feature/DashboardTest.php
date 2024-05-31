<?php

use App\Models\Category;
use App\Models\Poste;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

describe('Test Note blade component', function () {
    test('Simple label', function () {
        $view = $this->blade('<x-dashboard.note :label="$label"/>', ['label' => 'test 1']);
        $view->assertSeeText('test 1');
    });

    test('Simple price', function () {
        $view = $this->blade('<x-dashboard.note :price="$price"/>', ['price' => 12]);
        $view->assertSeeText('12,00');
        $view->assertDontSee('price="12"', false);
    });

    test('Label and Price', function () {
        $view = $this->blade('<x-dashboard.note :label="$label" :price="$price"/>', ['label' => 'test 2', 'price' => 13]);
        $view->assertSeeText('test 2');
        $view->assertSeeText('13,00');
    });
});

describe('Test Category blade component', function () {
    test('Simple catégorie', function () {
        $this->seed();
        $category = Category::find(3);
        $view = $this->blade('<x-dashboard.category :category="$category" />', ['category' => $category]);
        $view->assertSeeText('Revenue');
        $view->assertSeeText('0,00');
    });

    test('Simple catégorie, avec montant', function () {
        $this->seed();
        $category = Category::find(3);
        $category->notes_sum_price = 12345;
        $view = $this->blade('<x-dashboard.category :category="$category" />', ['category' => $category]);
        $view->assertSeeText('Revenue');
        $view->assertSeeText('123,45');
    });

    test('Catégorie avec 1 poste', function () {
        $this->seed();
        $category = Category::with(['postes' => function (HasMany $query) {
            $query->withCount('notes');
        }, 'postes.notes'])->find(2);
        $view = $this->blade('<x-dashboard.category :category="$category" />', ['category' => $category]);
        $view->assertSeeText('Carburant');
        $view->assertDontSee('fromage');
        $view->assertDontSee('boulangerie');
        $view->assertSee('voiture1');
    });

    test('Catégorie avec 2 postes, mais 1 seule avec note', function () {
        $this->seed();
        $category = Category::with(['postes' => function (HasMany $query) {
            $query->withCount('notes');
        }, 'postes.notes'])->find(1);
        $view = $this->blade('<x-dashboard.category :category="$category" />', ['category' => $category]);
        $view->assertSeeText('Alimentation');
        $view->assertSeeText('fromage'); // poste avec notes
        $view->assertDontSee('boulangerie'); // poste sans note
        $view->assertDontSee('voiture1');
    });
});
