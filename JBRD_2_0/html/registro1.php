<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro JBRD</title>

    <script src="../js/app.js" defer></script>

    <link rel="stylesheet" href="../css/estilos.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="box-reg1">

    <h2>Datos del usuario</h2>

    <!-- FORMULARIO CONECTADO AL PHP -->
    <form method="POST" action="../php/JBRD.php">

        <input
        type="text"
        name="nombres"
        placeholder="Nombres"
        required
        >

        <input
        type="text"
        name="apellidos"
        placeholder="Apellidos"
        required
        >

        <select
        name="tipo_documento"
        id="tipoid"
        required
        >

            <option value="">Tipo de documento</option>

            <option value="CC">CC</option>
            <option value="CE">CE</option>
            <option value="TI">TI</option>
            <option value="NIT">NIT</option>
            <option value="PAS">PAS</option>

        </select>

        <input
        type="text"
        name="documento"
        placeholder="Número de documento"
        required
        >

        <input
        type="email"
        name="correo"
        placeholder="Correo electrónico"
        required
        >

        <input
        type="text"
        name="contacto"
        placeholder="Número de contacto"
        required
        >

        <input
        type="date"
        name="fecha_nac"
        required
        >

        <input
        type="date"
        name="fecha_exp"
        required
        >

        <!-- BOTÓN -->
        <button
        type="submit"
        name="Registrar"
        >
            Registrarse
        </button>

    </form>

</div>

</body>
</html>
