<?php
session_name('jbrd');
session_start();
 
$conexion = mysqli_connect("localhost", "root", "", "jbrd");
if (!$conexion) die("Error de conexión");
 
if (isset($_POST['ingresar'])) {
 
    $usuario  = trim($_POST['usuario']);
    $password = $_POST['password'];
 
    $stmt = mysqli_prepare($conexion, "SELECT * FROM cuentas WHERE usuario = ?");
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
 
    if (mysqli_num_rows($resultado) == 1) {
        $datos = mysqli_fetch_assoc($resultado);
 
        if (password_verify($password, $datos['password'])) {
 
            $_SESSION['id_cuenta']  = $datos['id_cuenta'];
            $_SESSION['id_usuario'] = $datos['id_usuario'];
            $_SESSION['usuario']    = $datos['usuario'];
            $_SESSION['id_rol']     = $datos['id_rol'];
            $_SESSION['creditos']   = $datos['creditos'];
            $_SESSION['experiencia']= $datos['experiencia'];
 
            header("Location: ../index.php");
            exit();
 
        } else {
            header("Location: ../login.php?error=1");
            exit();
        }
    } else {
        header("Location: ../login.php?error=1");
        exit();
    }
}
?>