<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Expense Categories
            [
                'name' => 'Makanan & Minuman',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'restaurant',
            ],
            [
                'name' => 'Transportasi',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'directions_car',
            ],
            [
                'name' => 'Belanja & Kebutuhan',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'shopping_cart',
            ],
            [
                'name' => 'Tagihan & Utilitas',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'receipt_long',
            ],
            [
                'name' => 'Hiburan & Rekreasi',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'sports_esports',
            ],
            [
                'name' => 'Kesehatan & Medis',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'medical_services',
            ],
            [
                'name' => 'Pendidikan',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'school',
            ],
            [
                'name' => 'Keluarga & Donasi',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'volunteer_activism',
            ],
            [
                'name' => 'Lain-lain Pengeluaran',
                'type' => CategoryType::EXPENSE->value,
                'icon' => 'more_horiz',
            ],

            // Income Categories
            [
                'name' => 'Gaji Pokok',
                'type' => CategoryType::INCOME->value,
                'icon' => 'payments',
            ],
            [
                'name' => 'Bonus & THR',
                'type' => CategoryType::INCOME->value,
                'icon' => 'redeem',
            ],
            [
                'name' => 'Hasil Investasi',
                'type' => CategoryType::INCOME->value,
                'icon' => 'trending_up',
            ],
            [
                'name' => 'Freelance & Bisnis',
                'type' => CategoryType::INCOME->value,
                'icon' => 'business_center',
            ],
            [
                'name' => 'Hadiah & Cashback',
                'type' => CategoryType::INCOME->value,
                'icon' => 'card_giftcard',
            ],
            [
                'name' => 'Lain-lain Pemasukan',
                'type' => CategoryType::INCOME->value,
                'icon' => 'add_circle',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']],
                $cat
            );
        }
    }
}
