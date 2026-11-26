<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user  = User::where('role', 'user')->first();
        $category = Category::first();

        Item::create([
            'user_id'       => $admin->id,
            'category_id'   => $category->id,
            'name'          => 'Tenda Dome Kapasitas 4 Orang',
            'description'   => 'Tenda double layer tahan hujan deras dengan frame alloy.',
            'stock'         => 10,
            'price_per_day' => 50000,
        ]);

        Item::create([
            'user_id'       => $user->id,
            'category_id'   => $category->id,
            'name'          => 'Carrier 60L + Raincover',
            'description'   => 'Tas gunung ergonomis dengan backsystem nyaman.',
            'stock'         => 5,
            'price_per_day' => 35000,
        ]);
    }
}