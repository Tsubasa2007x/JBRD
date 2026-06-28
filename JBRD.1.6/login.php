<?php
session_name('jbrd');
session_start();
if (isset($_SESSION['id_cuenta'])) {
    header("Location: index.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JBRD Coin - Iniciar sesión</title>
  <link rel="stylesheet" href="CSS/main.css">
  <link rel="stylesheet" href="CSS/ojo.css">
</head>
<body class="auth-page">

<div class="auth-card">

  <div class="auth-logo">
    <img src="imagenes/logogris.png" alt="JBRD">
    <span>JBRD COIN</span>
  </div>

  <h2>Iniciar sesión</h2>
  <p class="auth-sub">Bienvenido de nuevo</p>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">Usuario o contraseña incorrectos.</div>
  <?php endif; ?>

  <?php if (isset($_GET['registro'])): ?>
    <div class="alert alert-success">Cuenta creada correctamente. Ya puedes iniciar sesión.</div>
  <?php endif; ?>

  <form action="php/login_validar.php" method="POST">

    <div class="form-group">
      <label>Usuario</label>
      <input type="text" name="usuario" placeholder="Tu usuario" required autofocus>
    </div>

    <div class="form-group">
      <label>Contraseña</label>
      <div class="password-wrapper">
        <input type="password" name="password" id="pass1" placeholder="Tu contraseña" required>
        <button type="button" class="toggle-pass" onclick="togglePass('pass1', this)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
    </div>

    <button type="submit" name="ingresar" class="btn-primary btn-block">Ingresar</button>

  </form>

  <p class="auth-link">¿No tienes cuenta? <a href="registro1.php">Regístrate aquí</a></p>

</div>

<script src="js/app.js"></script>
</body>
</html>