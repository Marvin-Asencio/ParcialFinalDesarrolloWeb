<?php
require "conexion.php";

$errores = [];
$codigo = "";
$nombre = "";
$tipo = "";
$descripcion = "";
$ubicacion = "";
$estado = "Disponible";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo = trim($_POST["codigo"] ?? "");
    $nombre = trim($_POST["nombre"] ?? "");
    $tipo = $_POST["tipo"] ?? "";
    $descripcion = trim($_POST["descripcion"] ?? "");
    $ubicacion = trim($_POST["ubicacion"] ?? "");
    $estado = $_POST["estado"] ?? "";

    if ($codigo === "" || strlen($codigo) > 20) {
        $errores[] = "El código es obligatorio (máximo 20 caracteres).";
    }
    if ($nombre === "" || strlen($nombre) > 100) {
        $errores[] = "El nombre es obligatorio (máximo 100 caracteres).";
    }
    if (!in_array($tipo, $tipos, true)) {
        $errores[] = "Selecciona un tipo válido.";
    }
    if (strlen($descripcion) > 255) {
        $errores[] = "La descripción no puede pasar de 255 caracteres.";
    }
    if ($ubicacion === "" || strlen($ubicacion) > 100) {
        $errores[] = "La ubicación es obligatoria (máximo 100 caracteres).";
    }
    if (!in_array($estado, $estados, true)) {
        $errores[] = "Selecciona un estado válido.";
    }

    if (empty($errores)) {
        try {
            $sql = "INSERT INTO equipos (codigo, nombre, tipo, descripcion, ubicacion, estado)
                    VALUES (:codigo, :nombre, :tipo, :descripcion, :ubicacion, :estado)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":codigo" => $codigo,
                ":nombre" => $nombre,
                ":tipo" => $tipo,
                ":descripcion" => $descripcion === "" ? null : $descripcion,
                ":ubicacion" => $ubicacion,
                ":estado" => $estado
            ]);

            header("Location: index.php?msg=creado");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $errores[] = "Ya existe un equipo con ese código.";
            } else {
                throw $e;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo equipo</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <nav>
        <a href="index.php">Equipos</a>
        <a href="reservas.php">Reservas</a>
    </nav>

    <h1>Nuevo equipo</h1>

    <?php if (!empty($errores)): ?>
        <ul class="errores">
            <?php foreach ($errores as $error): ?>
                <li>
                    <?php echo $error; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Código:
            <input type="text" name="codigo" value="<?php echo htmlspecialchars($codigo); ?>">
        </label>

        <label>Nombre:
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
        </label>

        <label>Tipo:
            <select name="tipo">
                <option value="">-- Selecciona --</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo $tipo === $t ? "selected" : ""; ?>>
                        <?php echo $t; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Descripción (opcional):
            <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($descripcion); ?></textarea>
        </label>

        <label>Ubicación:
            <input type="text" name="ubicacion" value="<?php echo htmlspecialchars($ubicacion); ?>">
        </label>

        <label>Estado:
            <select name="estado">
                <?php foreach ($estados as $es): ?>
                    <option value="<?php echo $es; ?>" <?php echo $estado === $es ? "selected" : ""; ?>>
                        <?php echo $es; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit">Guardar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>

</html>