<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Note extends Model
{
    use HasFactory;
    use HasTags;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['label', 'price', 'year', 'month', 'category_id', 'poste_id', 'user_id'];

    protected $casts = [
        'price' => MoneyCast::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
