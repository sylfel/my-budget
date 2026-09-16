<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\Note;
use App\Models\User;

test('authenticated users can delete a note from a budget', function () {
    $user = User::factory()->create();
    $budget = Budget::create();
    $category = Category::create([
        'label' => 'Groceries',
        'credit' => false,
        'budget_id' => $budget->id,
    ]);
    $note = Note::create([
        'label' => 'Milk',
        'price' => 2500,
        'year' => 2026,
        'month' => 0,
        'category_id' => $category->id,
        'poste_id' => null,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(
        "/budget/{$budget->id}/notes/{$note->id}",
    );

    $response->assertRedirect(route('budget', ['budget' => $budget]));
    expect(Note::find($note->id))->toBeNull();
});
