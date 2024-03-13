<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['label', 'credit', 'extra'];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function postes(): HasMany
    {
        return $this->hasMany(Poste::class);
    }
}
