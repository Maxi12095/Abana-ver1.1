<?php
session_start();
include 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Manejar el formulario de añadir categoría
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];

    // Insertar la categoría (ID_Category lo maneja el trigger)
    $query = "INSERT INTO categories (Category_Name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {
        $success_message = "Categoría agregada exitosamente.";
    } else {
        $error_message = "Error al agregar la categoría: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Categoría</title>
    <link rel="stylesheet" href="css/styleback.css">
</head>
<body>
    <div class="container-centered">
        <h2>Añadir Nueva Categoría</h2>
        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Formulario para añadir categoría -->
        <form action="add_category.php" method="post">
            <div class="form-group">
                <label for="category_name">Nombre de la Categoría:</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <button type="submit" class="btn-submit">Añadir Categoría</button>
            <a href="inventory.php" class="btn-back">Volver al Inventario</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
