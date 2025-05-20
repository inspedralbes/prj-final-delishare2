<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Solicitud de Verificación</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #333;">📩 Solicitud de verificación de usuario</h2>

        <p><strong>🧑 Usuario:</strong> {{ $username }}</p>
     

        <hr style="margin: 20px 0;">

        <p><strong>📜 Mensaje del usuario:</strong></p>
        <div style="background-color: #f0f0f0; padding: 15px; border-radius: 5px; font-style: italic;">
            {{ $messageContent }}
        </div>

        <p style="margin-top: 30px; font-size: 12px; color: #888;">
            Este mensaje fue generado automáticamente desde el sistema de verificación de usuarios.
        </p>
    </div>
</body>
</html>
