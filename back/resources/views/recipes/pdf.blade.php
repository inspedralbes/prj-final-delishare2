<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $recipe['title'] }}</title>
    <style>
        :root {
            --primary-color: #e74c3c;
            --secondary-color: #2c3e50;
            --accent-color: #f39c12;
            --light-bg: #f9f9f9;
            --text-color: #333;
            --border-radius: 8px;
            --box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
        }
        
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--accent-color);
            font-size: 2.5rem;
        }
        
        h2 {
            color: var(--secondary-color);
            margin-top: 30px;
            font-size: 1.8rem;
            position: relative;
            padding-left: 15px;
        }
        
        h2:before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 70%;
            width: 5px;
            background-color: var(--accent-color);
            border-radius: 5px;
        }
        
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .metadata {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .metadata p {
            margin: 5px 0;
            display: flex;
            align-items: center;
        }
        
        .metadata strong {
            color: var(--secondary-color);
            margin-right: 8px;
            min-width: 100px;
            display: inline-block;
        }
        
        .ingredient-list, .step-list {
            margin-left: 20px;
            padding-left: 10px;
        }
        
        .ingredient-list li {
            margin-bottom: 8px;
            position: relative;
            padding-left: 25px;
            list-style-type: none;
        }
        
        .ingredient-list li:before {
            content: "•";
            color: var(--primary-color);
            font-size: 1.5rem;
            position: absolute;
            left: 0;
            top: -3px;
        }
        
        .step-list li {
            margin-bottom: 15px;
            padding-left: 10px;
            counter-increment: step-counter;
        }
        
        .step-list li:before {
            content: counter(step-counter);
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 50%;
            margin-right: 10px;
            font-size: 0.9rem;
        }
        
        .recipe-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
            border: 1px solid #ddd;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .metadata {
                grid-template-columns: 1fr;
            }
            
            .recipe-image {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <h1>{{ $recipe['title'] }}</h1>

    <div class="section metadata">
        <p><strong>Creador:</strong> {{ $metadata['creador'] }}</p>
        <p><strong>Categoría:</strong> {{ $metadata['categoria'] }}</p>
        <p><strong>Tipo de cocina:</strong> {{ $metadata['tipo_cocina'] }}</p>
        <p><strong>Tiempo total:</strong> {{ $recipe['total_time'] }} minutos</p>
        <p><strong>Porciones:</strong> {{ $recipe['servings'] }}</p>
    </div>

    <div class="section">
        <h2>Descripción</h2>
        <p>{{ $recipe['description'] }}</p>
    </div>

    <div class="section">
        <h2>Ingredientes</h2>
        <ul class="ingredient-list">
            @foreach($recipe['ingredients'] as $ingredient)
                <li>{{ is_array($ingredient) ? $ingredient['name'] : $ingredient }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>Pasos</h2>
        <ol class="step-list">
            @foreach($recipe['steps'] as $step)
                <li>{{ is_array($step) ? $step['description'] : $step }}</li>
            @endforeach
        </ol>
    </div>
</body>
</html>