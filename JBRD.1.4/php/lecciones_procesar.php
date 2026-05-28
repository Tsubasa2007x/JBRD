<?php
// php/lecciones_procesar.php
session_name('jbrd');
session_start();

// Al estar en la carpeta php/, incluimos la conexión directamente
require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificación de seguridad de sesión
if (!isset($_SESSION['id_cuenta']) || !isset($_POST['id_leccion'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida o petición incompleta.']);
    exit();
}

$id_cuenta = $_SESSION['id_cuenta'];
$id_leccion = intval($_POST['id_leccion']);

try {
    // 1. Verificar si el usuario YA completó esta lección previamente
    $stmtCheck = $pdo->prepare("SELECT id_progreso FROM progreso_lecciones WHERE id_cuenta = :id_cuenta AND id_leccion = :id_leccion");
    $stmtCheck->execute(['id_cuenta' => $id_cuenta, 'id_leccion' => $id_leccion]);
    
    if ($stmtCheck->fetch()) {
        echo json_encode(['status' => 'info', 'message' => 'Ya has reclamado las recompensas de este módulo anteriormente.']);
        exit();
    }

    // 2. Obtener los valores de recompensa que otorga esta lección en específico
    $stmtLec = $pdo->prepare("SELECT recompensa_creditos, recompensa_experiencia FROM lecciones WHERE id_leccion = :id_leccion");
    $stmtLec->execute(['id_leccion' => $id_leccion]);
    $leccion = $stmtLec->fetch(PDO::FETCH_ASSOC);

    if (!$leccion) {
        echo json_encode(['status' => 'error', 'message' => 'La lección solicitada no existe.']);
        exit();
    }

    $xp_ganada = intval($leccion['recompensa_experiencia']);
    $creditos_ganados = intval($leccion['recompensa_creditos']);

    // --- INICIAMOS TRANSACCIÓN SQL ---
    $pdo->beginTransaction();

    // 3. Insertar el registro de finalización (Respetando las columnas de tu tabla progreso_lecciones)
    $stmtProg = $pdo->prepare("
        INSERT INTO progreso_lecciones (id_cuenta, id_leccion, fecha_completada) 
        VALUES (:id_cuenta, :id_leccion, NOW())
    ");
    $stmtProg->execute([
        'id_cuenta'  => $id_cuenta,
        'id_leccion' => $id_leccion
    ]);

    // 4. Actualizar el balance de experiencia y créditos directamente en la cuenta del usuario
    $stmtUpdateUser = $pdo->prepare("
        UPDATE cuentas 
        SET experiencia = experiencia + :xp, creditos = creditos + :creditos 
        WHERE id_cuenta = :id_cuenta
    ");
    $stmtUpdateUser->execute([
        'xp'        => $xp_ganada,
        'creditos'  => $creditos_ganados,
        'id_cuenta' => $id_cuenta
    ]);

    // 5. Consultar los nuevos datos acumulados para actualizar la interfaz del usuario al instante
    $stmtUserFresh = $pdo->prepare("
        SELECT c.experiencia, r.nombre_rol 
        FROM cuentas c 
        JOIN roles r ON c.id_rol = r.id_rol 
        WHERE c.id_cuenta = :id_cuenta
    ");
    $stmtUserFresh->execute(['id_cuenta' => $id_cuenta]);
    $userFresh = $stmtUserFresh->fetch(PDO::FETCH_ASSOC);

    // Confirmamos todos los cambios en la base de datos
    $pdo->commit();

    // Actualizamos las variables de sesión activas para que persistan al navegar por la app
    $_SESSION['experiencia'] += $xp_ganada;
    $_SESSION['creditos']    += $creditos_ganados;

    // Enviamos la respuesta de éxito al SweetAlert de tu JavaScript
    echo json_encode([
        'status'           => 'success',
        'xp_ganada'        => $xp_ganada,
        'creditos_ganados' => $creditos_ganados,
        'total_xp'         => $userFresh['experiencia'],
        'nuevo_rol'        => $userFresh['nombre_rol']
    ]);

} catch (Exception $e) {
    // Si algo sale mal, cancelamos cualquier cambio en la base de datos para evitar duplicados ruidosos
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Error interno en el servidor: ' . $e->getMessage()]);
}
exit();
?>