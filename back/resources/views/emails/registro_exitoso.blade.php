<!DOCTYPE html>
<html>
<head>
    <title>Registro Exitoso en Delishare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Registro Exitoso en Delishare!</h1>
    </div>
    
    <div class="content">
        <h2>Hola {{ $user->name }},</h2>
        
        <p>¡Gracias por registrarte en Delishare! Estamos emocionados de tenerte como parte de nuestra comunidad.</p>
        
        <p>Aquí están tus datos de registro:</p>
        <ul>
            <li><strong>Nombre de usuario:</strong> {{ $user->name }}</li>
            <li><strong>Correo electrónico:</strong> {{ $user->email }}</li>
        </ul>
        
        <p>Ya puedes comenzar a usar nuestra plataforma para compartir y descubrir recetas deliciosas.</p>
        
        <p>¡Esperamos que disfrutes de Delishare!</p>
    </div>
    
    <div class="footer">
        <p>&copy; 2025 Delishare. Todos los derechos reservados.</p>
        <p>Este es un correo automático, por favor no responda a este mensaje.</p>
    </div>
</body>
</html>