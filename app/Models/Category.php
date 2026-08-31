<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'user_id',
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
     * Get the user that owns the custom category.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include expense categories.
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', CategoryType::EXPENSE->value);
    }

    /**
     * Scope a query to only include income categories.
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', CategoryType::INCOME->value);
    }

    /**
     * Scope a query to include system global categories and user's custom categories.
     */
    public function scopeForUserOrGlobal(Builder $query, ?int $userId): Builder
    {
        if ($userId) {
            return $query->where(function (Builder $q) use ($userId) {
                $q->whereNull('user_id')->orWhere('user_id', $userId);
            });
        }

        return $query->whereNull('user_id');
    }

    /**
     * Get the transactions for the category.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
