<?php
session_name('jbrd');
session_start();
// Si no viene del paso 1, redirigir
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
  <p class="auth-sub">Paso 2 de 2 — Elige tu usuario y contraseña</p>
 
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">
      <?php
        $err = $_GET['error'];
        if ($err == 'usuario_existe') echo 'Ese nombre de usuario ya está en uso.';
        else echo 'Ocurrió un error. Intenta de nuevo.';
      ?>
    </div>
  <?php endif; ?>
 
  <form action="php/registro2_validar.php" method="POST">
 
    <div class="form-group">
      <label>Nombre de usuario</label>
      <input type="text" name="usuario" placeholder="Ej: daniel123" required minlength="4" maxlength="30">
    </div>
 
    <div class="form-group">
      <label>Contraseña</label>
      <input type="password" name="password" placeholder="Mínimo 6 caracteres" required minlength="6" id="pass1">
    </div>
 
    <div class="form-group">
      <label>Confirmar contraseña</label>
      <input type="password" name="password2" placeholder="Repite tu contraseña" required minlength="6" id="pass2">
    </div>
 
    <button type="submit" class="btn-primary btn-block">Registrarme</button>
 
  </form>
 
  <p class="auth-link"><a href="registro1.php">← Volver al paso anterior</a></p>
 
</div>
 
<script>
  document.querySelector('form').addEventListener('submit', function(e) {
    var p1 = document.getElementById('pass1').value;
    var p2 = document.getElementById('pass2').value;
    if (p1 !== p2) {
      e.preventDefault();
      alert('Las contraseñas no coinciden.');
    }
  });
</script>
 
</body>
</html>