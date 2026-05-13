<?php

// CONEXIÓN
$conexion = mysqli_connect(
    "localhost",
    "root",
    "",
    "jbrd"
);

// VERIFICAR CONEXIÓN
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// SI SE PRESIONÓ EL BOTÓN
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
    header("Location: ../html/registro.html");

    exit();
}

?>