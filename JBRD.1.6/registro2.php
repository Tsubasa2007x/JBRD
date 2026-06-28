<?php
session_name('jbrd');
session_start();
if (!isset($_SESSION['registro_paso1'])) {
    header("Location: registro1.php");
    exit();
}
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
  <title>JBRD Coin - Crear cuenta</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/ojo.css">
</head>
<body class="auth-page">

<div class="auth-card">

  <div class="auth-logo">
    <img src="imagenes/logogris.png" alt="JBRD">
    <span>JBRD COIN</span>
  </div>

  <div class="steps">
    <div class="step done">✓</div>
    <div class="step-line active"></div>
    <div class="step active">2</div>
  </div>

  <h2>Crea tu cuenta</h2>
  <p class="auth-sub">Paso 2 de 2 — Elige tu usuario y contrasena</p>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">
      <?php
        $err = $_GET['error'];
        if ($err == 'usuario_existe')    echo 'Ese nombre de usuario ya esta en uso.';
        elseif ($err == 'correo_existe') echo 'Ese correo ya esta registrado.';
        else                             echo 'Ocurrio un error. Intenta de nuevo.';
      ?>
    </div>
  <?php endif; ?>

  <form action="php/registro2_validar.php" method="POST" id="formRegistro">

    <div class="form-group">
      <label>Nombre de usuario</label>
      <input type="text" name="usuario" placeholder="Ej: daniel123" required minlength="4" maxlength="30">
    </div>

    <div class="form-group">
      <label>Contrasena</label>
      <div class="password-wrapper">
        <input type="password" name="password" id="pass1" placeholder="Minimo 6 caracteres" required minlength="6">
        <button type="button" class="toggle-pass" onclick="togglePass('pass1', this)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="form-group">
      <label>Confirmar contrasena</label>
      <div class="password-wrapper">
        <input type="password" name="password2" id="pass2" placeholder="Repite tu contrasena" required minlength="6">
        <button type="button" class="toggle-pass" onclick="togglePass('pass2', this)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
    </div>

    <button type="submit" class="btn-primary btn-block">Registrarme</button>

  </form>

  <p class="auth-link"><a href="registro1.php">← Volver al paso anterior</a></p>

</div>

<script src="/JBRD.1.3/js/app.js"></script>
</body>
</html>