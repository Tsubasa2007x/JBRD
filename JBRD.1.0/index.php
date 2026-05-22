<?php
session_name('jbrd');
session_start();
$sesionActiva = isset($_SESSION['id_cuenta']);
$usuario      = $sesionActiva ? htmlspecialchars($_SESSION['usuario'])        : '';
$iniciales    = $sesionActiva ? strtoupper(substr($_SESSION['usuario'], 0, 2)) : '';
$creditos     = $sesionActiva ? $_SESSION['creditos']     ?? 0 : 0;
$experiencia  = $sesionActiva ? $_SESSION['experiencia']  ?? 0 : 0;
$rol          = $sesionActiva ? htmlspecialchars($_SESSION['nombre_rol'] ?? 'Explorador Digital') : '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JBRD Coin</title>
  <link rel="stylesheet" href="css/main.css">
</head>
 
<body>
 
<!-- ══════════════════════════════════════
     HEADER
══════════════════════════════════════ -->
<header id="header">
  <div class="logo">
    <img src="imagenes/logogris.png" alt="JBRD">
    <span>JBRD</span>
  </div>
 
  <nav>
    <a href="#servicios">Servicios</a>
    <a href="#aprender">Aprender</a>
    <a href="#conocenos">Conócenos</a>
  </nav>
 
  <div class="auth-buttons">
    <?php if ($sesionActiva): ?>
 
      <!-- Usuario logueado -->
      <div class="user-stats">
        <span class="stat"><i class="icon-coin">◈</i><?= number_format($creditos) ?> JBRDCN</span>
        <span class="stat"><i class="icon-xp">⬡</i><?= number_format($experiencia) ?> XP</span>
      </div>
      <div class="user-menu">
        <button class="avatar-btn" onclick="toggleMenu()" title="<?= $usuario ?>">
          <?= $iniciales ?>
        </button>
        <div class="dropdown hidden" id="userDropdown">
          <div class="dropdown-header">
            <strong><?= $usuario ?></strong>
            <span><?= $rol ?></span>
          </div>
          <a href="pages/perfil.php">⚙️ Configuración</a>
          <a href="php/cerrar_sesion.php" class="danger">🚪 Cerrar sesión</a>
        </div>
      </div>
 
    <?php else: ?>
 
      <!-- Sin sesión -->
      <a href="registro1.php" class="btn-outline">Registro</a>
      <a href="login.php" class="btn-primary">Iniciar sesión</a>
 
    <?php endif; ?>
  </div>
</header>
 
 
<!-- ══════════════════════════════════════
     HERO
══════════════════════════════════════ -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-text">
      <?php if ($sesionActiva): ?>
        <p class="hero-saludo">Bienvenido de nuevo,</p>
        <h1>HOLA, <span><?= strtoupper($usuario) ?></span></h1>
        <p class="hero-sub">Continúa tu camino hacia el dominio financiero.</p>
        <a href="#aprender" class="btn-primary btn-lg">Continuar aprendiendo</a>
      <?php else: ?>
        <h1>CRECE CON<br><span>NOSOTROS</span></h1>
        <p class="hero-sub">Aprende a invertir sin riesgos, simula escenarios reales y toma decisiones con conocimiento.</p>
        <div class="hero-actions">
          <a href="registro1.php" class="btn-primary btn-lg">Comenzar gratis</a>
          <a href="#conocenos" class="btn-ghost btn-lg">Saber más</a>
        </div>
      <?php endif; ?>
    </div>
 
    <div class="hero-card" id="conocenos">
      <h2>CONÓCENOS</h2>
      <p>En JBRD Coin creemos que tomar buenas decisiones no es cuestión de suerte, sino de conocimiento.</p>
      <p>Somos una plataforma diseñada para ayudarte a entender el mundo de las inversiones de forma clara y sencilla.</p>
      <p>A través de simuladores y lecciones podrás experimentar distintos escenarios sin riesgos reales.</p>
      <p>No buscamos que simplemente inviertas, sino que comprendas el porqué de cada movimiento.</p>
      <p class="tagline">JBRD Coin — Analiza, simula y decide con conocimiento.</p>
    </div>
  </div>
</section>
 
 
<!-- ══════════════════════════════════════
     SERVICIOS
══════════════════════════════════════ -->
<section class="servicios" id="servicios">
  <div class="section-header">
    <h2>NUESTROS SERVICIOS</h2>
    <p>Todo lo que necesitas para aprender a invertir con inteligencia</p>
  </div>
 
  <div class="servicios-grid">
    <div class="servicio-card" onclick="window.location.href='pages/lecciones.php'">
      <div class="servicio-icon">📚</div>
      <h3>Lecciones</h3>
      <p>Aprende paso a paso los fundamentos del trading, criptomonedas e inversión digital.</p>
      <span class="servicio-tag">Aprendizaje</span>
    </div>
 
    <div class="servicio-card" onclick="window.location.href='pages/simulador.php'">
      <div class="servicio-icon">📈</div>
      <h3>Simulador</h3>
      <p>Practica inversiones con JBRDCN ficticios sin arriesgar dinero real.</p>
      <span class="servicio-tag">Práctica</span>
    </div>
 
    <div class="servicio-card" onclick="window.location.href='pages/misiones.php'">
      <div class="servicio-icon">🎯</div>
      <h3>Misiones</h3>
      <p>Completa retos y obtén experiencia y JBRDCN extra para avanzar más rápido.</p>
      <span class="servicio-tag">Recompensas</span>
    </div>
 
    <div class="servicio-card" onclick="window.location.href='pages/perfil.php'">
      <div class="servicio-icon">🏆</div>
      <h3>Progreso</h3>
      <p>Sube de rango desde Explorador Digital hasta Maestro del Trading.</p>
      <span class="servicio-tag">Rangos</span>
    </div>
  </div>
</section>
 
 
<!-- ══════════════════════════════════════
     SECCIÓN APRENDER (solo con sesión)
══════════════════════════════════════ -->
<?php if ($sesionActiva): ?>
<section class="aprender" id="aprender">
  <div class="section-header">
    <h2>EMPEZAR A APRENDER</h2>
    <p>Completa las lecciones para ganar XP y desbloquear nuevos contenidos</p>
  </div>
  <div class="aprender-cta">
    <a href="pages/lecciones.php" class="btn-primary btn-lg">Ver todas las lecciones</a>
    <a href="pages/misiones.php" class="btn-outline btn-lg">Ver misiones activas</a>
  </div>
</section>
<?php endif; ?>
 
 
<script src="js/app.js"></script>
<script>
  // Header scroll
  window.addEventListener('scroll', function() {
    document.getElementById('header').classList.toggle('scrolled', window.scrollY > 40);
  });
 
  // Avatar dropdown
  function toggleMenu() {
    document.getElementById('userDropdown').classList.toggle('hidden');
  }
  document.addEventListener('click', function(e) {
    var menu = document.querySelector('.user-menu');
    if (menu && !menu.contains(e.target)) {
      var dd = document.getElementById('userDropdown');
      if (dd) dd.classList.add('hidden');
    }
  });
</script>
 
</body>
</html>