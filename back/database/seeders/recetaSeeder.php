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
        'role' => 'chef',
    ],
    [
        'name' => 'ias1',
        'email' => 'ias1@gmail.com',
        'password' => bcrypt('123456789'),
        'role' => 'chef',
    ],
    [
        'name' => 'sim',
        'email' => 'sim@gmail.com',
        'password' => bcrypt('123456789'),
        'role' => 'user',
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
        $user2 = $users->first();

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
                'user_id' => $user2->id,
                'category_id' => $categoryInstances[4]->id, // Postres
                'cuisine_id' => $cuisineInstances[0]->id, // Italia
                'title' => 'Tiramisu',
                'description' => 'Delicioso postre italiano de capas de bizcocho empapadas en café y crema de mascarpone.',
                'ingredients' => [
                    [
                        'quantity' => '300',
                        'unit' => 'g',
                        'name' => 'Bizcochos de soletilla'
                    ],
                    [
                        'quantity' => '500',
                        'unit' => 'g',
                        'name' => 'Queso mascarpone'
                    ],
                    [
                        'quantity' => '4',
                        'unit' => 'unitats',
                        'name' => 'Ous'
                    ],
                    [
                        'quantity' => '100',
                        'unit' => 'g',
                        'name' => 'Azúcar'
                    ],
                    [
                        'quantity' => '200',
                        'unit' => 'ml',
                        'name' => 'Café fuerte'
                    ],
                    [
                        'quantity' => '2',
                        'unit' => 'culleradetes',
                        'name' => 'Extracto de vainilla'
                    ],
                    [
                        'quantity' => '30',
                        'unit' => 'g',
                        'name' => 'Cacao en polvo'
                    ]
                ],
                'steps' => [
                    'Batir las yemas con el azúcar hasta obtener una mezcla cremosa.',
                    'Añadir el mascarpone a la mezcla de yemas y azúcar.',
                    'Montar las claras a punto de nieve y mezclar con la crema de mascarpone.',
                    'Sumergir los bizcochos en el café con extracto de vainilla.',
                    'Colocar una capa de bizcochos empapados en el fondo de un recipiente.',
                    'Cubrir con una capa de la mezcla de mascarpone.',
                    'Repetir las capas hasta terminar los ingredientes.',
                    'Dejar reposar en la nevera durante al menos 4 horas.',
                    'Espolvorear con cacao en polvo antes de servir.'
                ],
                'nutrition' => ['Calories' => '350', 'Protein' => '8g'],
                'image' => 'https://res.cloudinary.com/dt5vjbgab/image/upload/v1745825570/mulxnnw9tm6bhg5grl0f.jpg',
                'video' => 'https://res.cloudinary.com/dt5vjbgab/video/upload/v1745825740/abgohb7pxjiva2t6cptd.mp4',
                'prep_time' => 20,
                'cook_time' => 0,
                'servings' => 6,
            ],
            [
                'user_id' => $user2->id,
                'category_id' => $categoryInstances[4]->id, // Postres
                'cuisine_id' => $cuisineInstances[0]->id, // Italia
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
            ]
        ];

        foreach ($recipes as $recipeData) {
            Recipe::create($recipeData);
        }
    }
}
