<?php
session_start();
include 'config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['username']) || $_SESSION['role'] != 1) { // Solo el rol de administrador (1) puede acceder
    header("Location: login.php");
    exit;
}

// Manejar las acciones: agregar, editar, eliminar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_question'])) {
        $question_text = $_POST['question_text'];

        // Llamar al procedimiento almacenado para agregar una pregunta
        $query = "CALL AddSecurityQuestion(?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $question_text);
        $stmt->execute();
        $stmt->close();
        $success_message = "Pregunta agregada exitosamente.";
    }

    if (isset($_POST['edit_question'])) {
        $question_id = $_POST['question_id'];
        $question_text = $_POST['question_text'];

        // Llamar al procedimiento almacenado para actualizar la pregunta
        $query = "CALL UpdateSecurityQuestion(?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $question_id, $question_text);
        $stmt->execute();
        $stmt->close();
        $success_message = "Pregunta actualizada exitosamente.";
    }

    if (isset($_POST['delete_question'])) {
        $question_id = $_POST['question_id'];

        // Llamar al procedimiento almacenado para eliminar la pregunta
        $query = "CALL DeleteSecurityQuestion(?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->close();
        $success_message = "Pregunta eliminada exitosamente.";
    }
}

// Obtener todas las preguntas
$query = "SELECT * FROM security_questions";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Preguntas de Seguridad</title>
    <link rel="stylesheet" href="css/styleback.css">
</head>
<body>
    <div class="container-centered">
        <h2>Gestión de Preguntas de Seguridad</h2>

        <!-- Botón para regresar al Panel de Administración -->
        <a href="admin_dashboard.php" class="btn-back">Volver al Panel de Administración</a>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Formulario para agregar una nueva pregunta -->
        <form action="manage_questions.php" method="post">
            <div class="form-group">
                <label for="question_text">Nueva Pregunta:</label>
                <input type="text" id="question_text" name="question_text" required>
            </div>
            <button type="submit" name="add_question" class="btn-submit">Agregar Pregunta</button>
        </form>

        <h3>Lista de Preguntas de Seguridad</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pregunta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ID_Question']); ?></td>
                    <td><?php echo htmlspecialchars($row['Question_Text']); ?></td>
                    <td>
                        <!-- Formulario para editar una pregunta -->
                        <form action="manage_questions.php" method="post" style="display: inline-block;">
                            <input type="hidden" name="question_id" value="<?php echo $row['ID_Question']; ?>">
                            <input type="text" name="question_text" value="<?php echo htmlspecialchars($row['Question_Text']); ?>" required>
                            <button type="submit" name="edit_question" class="btn">Editar</button>
                        </form>

                        <!-- Formulario para eliminar una pregunta -->
                        <form action="manage_questions.php" method="post" style="display: inline-block;">
                            <input type="hidden" name="question_id" value="<?php echo $row['ID_Question']; ?>">
                            <button type="submit" name="delete_question" class="btn-back" onclick="return confirm('¿Estás seguro de eliminar esta pregunta?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
