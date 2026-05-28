<?php
session_name('jbrd');
session_start();

if (isset($_SESSION['id_cuenta'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conexion = mysqli_connect("localhost", "root", "", "jbrd");
    if (!$conexion) die("Error de conexión con la base de datos.");

    $usuario  = trim($_POST['usuario']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conexion, "
        SELECT c.*, r.nombre_rol
        FROM cuentas c
        JOIN roles r ON c.id_rol = r.id_rol
        WHERE c.usuario = ? AND c.estado = 1
    ");
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $datos  = mysqli_fetch_assoc($result);

    if ($datos && password_verify($password, $datos['password'])) {

        $_SESSION['id_cuenta']   = $datos['id_cuenta'];
        $_SESSION['id_usuario']  = $datos['id_usuario'];
        $_SESSION['usuario']     = $datos['usuario'];
        $_SESSION['id_rol']      = $datos['id_rol'];
        $_SESSION['nombre_rol']  = $datos['nombre_rol'];
        $_SESSION['creditos']    = $datos['creditos'];
        $_SESSION['experiencia'] = $datos['experiencia'];

        header("Location: ../index.php");
        exit();

    } else {
        header("Location: ../login.php?error=1");
        exit();
    }
}
?>