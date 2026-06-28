<?php
session_name('jbrd');
session_start();

if (!isset($_SESSION['registro_paso1'])) {
    header("Location: ../registro1.php");
    exit();
}

$conexion = mysqli_connect("localhost", "root", "", "jbrd");
if (!$conexion) die("Error de conexion con la base de datos.");

$usuario  = trim($_POST['usuario']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$datos    = $_SESSION['registro_paso1'];

// Verificar si el usuario ya existe
$stmt = mysqli_prepare($conexion, "SELECT id_cuenta FROM cuentas WHERE usuario = ?");
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    header("Location: ../registro2.php?error=usuario_existe");
    exit();
}

// Verificar si el correo ya existe
$stmt2 = mysqli_prepare($conexion, "SELECT id_usuario FROM usuarios WHERE correo = ?");
mysqli_stmt_bind_param($stmt2, "s", $datos['correo']);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);

if (mysqli_stmt_num_rows($stmt2) > 0) {
    header("Location: ../registro2.php?error=correo_existe");
    exit();
}

// Insertar en tabla usuarios
$stmt3 = mysqli_prepare($conexion,
    "INSERT INTO usuarios (nombre, apellido, correo, telefono, tipo_documento, numero_documento, fecha_nacimiento, fecha_expedicion)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt3, "ssssssss",
    $datos['nombre'],
    $datos['apellido'],
    $datos['correo'],
    $datos['telefono'],
    $datos['tipo_documento'],
    $datos['numero_documento'],
    $datos['fecha_nacimiento'],
    $datos['fecha_expedicion']
);

if (!mysqli_stmt_execute($stmt3)) {
    header("Location: ../registro2.php?error=db");
    exit();
}

$id_usuario = mysqli_insert_id($conexion);

// Insertar en tabla cuentas
$stmt4 = mysqli_prepare($conexion,
    "INSERT INTO cuentas (id_usuario, usuario, password, creditos, experiencia, estado, id_rol)
     VALUES (?, ?, ?, 100, 0, 1, 1)"
);
mysqli_stmt_bind_param($stmt4, "iss", $id_usuario, $usuario, $password);

if (!mysqli_stmt_execute($stmt4)) {
    header("Location: ../registro2.php?error=db");
    exit();
}

// Limpiar sesion temporal
unset($_SESSION['registro_paso1']);

header("Location: ../login.php?registro=1");
exit();
?>