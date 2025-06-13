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
        $user = User::find(4);
        if (!$user) return;

        $categories = [
            // Pajamos
            ['name' => 'Alga', 'type' => 'income'],
            ['name' => 'Premija', 'type' => 'income'],
            ['name' => 'Dovanos', 'type' => 'income'],
            ['name' => 'Palūkanos', 'type' => 'income'],
            ['name' => 'Grąžinti pinigai', 'type' => 'income'],
            ['name' => 'Pajamos iš nuomos', 'type' => 'income'],
            ['name' => 'Pajamos iš lažybų', 'type' => 'income'],
            ['name' => 'Bonusai', 'type' => 'income'],
            ['name' => 'Verslo pajamos', 'type' => 'income'],
            ['name' => 'Dividentai', 'type' => 'income'],
            ['name' => 'Laimėjimas', 'type' => 'income'],
            ['name' => 'Grąžinimas iš pirkinių', 'type' => 'income'],
            ['name' => 'Stipendija', 'type' => 'income'],
            ['name' => 'Socialinė parama', 'type' => 'income'],
            ['name' => 'Pardavimai', 'type' => 'income'],
            ['name' => 'Akcijos grąža', 'type' => 'income'],
            ['name' => 'Už papildomą darbą', 'type' => 'income'],
            ['name' => 'Tėvų parama', 'type' => 'income'],
            ['name' => 'Freelance pajamos', 'type' => 'income'],
            ['name' => 'Youtube pajamos', 'type' => 'income'],

            // Išlaidos
            ['name' => 'Maistas', 'type' => 'expense'],
            ['name' => 'Kava', 'type' => 'expense'],
            ['name' => 'Nuoma', 'type' => 'expense'],
            ['name' => 'Transportas', 'type' => 'expense'],
            ['name' => 'Drabužiai', 'type' => 'expense'],
            ['name' => 'Telefonas', 'type' => 'expense'],
            ['name' => 'Internetas', 'type' => 'expense'],
            ['name' => 'Kinas', 'type' => 'expense'],
            ['name' => 'Dovanos kitiems', 'type' => 'expense'],
            ['name' => 'Kelionės', 'type' => 'expense'],
            ['name' => 'Sportas', 'type' => 'expense'],
            ['name' => 'Prenumeratos', 'type' => 'expense'],
            ['name' => 'Restoranai', 'type' => 'expense'],
            ['name' => 'Automobilis', 'type' => 'expense'],
            ['name' => 'Degalai', 'type' => 'expense'],
            ['name' => 'Vaistai', 'type' => 'expense'],
            ['name' => 'Mokslas', 'type' => 'expense'],
            ['name' => 'Skolos grąžinimas', 'type' => 'expense'],
            ['name' => 'Buitis', 'type' => 'expense'],
            ['name' => 'Kita', 'type' => 'expense'],
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
                'currency'    => 'EUR',
            ]);
        }
    }
}
