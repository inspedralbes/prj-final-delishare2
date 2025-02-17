<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cuisine;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;

class RecetaSeeder extends Seeder
{
    public function run()
    {
        // Crear usuarios
        $users = [
            ['name' => 'ishaa', 'email' => 'ishaa@gmail.com', 'password' => bcrypt('123456789')],
            ['name' => 'sim', 'email' => 'sim@gmail.com', 'password' => bcrypt('123456789')],
            ['name' => 'prueba', 'email' => 'prueba2@gmail.com', 'password' => bcrypt('123456789')],
        ];
        foreach ($users as $user) {
            User::create($user);
        }

        // Crear categorías y cocinas
        $categories = [
            'Peix', 'Entrants', 'Amanidas', 'Begudes', 'Postres'
        ];
        $categoryInstances = [];
        foreach ($categories as $category) {
            $categoryInstances[] = Category::create(['name' => $category]);
        }

        $cuisines = [
            'Italia', 'Mèxic', 'Índia', 'Espanya', 'França'
        ];
        $cuisineInstances = [];
        foreach ($cuisines as $cuisine) {
            $cuisineInstances[] = Cuisine::create(['country' => $cuisine]);
        }

        // Obtener usuarios
        $user1 = User::first();
        $user2 = User::skip(1)->first();

        // Crear recetas asegurando IDs existentes y guardando JSON correctamente
        $recipes = [
            [
                'user_id' => $user1->id,
                'category_id' => $categoryInstances[0]->id,
                'cuisine_id' => $cuisineInstances[0]->id,
                'title' => 'Spaghetti Carbonara',
                'description' => 'A classic Italian pasta dish with eggs, cheese, pancetta, and pepper.',
                'ingredients' => ['Spaghetti', 'Eggs', 'Pancetta', 'Parmesan cheese', 'Black pepper'],
                'steps' =>  ['Boil pasta', 'Fry pancetta', 'Mix eggs and cheese', 'Combine all ingredients'],
                'nutrition' => 'Calories: 500, Protein: 25g',
                'image' => 'https://res.cloudinary.com/dt5vjbgab/image/upload/v1739360021/w6iq0ifdqml2igbqcwqp.jpg',
                'prep_time' => 10,
                'cook_time' => 15,
                'servings' => 2,
            ],
            [
                'user_id' => $user2->id,
                'category_id' => $categoryInstances[1]->id,
                'cuisine_id' => $cuisineInstances[1]->id,
                'title' => 'Tacos al Pastor',
                'description' => 'Mexican tacos with marinated pork, pineapple, and cilantro.',
                'ingredients' =>  ['Pork', 'Pineapple', 'Cilantro', 'Onions', 'Taco tortillas'],
                'steps' =>  ['Marinate pork', 'Grill with pineapple', 'Serve in tortillas with cilantro and onions'],
                'nutrition' => 'Calories: 300, Protein: 20g',
                'image' => 'https://res.cloudinary.com/dt5vjbgab/image/upload/v1739360221/ohnvffg0xbau7uyup0bt.jpg',
                'prep_time' => 15,
                'cook_time' => 20,
                'servings' => 4,
            ]
        ];

        foreach ($recipes as $recipe) {
            Recipe::create($recipe);
        }
    }
}