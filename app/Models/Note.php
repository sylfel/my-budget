<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

#[Appends(['year_month'])]
class Note extends Model
{
    use HasFactory;
    use HasTags;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['label', 'price', 'year', 'month', 'category_id', 'poste_id', 'user_id'];

    protected $casts = [
        'price' => MoneyCast::class,
    ];

    /**
     * Get the year/month concat
     */
 /**
     * Determine if the user is an administrator.
     */
    protected function yearMonth(): Attribute
    {
        return new Attribute(
            get: fn ($_, $attributes) => sprintf('%04d%02d', $attributes['year'], $attributes['month']),
        );
    }

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
