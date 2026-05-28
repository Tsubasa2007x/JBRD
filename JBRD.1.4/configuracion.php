<?php
// php/configuracion.php
session_start();
require_once 'php/conexion.php';
 
// --- Protección de sesión ---
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
 
$id_usuario = $_SESSION['id_usuario'];
$mensajes   = [];
$errores    = [];
 
// ── Cargar datos actuales del usuario ──────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}
 
// ── Procesar formularios POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
 
    // ── 1. Actualizar datos personales ──────────────────────────────────────
    if ($accion === 'datos_personales') {
        $nombre           = trim($_POST['nombre'] ?? '');
        $apellido         = trim($_POST['apellido'] ?? '');
        $telefono         = trim($_POST['telefono'] ?? '');
        $tipo_documento   = trim($_POST['tipo_documento'] ?? '');
        $numero_documento = trim($_POST['numero_documento'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
        $fecha_expedicion = $_POST['fecha_expedicion'] ?? null;
 
        if (empty($nombre) || empty($apellido)) {
            $errores[] = 'El nombre y apellido son obligatorios.';
        }
 
        // Verificar unicidad del documento (excluyendo el propio usuario)
        if (!empty($numero_documento)) {
            $chk = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE numero_documento = ? AND id_usuario != ?");
            $chk->execute([$numero_documento, $id_usuario]);
            if ($chk->fetch()) {
                $errores[] = 'Ese número de documento ya está registrado por otro usuario.';
            }
        }
 
        if (empty($errores)) {
            $upd = $pdo->prepare("
                UPDATE usuarios SET
                    nombre           = ?,
                    apellido         = ?,
                    telefono         = ?,
                    tipo_documento   = ?,
                    numero_documento = ?,
                    fecha_nacimiento = ?,
                    fecha_expedicion = ?
                WHERE id_usuario = ?
            ");
            $upd->execute([
                $nombre,
                $apellido,
                $telefono ?: null,
                $tipo_documento,
                $numero_documento,
                $fecha_nacimiento ?: null,
                $fecha_expedicion ?: null,
                $id_usuario,
            ]);
 
            // Actualizar sesión con el nuevo nombre
            $_SESSION['nombre'] = $nombre;
 
            $mensajes[] = 'Datos personales actualizados correctamente.';
 
            // Recargar datos
            $stmt->execute([$id_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
 
    // ── 2. Actualizar correo ─────────────────────────────────────────────────
    if ($accion === 'cambiar_correo') {
        $nuevo_correo  = trim($_POST['nuevo_correo'] ?? '');
        $confirmar     = trim($_POST['confirmar_correo'] ?? '');
 
        if (!filter_var($nuevo_correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        } elseif ($nuevo_correo !== $confirmar) {
            $errores[] = 'Los correos ingresados no coinciden.';
        } else {
            $chk = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = ? AND id_usuario != ?");
            $chk->execute([$nuevo_correo, $id_usuario]);
            if ($chk->fetch()) {
                $errores[] = 'Ese correo ya está en uso por otro usuario.';
            }
        }
 
        if (empty($errores)) {
            $pdo->prepare("UPDATE usuarios SET correo = ? WHERE id_usuario = ?")->execute([$nuevo_correo, $id_usuario]);
            $_SESSION['correo'] = $nuevo_correo;
            $mensajes[] = 'Correo electrónico actualizado correctamente.';
            $stmt->execute([$id_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
 
    // ── 3. Cambiar contraseña ────────────────────────────────────────────────
    if ($accion === 'cambiar_password') {
        $actual    = $_POST['password_actual'] ?? '';
        $nueva     = $_POST['password_nueva'] ?? '';
        $confirmar = $_POST['password_confirmar'] ?? '';
 
        // Verificar contraseña actual (asumiendo que se guarda con password_hash)
        $chk = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
        $chk->execute([$id_usuario]);
        $row = $chk->fetch(PDO::FETCH_ASSOC);
 
        if (!$row || !password_verify($actual, $row['password'] ?? '')) {
            $errores[] = 'La contraseña actual es incorrecta.';
        } elseif (strlen($nueva) < 8) {
            $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } elseif ($nueva !== $confirmar) {
            $errores[] = 'Las nuevas contraseñas no coinciden.';
        }
 
        if (empty($errores)) {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?")->execute([$hash, $id_usuario]);
            $mensajes[] = 'Contraseña actualizada correctamente.';
        }
    }
}
 
// Helper para escapar salida HTML
function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Configuración · JBRD</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link  rel="stylesheet" href="CSS/main.css">
<style>
/* ─── Reset & variables ─────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
:root {
  --bg:          #0c0e13;
  --bg2:         #13161e;
  --bg3:         #1a1e29;
  --border:      rgba(255,255,255,.07);
  --accent:      #e8c468;
  --accent2:     #c9a84c;
  --text:        #e8eaf0;
  --text-muted:  #7a8096;
  --danger:      #e05555;
  --success:     #4caf7d;
  --radius:      14px;
  --trans:       .22s cubic-bezier(.4,0,.2,1);
}
 
html { scroll-behavior: smooth; }
 
body {
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  line-height: 1.6;
}
 
/* ─── Layout ────────────────────────────────────────────────────────────── */
.page {
  display: grid;
  grid-template-columns: 260px 1fr;
  min-height: 100vh;
}
 
/* ─── Sidebar ───────────────────────────────────────────────────────────── */
.sidebar {
  background: var(--bg2);
  border-right: 1px solid var(--border);
  padding: 2rem 0;
  position: sticky;
  top: 0;
  height: 100vh;
  display: flex;
  flex-direction: column;
}
 
.sidebar-brand {
  padding: 0 1.75rem 2rem;
  border-bottom: 1px solid var(--border);
  margin-bottom: 1.5rem;
}
 
.brand-title {
  font-family: 'Syne', sans-serif;
  font-size: 1.5rem;
  font-weight: 800;
  letter-spacing: -.5px;
  color: var(--accent);
}
 
.brand-sub {
  font-size: .75rem;
  color: var(--text-muted);
  letter-spacing: .08em;
  text-transform: uppercase;
}
 
.nav-label {
  font-size: .7rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--text-muted);
  padding: .25rem 1.75rem .5rem;
  margin-top: .5rem;
}
 
.nav-link {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .7rem 1.75rem;
  color: var(--text-muted);
  text-decoration: none;
  font-size: .92rem;
  font-weight: 500;
  transition: background var(--trans), color var(--trans);
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  border-radius: 0;
}
 
.nav-link svg { width: 17px; height: 17px; flex-shrink: 0; }
 
.nav-link:hover  { background: rgba(255,255,255,.04); color: var(--text); }
.nav-link.active { background: rgba(232,196,104,.08); color: var(--accent); }
 
.nav-link .dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--accent);
  margin-left: auto;
  opacity: 0;
  transition: opacity var(--trans);
}
.nav-link.active .dot { opacity: 1; }
 
.sidebar-footer {
  margin-top: auto;
  padding: 1.25rem 1.75rem 0;
  border-top: 1px solid var(--border);
}
 
.avatar-row {
  display: flex;
  align-items: center;
  gap: .85rem;
}
 
.avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), #a87c2a);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: .95rem;
  color: #0c0e13;
  flex-shrink: 0;
}
 
.avatar-info { flex: 1; min-width: 0; }
.avatar-name { font-size: .88rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.avatar-role { font-size: .73rem; color: var(--text-muted); }
 
/* ─── Main content ──────────────────────────────────────────────────────── */
.main {
  padding: 2.5rem 3rem;
  max-width: 820px;
}
 
.page-header {
  margin-bottom: 2.5rem;
}
 
.page-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: -.5px;
  line-height: 1.1;
}
 
.page-header h1 span { color: var(--accent); }
 
.page-header p {
  color: var(--text-muted);
  margin-top: .4rem;
  font-size: .95rem;
}
 
/* ─── Tabs ──────────────────────────────────────────────────────────────── */
.tabs {
  display: flex;
  gap: .25rem;
  border-bottom: 1px solid var(--border);
  margin-bottom: 2rem;
}
 
.tab-btn {
  background: none;
  border: none;
  color: var(--text-muted);
  font-family: 'DM Sans', sans-serif;
  font-size: .9rem;
  font-weight: 500;
  padding: .65rem 1.1rem;
  cursor: pointer;
  position: relative;
  transition: color var(--trans);
}
 
.tab-btn::after {
  content: '';
  position: absolute;
  bottom: -1px; left: 0; right: 0;
  height: 2px;
  background: var(--accent);
  border-radius: 2px 2px 0 0;
  transform: scaleX(0);
  transition: transform var(--trans);
}
 
.tab-btn.active { color: var(--accent); }
.tab-btn.active::after { transform: scaleX(1); }
.tab-btn:hover:not(.active) { color: var(--text); }
 
/* ─── Tab panels ────────────────────────────────────────────────────────── */
.tab-panel { display: none; }
.tab-panel.active { display: block; }
 
/* ─── Cards ─────────────────────────────────────────────────────────────── */
.card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.75rem 2rem;
  margin-bottom: 1.5rem;
}
 
.card-title {
  font-family: 'Syne', sans-serif;
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 1.25rem;
  display: flex;
  align-items: center;
  gap: .6rem;
}
 
.card-title svg { color: var(--accent); }
 
/* ─── Form ───────────────────────────────────────────────────────────────── */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem 1.25rem;
}
 
.form-grid .full { grid-column: 1 / -1; }
 
.field { display: flex; flex-direction: column; gap: .45rem; }
 
.field label {
  font-size: .8rem;
  font-weight: 500;
  letter-spacing: .04em;
  color: var(--text-muted);
  text-transform: uppercase;
}
 
.field input,
.field select {
  background: var(--bg3);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  font-family: 'DM Sans', sans-serif;
  font-size: .93rem;
  padding: .65rem .9rem;
  transition: border-color var(--trans), box-shadow var(--trans);
  outline: none;
  width: 100%;
}
 
.field input:focus,
.field select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(232,196,104,.12);
}
 
.field select option { background: var(--bg3); }
 
.field input[readonly] {
  opacity: .55;
  cursor: not-allowed;
}
 
/* ─── Buttons ───────────────────────────────────────────────────────────── */
.btn {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  padding: .65rem 1.4rem;
  border-radius: 8px;
  font-family: 'DM Sans', sans-serif;
  font-size: .9rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all var(--trans);
}
 
.btn-primary {
  background: var(--accent);
  color: #0c0e13;
}
.btn-primary:hover { background: var(--accent2); transform: translateY(-1px); box-shadow: 0 6px 18px rgba(232,196,104,.25); }
 
.btn-ghost {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-muted);
}
.btn-ghost:hover { border-color: var(--text-muted); color: var(--text); }
 
.btn-danger {
  background: rgba(224,85,85,.12);
  border: 1px solid rgba(224,85,85,.3);
  color: var(--danger);
}
.btn-danger:hover { background: rgba(224,85,85,.2); }
 
.form-actions { margin-top: 1.5rem; display: flex; gap: .75rem; align-items: center; }
 
/* ─── Alerts ────────────────────────────────────────────────────────────── */
.alert {
  padding: .85rem 1.1rem;
  border-radius: 8px;
  font-size: .9rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: flex-start;
  gap: .6rem;
}
 
.alert svg { flex-shrink: 0; margin-top: 2px; }
 
.alert-success { background: rgba(76,175,125,.1); border: 1px solid rgba(76,175,125,.25); color: var(--success); }
.alert-error   { background: rgba(224,85,85,.1);  border: 1px solid rgba(224,85,85,.25);  color: var(--danger);  }
 
/* ─── Info display ──────────────────────────────────────────────────────── */
.info-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
 
.info-item {
  background: var(--bg3);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: .9rem 1.1rem;
}
 
.info-item .lbl { font-size: .72rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: .2rem; }
.info-item .val { font-size: .95rem; font-weight: 500; }
 
/* ─── Danger zone ───────────────────────────────────────────────────────── */
.danger-zone {
  border: 1px solid rgba(224,85,85,.2);
  border-radius: var(--radius);
  padding: 1.5rem 2rem;
  background: rgba(224,85,85,.04);
}
 
.danger-zone h3 {
  font-family: 'Syne', sans-serif;
  font-size: .95rem;
  color: var(--danger);
  margin-bottom: .4rem;
}
 
.danger-zone p { font-size: .88rem; color: var(--text-muted); margin-bottom: 1rem; }
 
/* ─── Toggle / switch ───────────────────────────────────────────────────── */
.toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: .9rem 0;
  border-bottom: 1px solid var(--border);
}
 
.toggle-row:last-child { border-bottom: none; }
 
.toggle-info .toggle-title { font-size: .93rem; font-weight: 500; }
.toggle-info .toggle-desc  { font-size: .82rem; color: var(--text-muted); margin-top: .1rem; }
 
.switch {
  position: relative;
  width: 44px; height: 24px;
  flex-shrink: 0;
}
 
.switch input { opacity: 0; width: 0; height: 0; }
 
.switch-track {
  position: absolute;
  inset: 0;
  background: var(--bg3);
  border: 1px solid var(--border);
  border-radius: 12px;
  cursor: pointer;
  transition: background var(--trans);
}
 
.switch-track::after {
  content: '';
  position: absolute;
  top: 3px; left: 3px;
  width: 16px; height: 16px;
  border-radius: 50%;
  background: var(--text-muted);
  transition: transform var(--trans), background var(--trans);
}
 
.switch input:checked + .switch-track { background: rgba(232,196,104,.2); border-color: var(--accent); }
.switch input:checked + .switch-track::after { transform: translateX(20px); background: var(--accent); }
 
/* ─── Password strength ─────────────────────────────────────────────────── */
.strength-bar {
  height: 4px;
  border-radius: 2px;
  background: var(--bg3);
  margin-top: .4rem;
  overflow: hidden;
}
 
.strength-fill {
  height: 100%;
  border-radius: 2px;
  width: 0;
  transition: width .35s ease, background .35s ease;
}
 
/* ─── Responsive ────────────────────────────────────────────────────────── */
@media (max-width: 900px) {
  .page { grid-template-columns: 1fr; }
  .sidebar { display: none; }
  .main { padding: 1.5rem; max-width: 100%; }
  .form-grid { grid-template-columns: 1fr; }
  .form-grid .full { grid-column: 1; }
  .info-grid { grid-template-columns: 1fr 1fr; }
}
 
@media (max-width: 500px) {
  .info-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div class="page">
 
  <!-- ───────────────────────── Sidebar ────────────────────────────────── -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-title">JBRD</div>
      <div class="brand-sub">Panel de usuario</div>
    </div>
 
    <span class="nav-label">Menú</span>
    <a href="index.php" class="nav-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Inicio
    </a>
    <a href="perfil.php" class="nav-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Mi perfil
    </a>
    <a href="configuracion.php" class="nav-link active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 0 1 22 12a10 10 0 0 1-2.93 7.07M4.93 4.93A10 10 0 0 0 2 12a10 10 0 0 0 2.93 7.07"/></svg>
      Configuración
      <span class="dot"></span>
    </a>
 
    <span class="nav-label" style="margin-top:auto"></span>
    <a href="logout.php" class="nav-link" style="color:var(--danger)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Cerrar sesión
    </a>
 
    <div class="sidebar-footer">
      <div class="avatar-row">
        <div class="avatar"><?= strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['apellido'], 0, 1)) ?></div>
        <div class="avatar-info">
          <div class="avatar-name"><?= h($usuario['nombre'] . ' ' . $usuario['apellido']) ?></div>
          <div class="avatar-role"><?= h($usuario['correo']) ?></div>
        </div>
      </div>
    </div>
  </aside>
 
  <!-- ────────────────────────── Main ──────────────────────────────────── -->
  <main class="main">
    <div class="page-header">
      <h1>Confi<span>guración</span></h1>
      <p>Gestiona tu información personal y preferencias de la cuenta.</p>
    </div>
 
    <!-- Alertas globales -->
    <?php foreach ($mensajes as $m): ?>
    <div class="alert alert-success">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      <?= h($m) ?>
    </div>
    <?php endforeach; ?>
 
    <?php foreach ($errores as $e): ?>
    <div class="alert alert-error">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= h($e) ?>
    </div>
    <?php endforeach; ?>
 
    <!-- Tabs -->
    <div class="tabs">
      <button class="tab-btn active" data-tab="personal">Datos personales</button>
      <button class="tab-btn" data-tab="cuenta">Cuenta</button>
      <button class="tab-btn" data-tab="seguridad">Seguridad</button>
      <button class="tab-btn" data-tab="preferencias">Preferencias</button>
    </div>
 
    <!-- ── TAB: Datos personales ──────────────────────────────────────── -->
    <div class="tab-panel active" id="tab-personal">
 
      <!-- Resumen rápido -->
      <div class="info-grid">
        <div class="info-item">
          <div class="lbl">ID de usuario</div>
          <div class="val">#<?= h($usuario['id_usuario']) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Nombre completo</div>
          <div class="val"><?= h($usuario['nombre'] . ' ' . $usuario['apellido']) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Documento</div>
          <div class="val"><?= h($usuario['tipo_documento'] . ' · ' . $usuario['numero_documento']) ?></div>
        </div>
      </div>
 
      <div class="card">
        <div class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Información personal
        </div>
 
        <form method="POST">
          <input type="hidden" name="accion" value="datos_personales">
          <div class="form-grid">
 
            <div class="field">
              <label>Nombre</label>
              <input type="text" name="nombre" value="<?= h($usuario['nombre']) ?>" required maxlength="50">
            </div>
 
            <div class="field">
              <label>Apellido</label>
              <input type="text" name="apellido" value="<?= h($usuario['apellido']) ?>" required maxlength="50">
            </div>
 
            <div class="field full">
              <label>Teléfono</label>
              <input type="tel" name="telefono" value="<?= h($usuario['telefono']) ?>" maxlength="20" placeholder="Ej: +57 300 000 0000">
            </div>
 
            <div class="field">
              <label>Tipo de documento</label>
              <select name="tipo_documento">
                <?php
                $tipos = ['CC' => 'Cédula de Ciudadanía', 'CE' => 'Cédula de Extranjería', 'PA' => 'Pasaporte', 'NIT' => 'NIT', 'TI' => 'Tarjeta de Identidad'];
                foreach ($tipos as $val => $lbl):
                    $sel = ($usuario['tipo_documento'] === $val) ? 'selected' : '';
                ?>
                <option value="<?= h($val) ?>" <?= $sel ?>><?= h($lbl) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
 
            <div class="field">
              <label>Número de documento</label>
              <input type="text" name="numero_documento" value="<?= h($usuario['numero_documento']) ?>" maxlength="30">
            </div>
 
            <div class="field">
              <label>Fecha de nacimiento</label>
              <input type="date" name="fecha_nacimiento" value="<?= h($usuario['fecha_nacimiento']) ?>">
            </div>
 
            <div class="field">
              <label>Fecha de expedición doc.</label>
              <input type="date" name="fecha_expedicion" value="<?= h($usuario['fecha_expedicion']) ?>">
            </div>
 
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Guardar cambios
            </button>
          </div>
        </form>
      </div>
    </div>
 
    <!-- ── TAB: Cuenta ────────────────────────────────────────────────── -->
    <div class="tab-panel" id="tab-cuenta">
      <div class="card">
        <div class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          Cambiar correo electrónico
        </div>
 
        <div class="field" style="margin-bottom:1rem">
          <label>Correo actual</label>
          <input type="email" value="<?= h($usuario['correo']) ?>" readonly>
        </div>
 
        <form method="POST">
          <input type="hidden" name="accion" value="cambiar_correo">
          <div class="form-grid">
            <div class="field">
              <label>Nuevo correo</label>
              <input type="email" name="nuevo_correo" placeholder="nuevo@correo.com" required>
            </div>
            <div class="field">
              <label>Confirmar correo</label>
              <input type="email" name="confirmar_correo" placeholder="nuevo@correo.com" required>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Actualizar correo</button>
          </div>
        </form>
      </div>
 
      <!-- Zona de peligro -->
      <div class="danger-zone">
        <h3>⚠ Zona de peligro</h3>
        <p>Estas acciones son irreversibles. Procede con cuidado.</p>
        <button type="button" class="btn btn-danger" onclick="confirmarEliminar()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          Eliminar mi cuenta
        </button>
      </div>
    </div>
 
    <!-- ── TAB: Seguridad ─────────────────────────────────────────────── -->
    <div class="tab-panel" id="tab-seguridad">
      <div class="card">
        <div class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Cambiar contraseña
        </div>
 
        <form method="POST">
          <input type="hidden" name="accion" value="cambiar_password">
          <div class="form-grid">
 
            <div class="field full">
              <label>Contraseña actual</label>
              <input type="password" name="password_actual" required autocomplete="current-password">
            </div>
 
            <div class="field">
              <label>Nueva contraseña</label>
              <input type="password" name="password_nueva" id="pwd-nueva" required autocomplete="new-password" minlength="8">
              <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
            </div>
 
            <div class="field">
              <label>Confirmar nueva contraseña</label>
              <input type="password" name="password_confirmar" required autocomplete="new-password">
            </div>
 
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
          </div>
        </form>
      </div>
 
      <div class="card">
        <div class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Sesiones activas
        </div>
        <div class="toggle-row">
          <div class="toggle-info">
            <div class="toggle-title">Sesión actual</div>
            <div class="toggle-desc">Dispositivo actual · Bogotá, Colombia</div>
          </div>
          <span style="font-size:.78rem;color:var(--success);background:rgba(76,175,125,.1);padding:.2rem .6rem;border-radius:20px;">Activa</span>
        </div>
        <div style="margin-top:1rem">
          <a href="logout.php?all=1" class="btn btn-ghost" style="font-size:.85rem">Cerrar todas las sesiones</a>
        </div>
      </div>
    </div>
 
    <!-- ── TAB: Preferencias ──────────────────────────────────────────── -->
    <div class="tab-panel" id="tab-preferencias">
      <div class="card">
        <div class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
          Notificaciones
        </div>
 
        <form method="POST">
          <input type="hidden" name="accion" value="notificaciones">
 
          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Notificaciones por correo</div>
              <div class="toggle-desc">Recibe actualizaciones importantes en tu bandeja de entrada.</div>
            </div>
            <label class="switch">
              <input type="checkbox" name="notif_correo" checked>
              <span class="switch-track"></span>
            </label>
          </div>
 
          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Alertas de actividad</div>
              <div class="toggle-desc">Accesos, cambios de contraseña y modificaciones en la cuenta.</div>
            </div>
            <label class="switch">
              <input type="checkbox" name="notif_actividad" checked>
              <span class="switch-track"></span>
            </label>
          </div>
 
          <div class="toggle-row">
            <div class="toggle-info">
              <div class="toggle-title">Boletín de noticias</div>
              <div class="toggle-desc">Novedades y actualizaciones de la plataforma JBRD.</div>
            </div>
            <label class="switch">
              <input type="checkbox" name="notif_boletin">
              <span class="switch-track"></span>
            </label>
          </div>
 
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar preferencias</button>
          </div>
        </form>
      </div>
 
      <div class="card">
        <div class="card-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
          Apariencia
        </div>
 
        <div class="toggle-row">
          <div class="toggle-info">
            <div class="toggle-title">Modo oscuro</div>
            <div class="toggle-desc">Actualmente usando el tema oscuro.</div>
          </div>
          <label class="switch">
            <input type="checkbox" checked>
            <span class="switch-track"></span>
          </label>
        </div>
 
        <div class="toggle-row">
          <div class="toggle-info">
            <div class="toggle-title">Animaciones reducidas</div>
            <div class="toggle-desc">Útil si prefieres una interfaz más estática.</div>
          </div>
          <label class="switch">
            <input type="checkbox">
            <span class="switch-track"></span>
          </label>
        </div>
      </div>
    </div>
 
  </main>
</div>
 
<!-- ── Modal confirmar eliminación ─────────────────────────────────────── -->
<div id="modal-eliminar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:999;align-items:center;justify-content:center;">
  <div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:2rem;max-width:380px;width:90%;text-align:center;">
    <div style="font-size:2.5rem;margin-bottom:.75rem;">⚠️</div>
    <h3 style="font-family:'Syne',sans-serif;font-size:1.1rem;margin-bottom:.5rem;">¿Eliminar tu cuenta?</h3>
    <p style="font-size:.88rem;color:var(--text-muted);margin-bottom:1.5rem;">Esta acción es permanente e irreversible. Todos tus datos serán eliminados.</p>
    <div style="display:flex;gap:.75rem;justify-content:center;">
      <button onclick="cerrarModal()" class="btn btn-ghost">Cancelar</button>
      <a href="eliminar_cuenta.php?confirm=1" class="btn btn-danger">Sí, eliminar</a>
    </div>
  </div>
</div>
 
<script>
/* ── Tabs ─────────────────────────────────────────────────────────────── */
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
  });
});
 
/* ── Fuerza de contraseña ─────────────────────────────────────────────── */
const pwdInput = document.getElementById('pwd-nueva');
const fill     = document.getElementById('strength-fill');
 
if (pwdInput) {
  pwdInput.addEventListener('input', () => {
    const v = pwdInput.value;
    let score = 0;
    if (v.length >= 8)  score++;
    if (v.length >= 12) score++;
    if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
 
    const colors = ['#e05555','#e07a55','#e0b855','#7ab855','#4caf7d'];
    const widths  = ['20%','40%','60%','80%','100%'];
    fill.style.width      = v.length ? widths[score - 1] || '5%' : '0';
    fill.style.background = v.length ? colors[score - 1] || '#e05555' : 'transparent';
  });
}
 
/* ── Modal ────────────────────────────────────────────────────────────── */
function confirmarEliminar() {
  const m = document.getElementById('modal-eliminar');
  m.style.display = 'flex';
}
function cerrarModal() {
  document.getElementById('modal-eliminar').style.display = 'none';
}
 
/* ── Auto-ocultar alertas ─────────────────────────────────────────────── */
document.querySelectorAll('.alert').forEach(a => {
  setTimeout(() => {
    a.style.transition = 'opacity .5s';
    a.style.opacity = '0';
    setTimeout(() => a.remove(), 500);
  }, 4500);
});
</script>
</body>
</html>