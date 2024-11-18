<?php
session_start();
include 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['role'] != 1) { // Suponiendo que el rol de administrador es 1
    header("Location: inventory.php");
    exit;
}

// Obtener el ID del usuario desde el parámetro GET
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: admin_dashboard.php");
    exit;
}

// Manejar la actualización del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashear la contraseña
    $role_id = $_POST['role'];

    // Llamar al procedimiento almacenado para editar al usuario
    $query = "CALL EditUser(?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssi", $user_id, $first_name, $last_name, $username, $password, $role_id);

    if ($stmt->execute()) {
        $success_message = "Usuario actualizado exitosamente.";
    } else {
        $error_message = "Error al actualizar el usuario: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener los datos del usuario
$query = "SELECT * FROM users WHERE ID_Users = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="css/styleback.css">
</head>
<body>
    <div class="container-centered">
        <h2>Editar Usuario</h2>
        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Formulario para editar usuario -->
        <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
            <div class="form-group">
                <label for="first_name">Nombre:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['FirstName']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Apellido:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['LastName']); ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rol:</label>
                <select id="role" name="role" required>
                    <option value="1" <?php echo $user['FK_Role'] == 1 ? 'selected' : ''; ?>>Administrador</option>
                    <option value="2" <?php echo $user['FK_Role'] == 2 ? 'selected' : ''; ?>>Empleado</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Guardar Cambios</button>
            <a href="admin_dashboard.php" class="btn-back">Volver al Dashboard</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
