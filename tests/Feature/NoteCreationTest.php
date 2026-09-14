<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\Note;
use App\Models\Poste;
use App\Models\User;

test('authenticated users can create a note for a budget', function () {
    $user = User::factory()->create();
    $budget = Budget::create();
    $category = Category::create([
        'label' => 'Groceries',
        'credit' => false,
        'budget_id' => $budget->id,
    ]);
    $poste = Poste::create([
        'label' => 'Food',
        'category_id' => $category->id,
    ]);

    $this->actingAs($user);

    $response = $this->post("/budget/{$budget->id}/notes", [
        'label' => 'Milk',
        'price' => 2500,
        'year' => 2026,
        'month' => 0,
        'category_id' => $category->id,
        'poste_id' => $poste->id,
    ]);

    $response->assertRedirect(route('budget', ['budget' => $budget]));

    expect(Note::query()->where('user_id', $user->id)->count())->toBe(1)
        ->and(Note::query()->where('user_id', $user->id)->first()->label)->toBe('Milk');
});

test('category is required when creating a note', function () {
    $user = User::factory()->create();
    $budget = Budget::create();
    $category = Category::create([
        'label' => 'Bills',
        'credit' => false,
        'budget_id' => $budget->id,
    ]);
    $poste = Poste::create([
        'label' => 'Internet',
        'category_id' => $category->id,
    ]);

    $this->actingAs($user);

    $this->post("/budget/{$budget->id}/notes", [
        'label' => 'Internet bill',
        'price' => 2500,
        'year' => 2026,
        'month' => 0,
        'poste_id' => $poste->id,
    ])->assertSessionHasErrors(['category_id']);
});

test('year and month are required when creating a note', function () {
    $user = User::factory()->create();
    $budget = Budget::create();
    $category = Category::create([
        'label' => 'Bills',
        'credit' => false,
        'budget_id' => $budget->id,
    ]);
    $poste = Poste::create([
        'label' => 'Internet',
        'category_id' => $category->id,
    ]);

    $this->actingAs($user);

    $this->post("/budget/{$budget->id}/notes", [
        'label' => 'Internet bill',
        'price' => 2500,
        'category_id' => $category->id,
        'poste_id' => $poste->id,
    ])->assertSessionHasErrors(['year', 'month']);
});

