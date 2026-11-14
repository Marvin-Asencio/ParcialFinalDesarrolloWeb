<?php
require "conexion.php";

$stmt = $pdo->query("SELECT id, codigo, nombre, tipo, ubicacion, estado FROM equipos ORDER BY id DESC");
$lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensajes = [
    "creado" => "Equipo creado correctamente.",
    "actualizado" => "Equipo actualizado correctamente.",
    "eliminado" => "Equipo eliminado correctamente.",
    "en_uso" => "No se puede eliminar: el equipo tiene reservas."
];
$msg = $_GET["msg"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Equipos</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <nav>
        <a href="index.php">Equipos</a>
        <a href="reservas.php">Reservas</a>
    </nav>

    <h1>Equipos</h1>

    <?php if (isset($mensajes[$msg])): ?>
        <p class="<?php echo $msg === "en_uso" ? "errores" : "mensaje"; ?>">
            <?php echo $mensajes[$msg]; ?>
        </p>
    <?php endif; ?>

    <a href="crear.php">Nuevo equipo</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Ubicación</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php if (count($lista) === 0): ?>
            <tr>
                <td colspan="7">No hay equipos registrados.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($lista as $e): ?>
            <tr>
                <td>
                    <?php echo $e["id"]; ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($e["codigo"]); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($e["nombre"]); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($e["tipo"]); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($e["ubicacion"]); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($e["estado"]); ?>
                </td>
                <td>
                    <a href="editar.php?id=<?php echo $e["id"]; ?>">Editar</a>
                    <form class="en-linea" method="POST" action="eliminar.php"
                        onsubmit="return confirm('¿Eliminar este equipo?');">
                        <input type="hidden" name="id" value="<?php echo $e["id"]; ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>