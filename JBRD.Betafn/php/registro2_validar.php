<?php
session_name('jbrd');
session_start();
 
if (!isset($_SESSION['registro_paso1'])) {
    header("Location: ../registro1.php");
    exit();
}
 
$conexion = mysqli_connect("localhost", "root", "", "jbrd");
if (!$conexion) die("Error de conexión");
 
$usuario   = trim($_POST['usuario']);
$password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
$datos     = $_SESSION['registro_paso1'];
 
// Verificar si el usuario ya existe
$stmt = mysqli_prepare($conexion, "SELECT id_cuenta FROM cuentas WHERE usuario = ?");
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
 
if (mysqli_stmt_num_rows($stmt) > 0) {
    header("Location: ../registro2.php?error=usuario_existe");
    exit();
}
 
// Insertar en tabla usuarios
$stmt2 = mysqli_prepare($conexion,
    "INSERT INTO usuarios (nombre, apellido, tipo_documento, documento, correo, telefono, fecha_nac, fecha_exp)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt2, "ssssssss",
    $datos['nombre'],
    $datos['apellido'],
    $datos['tipo_documento'],
    $datos['documento'],
    $datos['correo'],
    $datos['telefono'],
    $datos['fecha_nac'],
    $datos['fecha_exp']
);
mysqli_stmt_execute($stmt2);
$id_usuario = mysqli_insert_id($conexion);
 
// Insertar en tabla cuentas
$stmt3 = mysqli_prepare($conexion,
    "INSERT INTO cuentas (id_usuario, usuario, password, creditos, experiencia, estado, id_rol)
     VALUES (?, ?, ?, 0, 0, 'activo', 1)"
);
mysqli_stmt_bind_param($stmt3, "iss", $id_usuario, $usuario, $password);
mysqli_stmt_execute($stmt3);
 
// Limpiar datos temporales
unset($_SESSION['registro_paso1']);
 
header("Location: ../login.php?registro=1");
exit();
?>