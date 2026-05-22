<?php
session_name('jbrd');
session_start();
 
if (!isset($_POST['nombre'])) {
    header("Location: ../registro1.php");
    exit();
}
 
// Guardar datos del paso 1 en sesión temporal
$_SESSION['registro_paso1'] = [
    'nombre'         => trim($_POST['nombre']),
    'apellido'       => trim($_POST['apellido']),
    'tipo_documento' => trim($_POST['tipo_documento']),
    'documento'      => trim($_POST['documento']),
    'correo'         => trim($_POST['correo']),
    'telefono'       => trim($_POST['telefono']),
    'fecha_nac'      => $_POST['fecha_nac'],
    'fecha_exp'      => $_POST['fecha_exp'],
];
 
header("Location: ../registro2.php");
exit();
?>
 