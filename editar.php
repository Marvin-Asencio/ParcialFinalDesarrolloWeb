<?php
require "conexion.php";

$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM equipos WHERE id = :id");
$stmt->execute([":id" => $id]);
$equipo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipo) {
    header("Location: index.php");
    exit;
}

$errores = [];
$codigo = $equipo["codigo"];
$nombre = $equipo["nombre"];
$tipo = $equipo["tipo"];
$descripcion = $equipo["descripcion"] ?? "";
$ubicacion = $equipo["ubicacion"];
$estado = $equipo["estado"];

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
            $sql = "UPDATE equipos
                    SET codigo = :codigo, nombre = :nombre, tipo = :tipo,
                        descripcion = :descripcion, ubicacion = :ubicacion, estado = :estado
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":codigo" => $codigo,
                ":nombre" => $nombre,
                ":tipo" => $tipo,
                ":descripcion" => $descripcion === "" ? null : $descripcion,
                ":ubicacion" => $ubicacion,
                ":estado" => $estado,
                ":id" => $id
            ]);

            header("Location: index.php?msg=actualizado");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $errores[] = "Ya existe otro equipo con ese código.";
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
    <title>Editar equipo</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <nav>
        <a href="index.php">Equipos</a>
        <a href="reservas.php">Reservas</a>
    </nav>

    <h1>Editar equipo</h1>

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
        <input type="hidden" name="id" value="<?php echo $id; ?>">

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

        <button type="submit">Actualizar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>

</html>