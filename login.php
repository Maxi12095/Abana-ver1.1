<?php
session_start();
include 'config.php';  // Incluye la configuración de la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Llamar al proceso almacenado UserLogin
    $stmt = $conn->prepare("CALL UserLogin(?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verificar la contraseña hasheada
        if (password_verify($password, $row['Password'])) {
            // Credenciales correctas
            $_SESSION['user_id'] = $row['ID_Users'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['role'] = $row['FK_Role'];  // Rol del usuario

            // Redirección basada en el rol
            if ($_SESSION['role'] == 1) {  // Administrador
                header("Location: admin_dashboard.php");
            } else if ($_SESSION['role'] == 2) {  // Empleado
                header("Location: inventory.php");
            }
            exit;
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario no encontrado.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styleback.css"> <!-- Asegúrate de que el archivo CSS esté presente -->

    <link rel="icon" href="favicon.ico">
</head>
<body>
    <div class="container-centered">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
