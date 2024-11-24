<?php 
session_start();
include 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Inicializar la variable de búsqueda
$search_term = isset($_POST['search']) ? $_POST['search'] : '';

// Llamar al procedimiento almacenado para buscar productos
$query = "CALL SearchProductByName(?)";
$stmt = $conn->prepare($query);
$search_param = "%" . $search_term . "%"; // Usar comodines para búsqueda parcial
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

// Almacenar los resultados del procedimiento
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Liberar los resultados del procedimiento almacenado
$stmt->close();
$conn->next_result(); // Esto asegura que la conexión esté lista para la siguiente consulta

// Obtener las categorías para la tabla adicional
$category_query = "SELECT * FROM categories";
$category_result = $conn->query($category_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="css/styleback.css">
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
            <h2>Menú</h2>
            <nav>
                <ul>
                    <?php if ($_SESSION['role'] == 1): // Mostrar solo si el usuario es administrador ?>
                        <li><a href="admin_dashboard.php">Panel de Administrador</a></li>
                    <?php endif; ?>
                    <li><a href="inventory.php">Inventario</a></li>
                    <li><a href="configure_security_questions.php">Configurar Preguntas de Seguridad</a></li>
                    <li><a href="change_password.php">Cambiar Contraseña</a></li> <!-- Nuevo botón -->
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <div class="main-content" id="main-content">
            <h1 class="page-title">Inventario de Productos</h1>
            <p class="welcome-message">Usa las opciones de búsqueda para filtrar los productos.</p>

            <!-- Formulario de búsqueda -->
            <form action="inventory.php" method="post" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Buscar producto por nombre" 
                    value="<?php echo htmlspecialchars($search_term); ?>" 
                    class="search-input"
                >
                <button type="submit" class="btn btn-search">Buscar</button>
                <a href="inventory.php" class="btn btn-clear">Limpiar Búsqueda</a>
            </form>

            <!-- Botones generales -->
            <div class="button-container">
                <a href="add_product.php" class="btn">Añadir Nuevo Producto</a>
                <a href="add_category.php" class="btn">Añadir Nueva Categoría</a>
            </div>

            <!-- Tabla de productos -->
            <h2>Productos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['ID_Product']); ?></td>
                        <td><?php echo htmlspecialchars($product['Product']); ?></td>
                        <td><?php echo htmlspecialchars($product['Description']); ?></td>
                        <td><?php echo htmlspecialchars($product['Category']); ?></td>
                        <td>$<?php echo number_format($product['Price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['Qty']); ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $product['ID_Product']; ?>">Editar</a>
                            <a href="delete_product.php?id=<?php echo $product['ID_Product']; ?>" onclick="return confirm('¿Está seguro de querer eliminar este producto?');">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Tabla de categorías -->
            <h2>Categorías</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Categoría</th>
                        <th>Nombre de Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $category_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['ID_Category']); ?></td>
                        <td><?php echo htmlspecialchars($category['Category_Name']); ?></td>
                        <td>
                            <a href="edit_category.php?id=<?php echo $category['ID_Category']; ?>">Editar</a>
                            <a href="delete_category.php?id=<?php echo $category['ID_Category']; ?>" onclick="return confirm('¿Está seguro de querer eliminar esta categoría?');">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const header = document.querySelector('.header');
            
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('collapsed');
            header.classList.toggle('expanded');
        }
    </script>
</body>
</html>


<?php
$conn->close();
?>
