<?php
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST["id"] ?? 0);

    try {
        $stmt = $pdo->prepare("DELETE FROM equipos WHERE id = :id");
        $stmt->execute([":id" => $id]);

        header("Location: index.php?msg=eliminado");
        exit;
    } catch (PDOException $e) {
        // 23000 = violación de llave foránea (el equipo tiene reservas)
        if ($e->getCode() === "23000") {
            header("Location: index.php?msg=en_uso");
            exit;
        }
        throw $e;
    }
}

header("Location: index.php");
exit;