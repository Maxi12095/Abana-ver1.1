<?php
session_start();
include 'config.php';

// Verificar si el usuario tiene rol de administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) { // El rol 1 corresponde al administrador
    header("Location: login.php");
    exit;
}

// Llamar al procedimiento almacenado para obtener usuarios
$stmt = $conn->prepare("CALL GetUsers()");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <script src="js/slide.js"></script>
    <link rel="stylesheet" href="css/styleback.css"> <!-- Incluye tu archivo de estilos -->
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="user-info">
                Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </div>
            <div class="logout-container">
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Botón para mostrar/ocultar la Sidebar -->
    <button class="toggle-sidebar-btn" onclick="toggleSidebar()">☰</button>

    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php">Dashboard de Usuarios</a></li>
                    <li><a href="inventory.php">Inventario</a></li>
                    <li><a href="manage_questions.php">Gestión de Preguntas</a></li>
                    <li><a href="configure_security_questions.php">Configurar Preguntas de Seguridad</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <div class="main-content" id="main-content">
            <h1 class="page-title">Gestión de Usuarios</h1>
            <p class="welcome-message">Usa las opciones para gestionar los usuarios del sistema.</p>

            <!-- Botón de añadir usuario -->
            <a href="add_user.php" class="btn">Añadir Nuevo Usuario</a>

            <!-- Tabla de usuarios -->
            <h2>Usuarios Registrados</h2>
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
                            <a href="edit_user.php?id=<?php echo $row['ID_Users']; ?>" class="btn-edit">Editar</a>
                            <a href="delete_user.php?id=<?php echo $row['ID_Users']; ?>" class="btn-delete" onclick="return confirm('¿Está seguro de querer eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript para la Sidebar -->
   
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
