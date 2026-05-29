<?php
// paginas/get_contenidos.php
session_name('jbrd');
session_start();
 
require_once '../php/conexion.php';
 
if (!isset($_SESSION['id_cuenta']) || !isset($_GET['id_leccion'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}
 
$id_leccion = intval($_GET['id_leccion']);
 
try {
    $stmt = $pdo->prepare("
        SELECT id_contenido, nombre_contenido, descripcion, orden, video_id, conceptos
        FROM contenidos 
        WHERE id_leccion = :id_leccion 
        ORDER BY orden ASC
    ");
    $stmt->execute(['id_leccion' => $id_leccion]);
    $contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($contenidos, JSON_UNESCAPED_UNICODE);
 
} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    // En producción nunca expongas $e->getMessage() al cliente
    echo json_encode(['error' => 'Error interno del servidor']);
}
exit();