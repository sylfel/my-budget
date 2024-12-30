<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\Poste;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Budget::count() === 0) {
            $budget = Budget::create();
            DB::update(
                'update categories set budget_id = ?',
                [$budget->id]
            );
        }
        Schema::table('postes', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('poste_id')->references('id')->on('postes')->nullable()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('budget_id')->references('id')->on('budgets')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postes', function (Blueprint $table) {
            $table->dropForeignIdFor(Category::class);
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropForeignIdFor(Category::class);
            $table->dropForeignIdFor(Poste::class);
            $table->dropForeignIdFor(User::class);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeignIdFor(Budget::class);
        });
    }
};
