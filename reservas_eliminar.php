<?php
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST["id"] ?? 0);

    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = :id");
    $stmt->execute([":id" => $id]);

    header("Location: reservas.php?msg=eliminado");
    exit;
}

header("Location: reservas.php");
exit;