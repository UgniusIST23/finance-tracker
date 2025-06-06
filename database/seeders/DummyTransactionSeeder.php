<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Carbon;

class DummyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(2);
        if (!$user) return;

        $categories = [
            ['name' => 'Alga', 'type' => 'income'],
            ['name' => 'Premija', 'type' => 'income'],
            ['name' => 'Maistas', 'type' => 'expense'],
            ['name' => 'Nuoma', 'type' => 'expense'],
            ['name' => 'Transportas', 'type' => 'expense'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate([
                'name' => $cat['name'],
                'type' => $cat['type'],
                'user_id' => $user->id,
            ]);
        }

        $allCategories = Category::where('user_id', $user->id)->get();

        for ($i = 0; $i < 100; $i++) {
            $category = $allCategories->random();
            Transaction::create([
                'user_id'     => $user->id,
                'category_id' => $category->id,
                'amount'      => $category->type === 'income'
                    ? rand(500, 3000)
                    : rand(10, 800),
                'date'        => Carbon::now()->subDays(rand(0, 90)),
                'currency'    => 'EUR', // ← Reikalingas laukas
            ]);
        }
    }
}
