<?php
session_name('jbrd');
session_start();
 
// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['id_cuenta'])) {
    header('Location: ../login.php');
    exit;
}
 
$id_cuenta   = $_SESSION['id_cuenta'];
$usuario     = htmlspecialchars($_SESSION['usuario']);
$iniciales   = strtoupper(substr($_SESSION['usuario'], 0, 2));
$creditos    = $_SESSION['creditos']    ?? 0;
$experiencia = $_SESSION['experiencia'] ?? 0;
$rol         = htmlspecialchars($_SESSION['nombre_rol'] ?? 'Explorador Digital');
 
// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'jbrd');
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
 
// Obtener datos completos del usuario
$stmt = $conn->prepare("
    SELECT c.usuario,
           c.creditos,
           c.experiencia,
           c.fecha_registro,
           u.correo,
           r.nombre_rol
    FROM cuentas c
    LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
    LEFT JOIN roles    r ON c.id_rol     = r.id_rol
    WHERE c.id_cuenta = ?
    LIMIT 1
");
$stmt->bind_param('i', $id_cuenta);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();
$stmt->close();
 
// Lecciones completadas
$stmtL = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM progreso_lecciones
    WHERE id_cuenta = ? AND completado = 1
");
$stmtL->bind_param('i', $id_cuenta);
$stmtL->execute();
$lecciones_completadas = $stmtL->get_result()->fetch_assoc()['total'] ?? 0;
$stmtL->close();
 
// Misiones completadas
$stmtM = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM cuenta_misiones
    WHERE id_cuenta = ? AND completada = 1
");
$stmtM->bind_param('i', $id_cuenta);
$stmtM->execute();
$misiones_completadas = $stmtM->get_result()->fetch_assoc()['total'] ?? 0;
$stmtM->close();
 
$conn->close();
 
// Valores finales
$creditos    = $datos['creditos']    ?? $creditos;
$experiencia = $datos['experiencia'] ?? $experiencia;
$rol         = htmlspecialchars($datos['nombre_rol'] ?? $rol);
$correo      = htmlspecialchars($datos['correo']     ?? '—');
$fecha_raw   = $datos['fecha_registro'] ?? null;
$fecha_ingreso = $fecha_raw ? date('d/m/Y', strtotime($fecha_raw)) : '—';
 
// Progreso XP hacia siguiente rango
$xp_siguiente = 1000;
$xp_parcial   = $experiencia % $xp_siguiente;
$porcentaje   = min(100, round($xp_parcial / $xp_siguiente * 100));
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil — JBRD Coin</title>
  <script>
    if (localStorage.getItem('tema') === 'claro') {
      document.documentElement.classList.add('tema-claro');
    }
  </script>
  <link rel="stylesheet" href="../CSS/main.css">
  <style>
    .perfil-wrapper {
      max-width: 900px;
      margin: 120px auto 60px;
      padding: 0 1.5rem;
    }
 
    /* Hero */
    .perfil-hero {
      background: var(--card-bg, #1a1a2e);
      border: 1px solid var(--border, rgba(255,255,255,.1));
      border-radius: 1.25rem;
      padding: 2.5rem 2rem;
      display: flex;
      align-items: center;
      gap: 2rem;
      margin-bottom: 1.75rem;
    }
 
    .perfil-avatar {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: var(--accent, #6c63ff);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: 700;
      color: #fff;
      flex-shrink: 0;
      letter-spacing: .05em;
      box-shadow: 0 0 0 4px rgba(108,99,255,.25);
    }
 
    .perfil-info h1 {
      font-size: 1.8rem;
      font-weight: 700;
      margin: 0 0 .3rem;
      color: var(--text, #fff);
    }
 
    .perfil-info .rol-badge {
      display: inline-block;
      padding: .25rem .75rem;
      border-radius: 999px;
      background: rgba(108,99,255,.18);
      color: var(--accent, #6c63ff);
      font-size: .8rem;
      font-weight: 600;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: .6rem;
    }
 
    .perfil-info .meta {
      font-size: .85rem;
      color: var(--text-muted, #888);
    }
 
    /* Botón configuración en hero */
    .perfil-hero-actions {
      margin-left: auto;
      flex-shrink: 0;
    }
 
    .btn-config {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      padding: .55rem 1.1rem;
      border-radius: 8px;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.1);
      color: var(--text-muted, #888);
      text-decoration: none;
      font-size: .85rem;
      font-weight: 500;
      transition: all .2s;
    }
 
    .btn-config:hover {
      background: rgba(255,255,255,.1);
      border-color: rgba(255,255,255,.2);
      color: var(--text, #fff);
    }
 
    .btn-config svg {
      width: 15px;
      height: 15px;
      flex-shrink: 0;
    }
 
    /* Stats */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
      margin-bottom: 1.75rem;
    }
 
    .stat-card {
      background: var(--card-bg, #1a1a2e);
      border: 1px solid var(--border, rgba(255,255,255,.1));
      border-radius: 1rem;
      padding: 1.5rem 1.25rem;
      text-align: center;
      transition: transform .2s, border-color .2s;
    }
 
    .stat-card:hover {
      transform: translateY(-3px);
      border-color: var(--accent, #6c63ff);
    }
 
    .stat-card .stat-icon  { font-size: 1.75rem; margin-bottom: .5rem; }
    .stat-card .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--accent, #6c63ff); line-height: 1; margin-bottom: .25rem; }
    .stat-card .stat-label { font-size: .78rem; color: var(--text-muted, #888); text-transform: uppercase; letter-spacing: .07em; }
 
    /* Barra XP */
    .xp-section {
      background: var(--card-bg, #1a1a2e);
      border: 1px solid var(--border, rgba(255,255,255,.1));
      border-radius: 1rem;
      padding: 1.5rem 1.75rem;
      margin-bottom: 1.75rem;
    }
 
    .xp-section h3 {
      font-size: .9rem;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: var(--text-muted, #888);
      margin: 0 0 1rem;
    }
 
    .xp-bar-wrap {
      background: rgba(255,255,255,.07);
      border-radius: 999px;
      height: 10px;
      overflow: hidden;
    }
 
    .xp-bar-fill {
      height: 100%;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--accent, #6c63ff), #a78bfa);
      width: 0;
      transition: width .8s ease;
    }
 
    .xp-labels {
      display: flex;
      justify-content: space-between;
      font-size: .78rem;
      color: var(--text-muted, #888);
      margin-top: .5rem;
    }
 
    /* Acciones */
    .acciones-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }
 
    .accion-card {
      background: var(--card-bg, #1a1a2e);
      border: 1px solid var(--border, rgba(255,255,255,.1));
      border-radius: 1rem;
      padding: 1.5rem 1.25rem;
      text-align: center;
      text-decoration: none;
      color: var(--text, #fff);
      transition: transform .2s, border-color .2s, background .2s;
      cursor: pointer;
    }
 
    .accion-card:hover {
      transform: translateY(-3px);
      border-color: var(--accent, #6c63ff);
      background: rgba(108,99,255,.08);
    }
 
    .accion-card .ac-icon { font-size: 1.75rem; margin-bottom: .6rem; }
    .accion-card h4      { margin: 0 0 .3rem; font-size: 1rem; }
    .accion-card p       { margin: 0; font-size: .8rem; color: var(--text-muted, #888); }
 
    .accion-card.danger:hover { border-color: #ef4444; background: rgba(239,68,68,.08); }
    .accion-card.danger h4    { color: #ef4444; }
 
    .accion-card.config:hover { border-color: #e8c468; background: rgba(232,196,104,.08); }
    .accion-card.config h4    { color: #e8c468; }
 
    @media (max-width: 600px) {
      .perfil-hero { flex-direction: column; text-align: center; }
      .perfil-hero-actions { margin-left: 0; }
    }
  </style>
</head>
<body>
 
<!-- HEADER -->
<header id="header">
  <div class="logo">
    <img src="../imagenes/logogris.png" alt="JBRD">
    <span>JBRD</span>
  </div>
 
  <nav>
    <a href="../index.php#servicios">Servicios</a>
    <a href="../index.php#aprender">Aprender</a>
    <a href="../index.php#conocenos">Conócenos</a>
  </nav>
 
  <div class="auth-buttons">
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
      <button class="avatar-btn" onclick="toggleMenu()" title="<?= $usuario ?>">
        <?= $iniciales ?>
      </button>
 
      <div class="dropdown hidden" id="userDropdown">
        <div class="dropdown-header">
          <strong><?= $usuario ?></strong>
          <span><?= $rol ?></span>
        </div>
        <a href="perfil.php">👤 Perfil</a>
        <a href="../configuracion.php">⚙️ Configuración</a>
        <a href="../php/cerrar_sesion.php" class="danger">🚪 Cerrar sesión</a>
      </div>
    </div>
  </div>
</header>
 
<!-- PERFIL -->
<main class="perfil-wrapper">
 
  <!-- Hero -->
  <div class="perfil-hero">
    <div class="perfil-avatar"><?= $iniciales ?></div>
    <div class="perfil-info">
      <h1><?= $usuario ?></h1>
      <span class="rol-badge"><?= $rol ?></span>
      <p class="meta">
        📧 <?= $correo ?> &nbsp;·&nbsp;
        📅 Miembro desde <?= $fecha_ingreso ?>
      </p>
    </div>
    <div class="perfil-hero-actions">
      <a href="../configuracion.php" class="btn-config">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
        Configuración
      </a>
    </div>
  </div>
 
  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">◈</div>
      <div class="stat-value"><?= number_format($creditos) ?></div>
      <div class="stat-label">JBRD Coins</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">⬡</div>
      <div class="stat-value"><?= number_format($experiencia) ?></div>
      <div class="stat-label">Experiencia XP</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📚</div>
      <div class="stat-value"><?= $lecciones_completadas ?></div>
      <div class="stat-label">Lecciones completadas</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🎯</div>
      <div class="stat-value"><?= $misiones_completadas ?></div>
      <div class="stat-label">Misiones completadas</div>
    </div>
  </div>
 
  <!-- Barra XP -->
  <div class="xp-section">
    <h3>Progreso hacia el siguiente rango</h3>
    <div class="xp-bar-wrap">
      <div class="xp-bar-fill" id="xpBar" data-pct="<?= $porcentaje ?>"></div>
    </div>
    <div class="xp-labels">
      <span><?= number_format($xp_parcial) ?> XP</span>
      <span><?= $porcentaje ?>%</span>
      <span><?= number_format($xp_siguiente) ?> XP</span>
    </div>
  </div>
 
  <!-- Acciones rápidas -->
  <div class="acciones-grid">
    <a href="lecciones.php" class="accion-card">
      <div class="ac-icon">📚</div>
      <h4>Lecciones</h4>
      <p>Continúa aprendiendo</p>
    </a>
    <a href="simulador.php" class="accion-card">
      <div class="ac-icon">📈</div>
      <h4>Simulador</h4>
      <p>Practica inversiones</p>
    </a>
    <a href="misiones.php" class="accion-card">
      <div class="ac-icon">🎯</div>
      <h4>Misiones</h4>
      <p>Ver misiones activas</p>
    </a>
    <a href="../configuracion.php" class="accion-card config">
      <div class="ac-icon">⚙️</div>
      <h4>Configuración</h4>
      <p>Edita tu cuenta</p>
    </a>
    <a href="../php/cerrar_sesion.php" class="accion-card danger">
      <div class="ac-icon">🚪</div>
      <h4>Cerrar sesión</h4>
      <p>Salir de tu cuenta</p>
    </a>
  </div>
 
</main>
 
<script src="../js/app.js"></script>
<script>
  // Animar barra XP al cargar
  window.addEventListener('load', () => {
    const bar = document.getElementById('xpBar');
    if (bar) bar.style.width = bar.dataset.pct + '%';
  });
</script>
 
</body>
</html>
 