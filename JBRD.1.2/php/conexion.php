<?php
// php/conexion.php

$host    = 'localhost';
$db      = 'jbrd';
$user    = 'root';
$password = ''; // Déjalo vacío si usas la configuración por defecto de XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error crítico de conexión con la base de datos: " . $e->getMessage());
}
?>