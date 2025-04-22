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
     // Crear usuarios con diferentes roles
$users = [
    [
        'name' => 'ias',
        'email' => 'ishaa@gmail.com',
        'password' => bcrypt('123456789'),
        'role' => 'user',
    ],
    [
        'name' => 'sim',
        'email' => 'sim@gmail.com',
        'password' => bcrypt('123456789'),
        'role' => 'chef',
    ],
    [
        'name' => 'prueba',
        'email' => 'prueba2@gmail.com',
        'password' => bcrypt('123456789'),
        'role' => 'admin',
    ],
];

// Insertar usuarios
foreach ($users as $userData) {
    \App\Models\User::create($userData);
}


        // Obtener los usuarios
        $users = User::all();
        $user1 = $users->first();
        $user2 = $users->skip(1)->first() ?? $user1;

        // Crear categorías y cocinas
        $categories = ['Peix', 'Entrants', 'Amanidas', 'Begudes', 'Postres'];
        $categoryInstances = [];
        foreach ($categories as $category) {
            $categoryInstances[] = Category::firstOrCreate(['name' => $category]);
        }

        $cuisines = ['Italia', 'Mèxic', 'Índia', 'Espanya', 'França'];
        $cuisineInstances = [];
        foreach ($cuisines as $cuisine) {
            $cuisineInstances[] = Cuisine::firstOrCreate(['country' => $cuisine]);
        }

        // Crear recetas con arrays directamente (sin json_encode)
        $recipes = [
            [
                'user_id' => $user1->id,
                'category_id' => $categoryInstances[0]->id,
                'cuisine_id' => $cuisineInstances[0]->id,
                'title' => 'Spaghetti Carbonara',
                'description' => 'A classic Italian pasta dish with eggs, cheese, pancetta, and pepper.',
                'ingredients' => [
                    [
                        'quantity' => '200',
                        'unit' => 'g',
                        'name' => 'Spaghetti'
                    ],
                    [
                        'quantity' => '2',
                        'unit' => 'unitats',
                        'name' => 'Ous'
                    ],
                    [
                        'quantity' => '100',
                        'unit' => 'g',
                        'name' => 'Pancetta'
                    ],
                    [
                        'quantity' => '50',
                        'unit' => 'g',
                        'name' => 'Formatge parmesà'
                    ],
                    [
                        'quantity' => '1',
                        'unit' => 'culleradeta',
                        'name' => 'Pebre negre'
                    ]
                ],
                'steps' => [
                    'Boil pasta', 
                    'Fry pancetta', 
                    'Mix eggs and cheese', 
                    'Combine all ingredients'
                ],
                'nutrition' => ['Calories' => '500', 'Protein' => '25g'],
                'image' => 'https://res.cloudinary.com/dt5vjbgab/image/upload/v1739360021/w6iq0ifdqml2igbqcwqp.jpg',
                'video' => 'https://res.cloudinary.com/dt5vjbgab/video/upload/v1743764405/n9zv48fpmb9rz1zz2hwq.mp4',
                'prep_time' => 10,
                'cook_time' => 15,
                'servings' => 2,
            ],
            [
                'user_id' => $user1->id,
                'category_id' => $categoryInstances[4]->id, // Postres
                'cuisine_id' => $cuisineInstances[2]->id, // Índia
                'title' => 'Lassi de Mango',
                'description' => 'Refrescante bebida india de yogur y mango.',
                'ingredients' => [
                    [
                        'quantity' => '200',
                        'unit' => 'ml',
                        'name' => 'Iogurt natural'
                    ],
                    [
                        'quantity' => '100',
                        'unit' => 'ml',
                        'name' => 'Aigua'
                    ],
                    [
                        'quantity' => '1',
                        'unit' => 'cullerada',
                        'name' => 'Sucre moreno'
                    ],
                    [
                        'quantity' => '0.5',
                        'unit' => 'culleradeta',
                        'name' => 'Cardamom en pols'
                    ],
                    [
                        'quantity' => '0.25',
                        'unit' => 'culleradeta',
                        'name' => 'Canela en pols'
                    ]
                ],
                'steps' => [
                    'Mezclar todos los ingredientes en la licuadora',
                    'Licuar hasta obtener una mezcla homogénea',
                    'Servir frío'
                ],
                'nutrition' => ['Calories' => '150', 'Protein' => '5g'],
                'image' => 'https://res.cloudinary.com/dt5vjbgab/image/upload/v1743763266/l2myknmc5vhmx3dzdhn4.jpg',
                'video' => 'https://res.cloudinary.com/dt5vjbgab/video/upload/v1743764249/bn5bz3ulcvjt07dm50is.mp4',
                'prep_time' => 5,
                'cook_time' => 0,
                'servings' => 2,
            ]
        ];

        foreach ($recipes as $recipeData) {
            Recipe::create($recipeData);
        }
    }
}