<?php
require "conexion.php";

$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM reservas WHERE id = :id");
$stmt->execute([":id" => $id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    header("Location: reservas.php");
    exit;
}

$todos = $pdo->query("SELECT id, codigo, nombre FROM equipos ORDER BY nombre")
    ->fetchAll(PDO::FETCH_ASSOC);

$errores = [];
$equipo_id = $reserva["equipo_id"];
$nombre = $reserva["nombre"];
$carne = $reserva["carne"];
$fecha = $reserva["fecha"];
$hora_inicio = substr($reserva["hora_inicio"], 0, 5);
$hora_fin = substr($reserva["hora_fin"], 0, 5);
$motivo = $reserva["motivo"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $equipo_id = (int) ($_POST["equipo_id"] ?? 0);
    $nombre = trim($_POST["nombre"] ?? "");
    $carne = trim($_POST["carne"] ?? "");
    $fecha = $_POST["fecha"] ?? "";
    $hora_inicio = $_POST["hora_inicio"] ?? "";
    $hora_fin = $_POST["hora_fin"] ?? "";
    $motivo = trim($_POST["motivo"] ?? "");

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM equipos WHERE id = :id");
    $stmt->execute([":id" => $equipo_id]);
    if ($stmt->fetchColumn() == 0) {
        $errores[] = "Selecciona un equipo válido.";
    }

    if ($nombre === "" || strlen($nombre) > 100) {
        $errores[] = "El nombre es obligatorio (máximo 100 caracteres).";
    }
    if ($carne === "" || strlen($carne) > 20) {
        $errores[] = "El carné es obligatorio (máximo 20 caracteres).";
    }

    $fechaValida = DateTime::createFromFormat("Y-m-d", $fecha);
    if (!$fechaValida || $fechaValida->format("Y-m-d") !== $fecha) {
        $errores[] = "La fecha no es válida.";
    }

    $patronHora = "/^\d{2}:\d{2}$/";
    if (!preg_match($patronHora, $hora_inicio) || !preg_match($patronHora, $hora_fin)) {
        $errores[] = "Las horas no son válidas.";
    } elseif ($hora_fin <= $hora_inicio) {
        $errores[] = "La hora de fin debe ser mayor que la hora de inicio.";
    }

    if (strlen($motivo) > 255) {
        $errores[] = "El motivo no puede pasar de 255 caracteres.";
    }

    // Revisar cruce de horario, ignorando esta misma reserva
    if (empty($errores)) {
        $sql = "SELECT COUNT(*) FROM reservas
                WHERE equipo_id = :equipo AND fecha = :fecha
                AND hora_inicio < :fin AND hora_fin > :inicio
                AND id <> :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":equipo" => $equipo_id,
            ":fecha" => $fecha,
            ":inicio" => $hora_inicio,
            ":fin" => $hora_fin,
            ":id" => $id
        ]);
        if ($stmt->fetchColumn() > 0) {
            $errores[] = "Ese equipo ya está reservado en ese horario.";
        }
    }

    if (empty($errores)) {
        $sql = "UPDATE reservas
                SET equipo_id = :equipo, nombre = :nombre, carne = :carne, fecha = :fecha,
                    hora_inicio = :inicio, hora_fin = :fin, motivo = :motivo
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":equipo" => $equipo_id,
            ":nombre" => $nombre,
            ":carne" => $carne,
            ":fecha" => $fecha,
            ":inicio" => $hora_inicio,
            ":fin" => $hora_fin,
            ":motivo" => $motivo === "" ? null : $motivo,
            ":id" => $id
        ]);

        header("Location: reservas.php?msg=actualizado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar reserva</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <nav>
        <a href="index.php">Equipos</a>
        <a href="reservas.php">Reservas</a>
    </nav>

    <h1>Editar reserva</h1>

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

        <label>Equipo:
            <select name="equipo_id">
                <?php foreach ($todos as $eq): ?>
                    <option value="<?php echo $eq["id"]; ?>"
                        <?php echo $equipo_id == $eq["id"] ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($eq["codigo"] . " - " . $eq["nombre"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Nombre:
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
        </label>

        <label>Carné:
            <input type="text" name="carne" value="<?php echo htmlspecialchars($carne); ?>">
        </label>

        <label>Fecha:
            <input type="date" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
        </label>

        <label>Hora de inicio:
            <input type="time" name="hora_inicio" value="<?php echo htmlspecialchars($hora_inicio); ?>">
        </label>

        <label>Hora de fin:
            <input type="time" name="hora_fin" value="<?php echo htmlspecialchars($hora_fin); ?>">
        </label>

        <label>Motivo (opcional):
            <textarea name="motivo" rows="3"><?php echo htmlspecialchars($motivo); ?></textarea>
        </label>

        <button type="submit">Actualizar</button>
        <a href="reservas.php">Cancelar</a>
    </form>
</body>

</html>