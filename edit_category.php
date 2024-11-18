<?php
session_start();
include 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Obtener el ID de la categoría
$category_id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];

    // Actualizar la categoría en la base de datos
    $query = "UPDATE categories SET Category_Name = ? WHERE ID_Category = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $category_name, $category_id);

    if ($stmt->execute()) {
        $success_message = "Categoría actualizada exitosamente.";
    } else {
        $error_message = "Error al actualizar la categoría: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener los datos de la categoría
$query = "SELECT * FROM categories WHERE ID_Category = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoría</title>
    <link rel="stylesheet" href="css/styleback.css">
</head>
<body>
    <div class="container-centered">
        <h2>Editar Categoría</h2>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="edit_category.php?id=<?php echo $category_id; ?>" method="post">
            <div class="form-group">
                <label for="category_name">Nombre de la Categoría:</label>
                <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['Category_Name']); ?>" required>
            </div>
            <button type="submit" class="btn-submit">Guardar Cambios</button>
            <a href="inventory.php" class="btn-back">Volver al Inventario</a>
        </form>
    </div>
</body>
</html>
