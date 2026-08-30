<?php

use App\Models\Category;
use App\Models\Poste;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('price');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('label')->nullable();
            $table->foreignIdFor(Category::class);
            $table->foreignIdFor(Poste::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
