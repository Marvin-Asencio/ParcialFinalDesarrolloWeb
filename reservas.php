<?php
require "conexion.php";

$sql = "SELECT r.id, r.nombre, r.carne, r.fecha, r.hora_inicio, r.hora_fin, r.motivo,
               e.codigo, e.nombre AS equipo
        FROM reservas r
        INNER JOIN equipos e ON e.id = r.equipo_id
        ORDER BY r.fecha DESC, r.hora_inicio ASC";
$lista = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$mensajes = [
    "creado" => "Reserva creada correctamente.",
    "actualizado" => "Reserva actualizada correctamente.",
    "eliminado" => "Reserva eliminada correctamente."
];
$msg = $_GET["msg"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav>
        <a href="index.php">Equipos</a>
        <a href="reservas.php">Reservas</a>
    </nav>

    <h1>Reservas</h1>

    <?php if (isset($mensajes[$msg])): ?>
        <p class="mensaje"><?php echo $mensajes[$msg]; ?></p>
    <?php endif; ?>

    <a href="reserva_crear.php">Nueva reserva</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Equipo</th>
            <th>Nombre</th>
            <th>Carné</th>
            <th>Fecha</th>
            <th>Horario</th>
            <th>Motivo</th>
            <th>Acciones</th>
        </tr>

        <?php if (count($lista) === 0): ?>
            <tr><td colspan="8">No hay reservas registradas.</td></tr>
        <?php endif; ?>

        <?php foreach ($lista as $r): ?>
            <tr>
                <td><?php echo $r["id"]; ?></td>
                <td><?php echo htmlspecialchars($r["codigo"] . " - " . $r["equipo"]); ?></td>
                <td><?php echo htmlspecialchars($r["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($r["carne"]); ?></td>
                <td><?php echo $r["fecha"]; ?></td>
                <td><?php echo substr($r["hora_inicio"], 0, 5) . " - " . substr($r["hora_fin"], 0, 5); ?></td>
                <td><?php echo htmlspecialchars($r["motivo"] ?? ""); ?></td>
                <td>
                    <a href="reserva_editar.php?id=<?php echo $r["id"]; ?>">Editar</a>
                    <form class="en-linea" method="POST" action="reserva_eliminar.php"
                          onsubmit="return confirm('¿Eliminar esta reserva?');">
                        <input type="hidden" name="id" value="<?php echo $r["id"]; ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>