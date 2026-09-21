<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\BudgetUser;
use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Note $note): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $budget_id): bool
    {
        return BudgetUser::where('user_id', $user->id)
            ->where('budget_id', $budget_id)
            ->whereIn('role', [Role::OWNER, Role::WRITER])
            ->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Note $note): bool
    {
        return BudgetUser::where('user_id', $user->id)
            ->join('budgets', 'budget_user.budget_id', '=', 'budgets.id')
            ->join('categories', 'budgets.id', '=', 'categories.budget_id')
            ->where('categories.id', $note->category_id)
            ->whereIn('role', [Role::OWNER, Role::WRITER])
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Note $note): bool
    {
        return BudgetUser::where('user_id', $user->id)
            ->join('budgets', 'budget_user.budget_id', '=', 'budgets.id')
            ->join('categories', 'budgets.id', '=', 'categories.budget_id')
            ->where('categories.id', $note->category_id)
            ->whereIn('role', [Role::OWNER, Role::WRITER])
            ->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Note $note): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Note $note): bool
    {
        return false;
    }
}
