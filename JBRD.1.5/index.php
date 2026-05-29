
<?php
session_name('jbrd');
session_start();
 
$sesionActiva = isset($_SESSION['id_cuenta']);
 
$usuario      = $sesionActiva ? htmlspecialchars($_SESSION['usuario']) : '';
$iniciales    = $sesionActiva ? strtoupper(substr($_SESSION['usuario'], 0, 2)) : '';
$creditos     = $sesionActiva ? $_SESSION['creditos'] ?? 0 : 0;
$experiencia  = $sesionActiva ? $_SESSION['experiencia'] ?? 0 : 0;
$rol          = $sesionActiva
  ? htmlspecialchars($_SESSION['nombre_rol'] ?? 'Explorador Digital')
  : '';
?>
 
<!doctype html>
<html lang="es">
 
<head>
 
  <meta charset="UTF-8">
 
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
 
  <title>JBRD Coin</title>
  <script>
    if (localStorage.getItem('tema') === 'claro') {
      document.documentElement.classList.add('tema-claro');
    }
  </script>
 
  <link rel="stylesheet" href="CSS/main.css">

 
</head>
 
<body>
 
<!-- HEADER -->
<header id="header">
 
  <div class="logo">
    <img src="imagenes/logogris.png" alt="JBRD">
    <span>JBRD</span>
  </div>
 
  <nav>
 
    <a href="#servicios">Servicios</a>
 
    <?php if ($sesionActiva): ?>
      <a href="#aprender">Aprender</a>
    <?php endif; ?>
 
    <a href="#conocenos">Conócenos</a>
 
  </nav>
 
  <div class="auth-buttons">
 
    <?php if ($sesionActiva): ?>
 
      <div class="user-stats">
 
        <span class="stat">
          <i class="icon-coin">◈</i>
          <?= number_format($creditos) ?> JBRDCN
        </span>
 
        <span class="stat">
          <i class="icon-xp">⬡</i>
          <?= number_format($experiencia) ?> XP
        </span>
 
      </div>
 
      <div class="user-menu">
 
        <button class="avatar-btn"
                onclick="toggleMenu()"
                title="<?= $usuario ?>">
 
          <?= $iniciales ?>
 
        </button>
 
        <div class="dropdown hidden"
             id="userDropdown">
 
          <div class="dropdown-header">
 
            <strong><?= $usuario ?></strong>
 
            <span><?= $rol ?></span>
 
          </div>
 
          <!-- 👤 Perfil — nuevo, encima de configuración -->
          <a href="paginas/perfil.php">
            👤 Perfil
          </a>
 
     <a href="configuracion.php">
    ⚙️ Configuración
</a>
          <a href="php/cerrar_sesion.php"
             class="danger">
 
            🚪 Cerrar sesión
 
          </a>
 
        </div>
 
      </div>
 
    <?php else: ?>
 
      <a href="registro1.php"
         class="btn-outline">
 
         Registro
 
      </a>
 
      <a href="login.php"
         class="btn-primary">
 
         Iniciar sesión
 
      </a>
 
    <?php endif; ?>
 
  </div>
 
</header>
 
 
<!-- HERO -->
<section class="hero">
 
  <div class="hero-content">
 
    <div class="hero-text">
 
      <?php if ($sesionActiva): ?>
 
        <p class="hero-saludo">
          Bienvenido de nuevo,
        </p>
 
        <h1>
          HOLA,
          <span><?= strtoupper($usuario) ?></span>
        </h1>
 
        <p class="hero-sub">
          Continúa tu camino hacia el dominio financiero.
        </p>
 
        <a href="paginas/lecciones.php"
           class="btn-primary btn-lg">
 
           Continuar aprendiendo
 
        </a>
 
      <?php else: ?>
 
        <h1>
          CRECE CON<br>
          <span>NOSOTROS</span>
        </h1>
 
        <p class="hero-sub">
          Aprende a invertir sin riesgos,
          simula escenarios reales y toma
          decisiones con conocimiento.
        </p>
 
        <div class="hero-actions">
 
          <a href="registro1.php"
             class="btn-primary btn-lg">
 
             Comenzar gratis
 
          </a>
 
          <a href="#conocenos"
             class="btn-ghost btn-lg">
 
             Saber más
 
          </a>
 
        </div>
 
      <?php endif; ?>
 
    </div>
 
 
    <!-- CARD -->
    <div class="hero-card"
         id="conocenos">
 
      <h2>CONÓCENOS</h2>
 
      <p>
        En JBRD Coin creemos que tomar buenas
        decisiones no es cuestión de suerte,
        sino de conocimiento.
      </p>
 
      <p>
        Somos una plataforma diseñada para ayudarte
        a entender el mundo de las inversiones.
      </p>
 
      <p>
        Aprende mediante simulaciones y escenarios
        reales sin arriesgar dinero.
      </p>
 
      <p class="tagline">
        JBRD Coin — Analiza, simula y decide.
      </p>
 
    </div>
 
  </div>
 
</section>
 
 
<!-- MARKET -->
<section class="market-section">
 
  <!-- TOP -->
  <div class="market-top">
 
    <div class="market-live">
      <span class="live-dot"></span>
      EN VIVO
    </div>
 
    <div id="marketClock"></div>
 
    <div id="marketStatus"></div>
 
  </div>
 
  <!-- GRID -->
  <div class="market-grid">
 
    <!-- BTC -->
    <div class="market-card">
 
      <div class="market-header">
 
        <h3>Bitcoin / USDT</h3>
 
        <span>BTCUSDT</span>
 
      </div>
 
      <div class="tradingview-widget-container" id="btc_container">
        <div class="tradingview-widget-container__widget"></div>
      </div>
 
    </div>
 
    <!-- DXY -->
    <div class="market-card">
 
      <div class="market-header">
 
        <h3>US Dollar </h3>
 
        <span>DXY</span>
 
      </div>
 
      <div class="tradingview-widget-container" id="dxy_container">
        <div class="tradingview-widget-container__widget"></div>
      </div>
 
    </div>
 
  </div>
 
</section>
 
<!-- SERVICIOS -->
<section class="servicios"
         id="servicios">
 
  <div class="section-header">
 
    <h2>NUESTROS SERVICIOS</h2>
 
    <p>
      Todo lo que necesitas para aprender
      a invertir con inteligencia
    </p>
 
  </div>
 
  <div class="servicios-grid">
 
    <div class="servicio-card"
         onclick="window.location.href='paginas/lecciones.php'">
 
      <div class="servicio-icon">📚</div>
 
      <h3>Lecciones</h3>
 
      <p>
        Aprende trading, criptomonedas
        e inversión digital.
      </p>
 
      <span class="servicio-tag">
        Aprendizaje
      </span>
 
    </div>
 
 
    <div class="servicio-card"
         onclick="window.location.href='paginas/simulador.php'">
 
      <div class="servicio-icon">📈</div>
 
      <h3>Simulador</h3>
 
      <p>
        Practica inversiones sin
        dinero real.
      </p>
 
      <span class="servicio-tag">
        Práctica
      </span>
 
    </div>
 
 
    <div class="servicio-card"
         onclick="window.location.href='paginas/misiones.php'">
 
      <div class="servicio-icon">🎯</div>
 
      <h3>Misiones</h3>
 
      <p>
        Completa retos y gana XP
        y monedas.
      </p>
 
      <span class="servicio-tag">
        Recompensas
      </span>
 
    </div>
 
 
    <div class="servicio-card"
         onclick="window.location.href='paginas/perfil.php'">
 
      <div class="servicio-icon">🏆</div>
 
      <h3>Progreso</h3>
 
      <p>
        Avanza de rango y conviértete
        en maestro del trading.
      </p>
 
      <span class="servicio-tag">
        Rangos
      </span>
 
    </div>
 
  </div>
 
</section>
 
 
<!-- APRENDER -->
<?php if ($sesionActiva): ?>
 
<section class="aprender"
         id="aprender">
 
  <div class="section-header">
 
    <h2>EMPEZAR A APRENDER</h2>
 
    <p>
      Completa las lecciones para
      ganar experiencia.
    </p>
 
  </div>
 
  <div class="aprender-cta">
 
    <a href="paginas/lecciones.php"
       class="btn-primary btn-lg">
 
       Ver todas las lecciones
 
    </a>
 
    <a href="paginas/misiones.php"
       class="btn-outline btn-lg">
 
       Ver misiones activas
 
    </a>
 
  </div>
 
</section>
 
<?php endif; ?>
 
<script src="https://s3.tradingview.com/tv.js"></script>
<script src="js/app.js"></script>
 
</body>
</html>