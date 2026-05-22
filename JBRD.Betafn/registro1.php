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
  <title>JBRD Coin - Registro</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body class="auth-page">
 
<div class="auth-card auth-card-wide">
 
  <div class="auth-logo">
    <img src="imagenes/logogris.png" alt="JBRD">
    <span>JBRD COIN</span>
  </div>
 
  <div class="steps">
    <div class="step active">1</div>
    <div class="step-line"></div>
    <div class="step">2</div>
  </div>
 
  <h2>Datos personales</h2>
  <p class="auth-sub">Paso 1 de 2 — Cuéntanos quién eres</p>
 
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">Por favor completa todos los campos correctamente.</div>
  <?php endif; ?>
 
  <form action="php/registro1_validar.php" method="POST">
 
    <div class="form-row">
      <div class="form-group">
        <label>Nombres</label>
        <input type="text" name="nombre" placeholder="Tus nombres" required>
      </div>
      <div class="form-group">
        <label>Apellidos</label>
        <input type="text" name="apellido" placeholder="Tus apellidos" required>
      </div>
    </div>
 
    <div class="form-row">
      <div class="form-group">
        <label>Tipo de identificación</label>
        <select name="tipo_documento" required>
          <option value="">Selecciona...</option>
          <option value="CC">Cédula de ciudadanía</option>
          <option value="TI">Tarjeta de identidad</option>
          <option value="CE">Cédula de extranjería</option>
          <option value="PA">Pasaporte</option>
        </select>
      </div>
      <div class="form-group">
        <label>Número de identificación</label>
        <input type="text" name="documento" placeholder="Número de documento" required>
      </div>
    </div>
 
    <div class="form-row">
      <div class="form-group">
        <label>Correo electrónico</label>
        <input type="email" name="correo" placeholder="correo@ejemplo.com" required>
      </div>
      <div class="form-group">
        <label>Número de contacto</label>
        <input type="tel" name="telefono" placeholder="Ej: 3001234567" required>
      </div>
    </div>
 
    <div class="form-row">
      <div class="form-group">
        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nac" required>
      </div>
      <div class="form-group">
        <label>Fecha de expedición del documento</label>
        <input type="date" name="fecha_exp" required>
      </div>
    </div>
 
    <button type="submit" class="btn-primary btn-block">Siguiente →</button>
 
  </form>
 
  <p class="auth-link">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
 
</div>
 
</body>
</html>