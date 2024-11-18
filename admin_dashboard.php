<?php
session_start();
include 'config.php';

// Verificar si el usuario tiene rol de administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) { // El rol 1 corresponde al administrador
    header("Location: login.php");
    exit;
}

// Consultar la lista de usuarios
$query = "SELECT ID_Users, CONCAT(FirstName, ' ', LastName) AS FullName, Username FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/styleback.css"> <!-- Incluye tu archivo de estilos -->
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php">Dashboard de Usuarios</a></li>
                    <li><a href="inventory.php">Inventario</a></li>
                    <li><a href="logout.php" class="logout">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <div class="main-content">
            <h1>Gestión de Usuarios</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?> (Administrador)</p>

            <a href="add_user.php" class="btn">Añadir Nuevo Usuario</a>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Nombre de Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ID_Users']); ?></td>
                        <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                        <td><?php echo htmlspecialchars($row['Username']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['ID_Users']; ?>">Editar</a>
                            <a href="delete_user.php?id=<?php echo $row['ID_Users']; ?>" onclick="return confirm('¿Está seguro de querer eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
