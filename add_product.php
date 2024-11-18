<?php
session_start();
include 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = $_POST['product'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];
    $fk_category = $_POST['fk_category'];

    // Consulta SQL para insertar el producto (ID_Product lo maneja el trigger)
    $query = "INSERT INTO products (Product, Description, Price, Qty, fk_category) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdii", $product, $description, $price, $qty, $fk_category);

    if ($stmt->execute()) {
        $success_message = "Producto agregado exitosamente.";
    } else {
        $error_message = "Error al agregar el producto: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener las categorías para el dropdown
$category_query = "SELECT ID_Category, Category_Name FROM categories";
$categories = $conn->query($category_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Producto</title>
    <link rel="stylesheet" href="css/styleback.css">
</head>
<body>
    <div class="container-centered">
        <h2>Añadir Nuevo Producto</h2>
        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Formulario para añadir producto -->
        <form action="add_product.php" method="post">
            <div class="form-group">
                <label for="product">Nombre del Producto:</label>
                <input type="text" id="product" name="product" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción:</label>
                <input type="text" id="description" name="description" required>
            </div>
            <div class="form-group">
                <label for="price">Precio:</label>
                <input type="number" step="0.01" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="qty">Cantidad:</label>
                <input type="number" id="qty" name="qty" required>
            </div>
            <div class="form-group">
                <label for="fk_category">Categoría:</label>
                <select id="fk_category" name="fk_category" required>
                    <option value="">Seleccione una categoría</option>
                    <?php while ($row = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $row['ID_Category']; ?>">
                            <?php echo htmlspecialchars($row['Category_Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn-submit">Añadir Producto</button>
            <a href="inventory.php" class="btn-back">Volver al Inventario</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
