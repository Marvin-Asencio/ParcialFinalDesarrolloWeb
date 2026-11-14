<?php
$host = "127.0.0.1";
$puerto = "3307";
$base = "equipos";
$usuario = "root";
$clave = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$puerto;dbname=$base;charset=utf8mb4",
        $usuario,
        $clave
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Valores permitidos (los mismos ENUM de la base de datos)
$tipos = ["Laptop", "Proyector", "Tablet", "Cámara", "Laboratorio"];
$estados = ["Disponible", "Mantenimiento"];