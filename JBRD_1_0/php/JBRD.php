<?php
$conexion = mysqli_connect(
    "localhost",
    "root",
    "",
    "jbrd"
);
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if (isset($_POST['Registrar'])) {

    // DATOS PERSONALES
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $tipo_documento = $_POST['tipo_documento'];
    $documento = $_POST['documento'];
    $correo = $_POST['correo'];
    $contacto = $_POST['contacto'];
    $fecha_nac = $_POST['fecha_nac'];
    $fecha_exp = $_POST['fecha_exp'];

    // INSERTAR EN MYSQL
    $sql = "INSERT INTO usuarios_jbrd (

        nombres,
        apellidos,
        tipo_documento,
        documento,
        correo,
        contacto,
        fecha_nac,
        fecha_exp

    ) VALUES (

        '$nombres',
        '$apellidos',
        '$tipo_documento',
        '$documento',
        '$correo',
        '$contacto',
        '$fecha_nac',
        '$fecha_exp'

    )";

    // EJECUTAR CONSULTA
    mysqli_query($conexion, $sql);

    // REDIRECCIONAR
    header("Location: ../html/registro.php");

    exit();
}
if (isset($_POST['volver'])) {

    $usuario = $_POST['Usuario'];
    $password = $_POST['Password'];

    $vreg = "INSERT INTO cuenta (Usuario , Password) VALUES ('$usuario','$password')";

    mysqli_query($conexion, $vreg);

    header("Location: ../html/login.php");

    exit();


}




?>
