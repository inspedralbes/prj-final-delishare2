<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $recipe['title'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #2c3e50; }
        .section { margin-bottom: 20px; }
        .ingredient-list, .step-list { margin-left: 20px; }
        .metadata { background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .recipe-image { max-width: 100%; height: auto; margin-bottom: 20px; border-radius: 10px; }
    </style>
</head>
<body>
    <h1>{{ $recipe['title'] }}</h1>

    @if(!empty($recipe['image']))
        <div class="section">
            <img class="recipe-image" src="{{ $recipe['image'] }}" alt="Imagen de la receta">
        </div>
    @endif

    <div class="section metadata">
        <p><strong>Creador:</strong> {{ $metadata['creador'] }}</p>
        <p><strong>Categoría:</strong> {{ $metadata['categoria'] }}</p>
        <p><strong>Tipo de cocina:</strong> {{ $metadata['tipo_cocina'] }}</p>
        <p><strong>Tiempo total:</strong> {{ $recipe['total_time'] }} minutos</p>
        <p><strong>Porciones:</strong> {{ $recipe['servings'] }}</p>
        <p><strong>Likes:</strong> {{ $metadata['likes_count'] }}</p>
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

    @if(!empty($comments))
        <div class="section">
            <h2>Comentarios ({{ count($comments) }})</h2>
            @foreach($comments as $comment)
                <p><strong>{{ $comment->name }}:</strong> {{ $comment->comment }}</p>
            @endforeach
        </div>
    @endif
</body>
</html>
