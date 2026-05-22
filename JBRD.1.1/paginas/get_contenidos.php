<?php
// paginas/get_contenidos.php
session_name('jbrd');
session_start();

// Retrocedemos un nivel para incluir la conexión centralizada desde php/
require_once '../php/conexion.php';

// Validamos sesión básica y que venga el parámetro esperado por la URL
if (!isset($_SESSION['id_cuenta']) || !isset($_GET['id_leccion'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$id_leccion = intval($_GET['id_leccion']);

try {
    // Consultamos los contenidos de la lección ordenados fielmente por su columna 'orden'
    $stmt = $pdo->prepare("
        SELECT id_contenido, nombre_contenido, descripcion, orden 
        FROM contenidos 
        WHERE id_leccion = :id_leccion 
        ORDER BY orden ASC
    ");
    $stmt->execute(['id_leccion' => $id_leccion]);
    $contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Indicamos al navegador que la respuesta es un JSON limpio y estructurado
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($contenidos, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Si hay un fallo en el SQL, devolvemos un estado de error manejable
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit();
?>