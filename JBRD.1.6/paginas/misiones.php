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
 
// ──────────────────────────────────────────────
// Reclamar recompensa de una misión (POST)
// ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reclamar_mision'])) {
    $id_mision = (int) $_POST['reclamar_mision'];
 
    $conn->begin_transaction();
    try {
        // Verificar que la misión esté completada y no reclamada
        $stmtCheck = $conn->prepare("
            SELECT cm.completada, cm.reclamada, m.recompensa_creditos, m.recompensa_experiencia
            FROM cuenta_misiones cm
            JOIN misiones m ON m.id_mision = cm.id_mision
            WHERE cm.id_cuenta = ? AND cm.id_mision = ?
            LIMIT 1
        ");
        $stmtCheck->bind_param('ii', $id_cuenta, $id_mision);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();
 
        if ($res && (int)$res['completada'] === 1 && (int)$res['reclamada'] === 0) {
            $recompensa_creditos = (int) $res['recompensa_creditos'];
            $recompensa_xp       = (int) $res['recompensa_experiencia'];
 
            $stmtUpd = $conn->prepare("
                UPDATE cuenta_misiones
                SET reclamada = 1, fecha_reclamo = NOW()
                WHERE id_cuenta = ? AND id_mision = ?
            ");
            $stmtUpd->bind_param('ii', $id_cuenta, $id_mision);
            $stmtUpd->execute();
            $stmtUpd->close();
 
            $stmtCu = $conn->prepare("
                UPDATE cuentas
                SET creditos = creditos + ?, experiencia = experiencia + ?
                WHERE id_cuenta = ?
            ");
            $stmtCu->bind_param('iii', $recompensa_creditos, $recompensa_xp, $id_cuenta);
            $stmtCu->execute();
            $stmtCu->close();
 
            $conn->commit();
 
            // Actualizar sesión para que el header muestre el total correcto
            $_SESSION['creditos']    = ($_SESSION['creditos']    ?? 0) + $recompensa_creditos;
            $_SESSION['experiencia'] = ($_SESSION['experiencia'] ?? 0) + $recompensa_xp;
        } else {
            $conn->rollback();
        }
    } catch (Exception $e) {
        $conn->rollback();
    }
 
    header('Location: misiones.php');
    exit;
}
 
// ──────────────────────────────────────────────
// Sincronizar valores actuales (créditos/XP) por si vienen desactualizados
// ──────────────────────────────────────────────
$stmtCu = $conn->prepare("SELECT creditos, experiencia FROM cuentas WHERE id_cuenta = ? LIMIT 1");
$stmtCu->bind_param('i', $id_cuenta);
$stmtCu->execute();
$cuentaRow = $stmtCu->get_result()->fetch_assoc();
$stmtCu->close();
if ($cuentaRow) {
    $creditos    = $cuentaRow['creditos'];
    $experiencia = $cuentaRow['experiencia'];
}
 
// ──────────────────────────────────────────────
// Obtener todas las misiones + estado para esta cuenta
// ──────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT
        m.id_mision,
        m.nombre_mision,
        m.descripcion,
        m.tipo,
        m.meta,
        m.recompensa_creditos,
        m.recompensa_experiencia,
        COALESCE(cm.completada, 0) AS completada,
        COALESCE(cm.reclamada, 0)  AS reclamada
    FROM misiones m
    LEFT JOIN cuenta_misiones cm
        ON cm.id_mision = m.id_mision AND cm.id_cuenta = ?
    ORDER BY completada ASC, m.id_mision ASC
");
$stmt->bind_param('i', $id_cuenta);
$stmt->execute();
$misiones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
 
$conn->close();
 
// Iconos según el tipo de misión
function iconoMision(string $tipo): string {
    return match ($tipo) {
        'leccion'    => '📚',
        'practica'   => '📈',
        'simulador'  => '📈',
        'racha'      => '🔥',
        default      => '🎯',
    };
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Misiones — JBRD Coin</title>
  <script>
    if (localStorage.getItem('tema') === 'claro') {
      document.documentElement.classList.add('tema-claro');
    }
  </script>
  <link rel="stylesheet" href="../CSS/main.css">
  <style>
    .misiones-wrapper {
      max-width: 900px;
      margin: 120px auto 60px;
      padding: 0 1.5rem;
    }
 
    .misiones-header {
      margin-bottom: 1.75rem;
    }
 
    .misiones-header h1 {
      font-size: 1.8rem;
      font-weight: 700;
      margin: 0 0 .35rem;
      color: var(--text, #fff);
    }
 
    .misiones-header p {
      margin: 0;
      font-size: .9rem;
      color: var(--text-muted, #888);
    }
 
    .misiones-resumen {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.75rem;
      flex-wrap: wrap;
    }
 
    .resumen-chip {
      background: var(--card-bg, #1a1a2e);
      border: 1px solid var(--border, rgba(255,255,255,.1));
      border-radius: 999px;
      padding: .5rem 1.1rem;
      font-size: .82rem;
      color: var(--text-muted, #888);
      display: flex;
      align-items: center;
      gap: .4rem;
    }
 
    .resumen-chip strong {
      color: var(--accent, #6c63ff);
      font-weight: 700;
    }
 
    .mision-card {
      background: var(--card-bg, #1a1a2e);
      border: 1px solid var(--border, rgba(255,255,255,.1));
      border-radius: 1.1rem;
      padding: 1.5rem 1.6rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 1.25rem;
      transition: border-color .2s, transform .2s;
    }
 
    .mision-card:hover {
      border-color: var(--accent, #6c63ff);
      transform: translateY(-2px);
    }
 
    .mision-card.completada {
      border-color: rgba(74, 222, 128, .35);
    }
 
    .mision-card.reclamada {
      opacity: .55;
    }
 
    .mision-icon {
      width: 54px;
      height: 54px;
      flex-shrink: 0;
      border-radius: .9rem;
      background: rgba(108,99,255,.12);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
    }
 
    .mision-card.completada .mision-icon {
      background: rgba(74,222,128,.14);
    }
 
    .mision-body {
      flex: 1;
      min-width: 0;
    }
 
    .mision-body h3 {
      margin: 0 0 .25rem;
      font-size: 1.05rem;
      color: var(--text, #fff);
    }
 
    .mision-body p {
      margin: 0 0 .6rem;
      font-size: .85rem;
      color: var(--text-muted, #888);
    }
 
    .mision-recompensas {
      display: flex;
      gap: .9rem;
      font-size: .8rem;
      font-weight: 600;
    }
 
    .recompensa-coin  { color: #e8c468; }
    .recompensa-xp    { color: var(--accent, #6c63ff); }
 
    .mision-accion {
      flex-shrink: 0;
    }
 
    .btn-mision {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .55rem 1.2rem;
      border-radius: 999px;
      border: none;
      font-size: .82rem;
      font-weight: 600;
      cursor: pointer;
      transition: all .2s;
    }
 
    .btn-mision.disponible {
      background: rgba(255,255,255,.07);
      color: var(--text-muted, #888);
    }
 
    .btn-mision.lista {
      background: var(--accent, #6c63ff);
      color: #fff;
    }
 
    .btn-mision.lista:hover {
      background: #5750e0;
    }
 
    .btn-mision.completo {
      background: rgba(74,222,128,.15);
      color: #4ade80;
      cursor: default;
    }
 
    .estado-tag {
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      padding: .25rem .6rem;
      border-radius: 999px;
      background: rgba(74,222,128,.15);
      color: #4ade80;
    }
 
    .sin-misiones {
      text-align: center;
      padding: 3rem 1rem;
      color: var(--text-muted, #888);
    }
 
    @media (max-width: 600px) {
      .mision-card {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
      }
      .mision-accion { width: 100%; }
      .btn-mision { width: 100%; justify-content: center; }
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
 
<!-- MISIONES -->
<main class="misiones-wrapper">
 
  <div class="misiones-header">
    <h1>Misiones</h1>
    <p>Completa misiones para ganar JBRD Coins y experiencia extra.</p>
  </div>
 
  <?php
    $totalMisiones      = count($misiones);
    $totalCompletadas   = count(array_filter($misiones, fn($m) => (int)$m['completada'] === 1));
  ?>
  <div class="misiones-resumen">
    <span class="resumen-chip">🎯 <strong><?= $totalCompletadas ?></strong>/<?= $totalMisiones ?> completadas</span>
  </div>
 
  <?php if (empty($misiones)): ?>
    <div class="sin-misiones">
      <p>No hay misiones disponibles por el momento. ¡Vuelve pronto!</p>
    </div>
  <?php else: ?>
    <?php foreach ($misiones as $m): ?>
      <?php
        $completada = (int) $m['completada'] === 1;
        $reclamada  = (int) $m['reclamada']  === 1;
        $claseExtra = $completada ? ' completada' : '';
        $claseExtra .= $reclamada ? ' reclamada' : '';
      ?>
      <div class="mision-card<?= $claseExtra ?>">
        <div class="mision-icon"><?= iconoMision($m['tipo']) ?></div>
        <div class="mision-body">
          <h3><?= htmlspecialchars($m['nombre_mision']) ?></h3>
          <p><?= htmlspecialchars($m['descripcion'] ?? '') ?></p>
          <div class="mision-recompensas">
            <span class="recompensa-coin">◈ <?= number_format($m['recompensa_creditos']) ?> JBRDCN</span>
            <span class="recompensa-xp">⬡ <?= number_format($m['recompensa_experiencia']) ?> XP</span>
          </div>
        </div>
        <div class="mision-accion">
          <?php if ($reclamada): ?>
            <span class="estado-tag">✓ Reclamada</span>
          <?php elseif ($completada): ?>
            <form method="POST">
              <input type="hidden" name="reclamar_mision" value="<?= (int) $m['id_mision'] ?>">
              <button type="submit" class="btn-mision lista">Reclamar 🎁</button>
            </form>
          <?php else: ?>
            <button type="button" class="btn-mision disponible" disabled>En progreso</button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
 
</main>
 
<script src="../js/app.js"></script>
 
</body>
</html>