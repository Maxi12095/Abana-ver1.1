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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleback.css">
</head>
<body>
    <!-- Botón para alternar Sidebar -->
    <button class="btn btn-dark toggle-sidebar-btn" onclick="toggleSidebar()">☰</button>

    <!-- Contenedor principal -->
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-2 d-md-block sidebar">
            <div class="position-sticky pt-3">
                <h2 class="text-center">Menú</h2>
                <ul class="nav flex-column">
                    <?php if ($_SESSION['role'] == 1): // Mostrar solo si el usuario es administrador ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Panel de Administrador</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php">Inventario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="configure_security_questions.php">Configurar Preguntas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="change_password.php">Cambiar Contraseña</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenido principal -->
        <div id="main-content" class="main-content">
            <!-- Header -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Inventario</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <span class="nav-link">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-danger btn-sm text-white" href="logout.php">Cerrar Sesión</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Contenido -->
            <main class="container-fluid mt-4">
                <div class="card mb-4">
                    <div class="card-body">
                    <h1 class="h2">Busqueda</h1>
                    <br>
                        <!-- Formulario de búsqueda -->
                        <form class="row g-3" method="post" action="inventory.php">
                            <div class="col-md-8">
                                <input 
                                    type="text" 
                                    name="search" 
                                    class="form-control" 
                                    placeholder="Buscar producto por nombre" 
                                    value="<?php echo htmlspecialchars($search_term); ?>">
                            </div>
                            <div class="col-md-4 d-flex">
                                <button type="submit" class="btn btn-primary me-2">Buscar</button>
                                <a href="inventory.php" class="btn btn-secondary">Limpiar Búsqueda</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mb-4">
                    <a href="add_product.php" class="btn btn-success me-2">Añadir Nuevo Producto</a>
                    <a href="add_category.php" class="btn btn-info">Añadir Nueva Categoría</a>
                </div>

                <!-- Tabla de productos -->
                <h2>Productos</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
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
                                    <a href="edit_product.php?id=<?php echo $product['ID_Product']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="delete_product.php?id=<?php echo $product['ID_Product']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de querer eliminar este producto?');">Eliminar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tabla de categorías -->
                <h2>Categorías</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
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
                                    <a href="edit_category.php?id=<?php echo $category['ID_Category']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="delete_category.php?id=<?php echo $category['ID_Category']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de querer eliminar esta categoría?');">Eliminar</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="js/slide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
