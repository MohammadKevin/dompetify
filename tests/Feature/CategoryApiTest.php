<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_filter_categories(): void
    {
        Category::create(['name' => 'Makanan', 'type' => CategoryType::EXPENSE]);
        Category::create(['name' => 'Transportasi', 'type' => CategoryType::EXPENSE]);
        Category::create(['name' => 'Gaji', 'type' => CategoryType::INCOME]);

        $responseAll = $this->getJson('/api/categories');
        $responseAll->assertStatus(200)
            ->assertJsonPath('meta.total_categories', 3)
            ->assertJsonPath('meta.expense_count', 2)
            ->assertJsonPath('meta.income_count', 1);

        $responseExpenseOnly = $this->getJson('/api/categories?type=EXPENSE');
        $responseExpenseOnly->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Investasi Reksadana',
            'type' => 'INCOME',
            'icon' => 'trending_up',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Investasi Reksadana');

        $this->assertDatabaseHas('categories', [
            'name' => 'Investasi Reksadana',
            'type' => 'INCOME',
        ]);
    }

    public function test_can_update_and_delete_category(): void
    {
        $category = Category::create(['name' => 'Game', 'type' => CategoryType::EXPENSE]);

        $update = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Hiburan & Game',
        ]);

        $update->assertStatus(200)
            ->assertJsonPath('data.name', 'Hiburan & Game');

        $delete = $this->deleteJson("/api/categories/{$category->id}");
        $delete->assertStatus(200);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
