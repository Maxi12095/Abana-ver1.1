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
            <h1>Inventario de Productos</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

            <!-- Formulario de búsqueda -->
            <form action="inventory.php" method="post" style="margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Buscar producto por nombre" value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn-submit">Buscar</button>
                <a href="inventory.php" class="btn-clear">Limpiar Búsqueda</a>
            </form>

            <!-- Botón de añadir producto -->
            <a href="add_product.php" class="btn">Añadir Nuevo Producto</a>

            <!-- Botón de añadir categoría -->
            <a href="add_category.php" class="btn">Añadir Nueva Categoría</a>

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
</body>
</html>

<?php
$conn->close();
?>
