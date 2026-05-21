<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JBRD Coin - Registro</title>

    <link rel="stylesheet" href="../css/estilos.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="card">

    <h2>Registro de Usuario</h2>

    <!-- FORMULARIO -->
    <form method="POST" action="../php/JBRD.php">

        <!-- USUARIO -->
        <label for="Usuario">Usuario</label><br>
        <input type="text" name="Usuario" id="Usuario" required>

        <br><br>

        <!-- CONTRASEÑA -->
        <div class="password-box">

            <label for="Password">Contraseña</label><br>

            <input type="password" name="Password" id="Password" required>

            <span 
                onclick="
                const pass = document.getElementById('Password');

                if(pass.type === 'password'){
                    pass.type = 'text';
                }else{
                    pass.type = 'password';
                }
                "
            >
                👁
            </span>

        </div>

        <small>
            Mínimo 6 caracteres, 1 mayúscula, solo letras y números
        </small>

        <br><br>

        <!-- BOTÓN -->
        <button type="submit" name="volver">
            Crear cuenta
        </button>

    </form>

</div>

</body>
</html>