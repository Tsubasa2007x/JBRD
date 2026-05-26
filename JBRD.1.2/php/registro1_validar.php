<?php
session_name('jbrd');
session_start();

if (!isset($_POST['nombre'])) {
    header("Location: ../registro1.php");
    exit();
}

$_SESSION['registro_paso1'] = [
    'nombre'          => trim($_POST['nombre']),
    'apellido'        => trim($_POST['apellido']),
    'tipo_documento'  => trim($_POST['tipo_documento']),
    'numero_documento'=> trim($_POST['numero_documento']),
    'correo'          => trim($_POST['correo']),
    'telefono'        => trim($_POST['telefono']),
    'fecha_nacimiento'=> $_POST['fecha_nacimiento'],
    'fecha_expedicion'=> $_POST['fecha_expedicion'],
];

header("Location: ../registro2.php");
exit();
?>
 