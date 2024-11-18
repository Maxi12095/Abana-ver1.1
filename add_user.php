<?php
session_start();
include 'config.php';

// Verificar si el usuario tiene rol de administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hasheamos la contraseña
    $role = $_POST['role'];

    // Llamar al proceso almacenado AddUser
    $stmt = $conn->prepare("CALL AddUser(?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $firstname, $lastname, $username, $password, $role);

    if ($stmt->execute()) {
        $success_message = "Usuario creado exitosamente.";
    } else {
        $error_message = "Error al crear el usuario: " . $stmt->error;
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
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="css/styleback.css"> <!-- Asegúrate de que este archivo CSS esté presente -->
</head>
<body>
    <div class="container-centered">
        <h2>Agregar Usuario</h2>
        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <!-- Formulario de registro -->
        <form action="add_user.php" method="post">
            <div class="form-group">
                <label for="firstname">Nombre:</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">Apellido:</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rol:</label>
                <select id="role" name="role" required>
                    <option value="1">Administrador</option>
                    <option value="2">Empleado</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Crear Usuario</button>
            <a href="admin_dashboard.php" class="btn-back">Volver</a>
        </form>
    </div>
</body>
</html>
