<?php
session_start();
include 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Obtener el ID del producto desde el parámetro GET
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header("Location: inventory.php");
    exit;
}

// Manejar la actualización del producto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = $_POST['product'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];
    $fk_category = $_POST['fk_category'];

    // Consulta SQL para actualizar el producto
    $query = "UPDATE products SET Product = ?, Description = ?, Price = ?, Qty = ?, fk_category = ? WHERE ID_Product = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdiii", $product, $description, $price, $qty, $fk_category, $product_id);

    if ($stmt->execute()) {
        $success_message = "Producto actualizado exitosamente.";
    } else {
        $error_message = "Error al actualizar el producto: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener los datos del producto
$query = "SELECT * FROM products WHERE ID_Product = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Obtener las categorías para el dropdown
$category_query = "SELECT ID_Category, Category_Name FROM categories";
$categories = $conn->query($category_query);

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="css/styleback.css">
</head>
<body>
    <div class="container-centered">
        <h2>Editar Producto</h2>
        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Formulario para editar producto -->
        <form action="edit_product.php?id=<?php echo $product_id; ?>" method="post">
            <div class="form-group">
                <label for="product">Nombre del Producto:</label>
                <input type="text" id="product" name="product" value="<?php echo htmlspecialchars($product['Product']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción:</label>
                <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($product['Description']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Precio:</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['Price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="qty">Cantidad:</label>
                <input type="number" id="qty" name="qty" value="<?php echo htmlspecialchars($product['Qty']); ?>" required>
            </div>
            <div class="form-group">
                <label for="fk_category">Categoría:</label>
                <select id="fk_category" name="fk_category" required>
                    <?php while ($row = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $row['ID_Category']; ?>" <?php echo $row['ID_Category'] == $product['fk_category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['Category_Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn-submit">Guardar Cambios</button>
            <a href="inventory.php" class="btn-back">Volver al Inventario</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
