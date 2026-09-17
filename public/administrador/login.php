<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos_admin.css">
    <title>SACAM - Acceso administrador</title>
</head>
<body>
    <h1>Acceso administrador</h1>
    <form method="post" action="autenticar_admin.php">
        <div>
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" required>
        </div>
        <div>
            <label for="clave">Contraseña</label>
            <input type="password" id="clave" name="clave" required>
        </div>
        <?php if ($error !== ''): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <button type="submit">Entrar</button>
    </form>
    <p><a href="../index.php">Volver al inicio</a></p>
</body>
</html>