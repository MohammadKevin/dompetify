<?php

namespace App\Models;

use App\Enums\CategoryType;
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
    protected $fillable = [
        'name',
        'type',
        'icon',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => CategoryType::class,
        ];
    }

    /**
     * Scope a query to only include expense categories.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', CategoryType::EXPENSE->value);
    }

    /**
     * Scope a query to only include income categories.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', CategoryType::INCOME->value);
    }

    /**
     * Get the transactions for the category.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
