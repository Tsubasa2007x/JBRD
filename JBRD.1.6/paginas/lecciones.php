<?php
session_name('jbrd');
session_start();
 
require_once '../php/conexion.php';
 
if (!isset($_SESSION['id_cuenta'])) {
    header("Location: ../login.php");
    exit();
}
 
$id_cuenta = $_SESSION['id_cuenta'];
 
// 1. Datos del estudiante
$stmtUser = $pdo->prepare("
    SELECT c.usuario, c.experiencia, c.creditos, r.nombre_rol 
    FROM cuentas c 
    JOIN roles r ON c.id_rol = r.id_rol 
    WHERE c.id_cuenta = :id_cuenta
");
$stmtUser->execute(['id_cuenta' => $id_cuenta]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
 
// 2. Traer todas las lecciones ordenadas
$stmtLec = $pdo->query("SELECT * FROM lecciones ORDER BY orden ASC");
$lecciones = $stmtLec->fetchAll(PDO::FETCH_ASSOC);
 
// 3. Lecciones completadas por este usuario
$stmtProg = $pdo->prepare("SELECT id_leccion FROM progreso_lecciones WHERE id_cuenta = :id_cuenta");
$stmtProg->execute(['id_cuenta' => $id_cuenta]);
$completadas = $stmtProg->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academia JBRD - Módulos</title>
    <script>
        if (localStorage.getItem('tema') === 'claro') {
            document.documentElement.classList.add('tema-claro');
        }
    </script>
    <link rel="stylesheet" href="../CSS/main.css">
    <link rel="stylesheet" href="../CSS/ojo.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../JS/app.js" defer></script>
    <style>
        body { background: #0c1220; color: white; font-family: Arial, sans-serif; margin: 0; }
 
        /* ── TOPBAR ── */
        .topbar { height: 70px; background: #111a2e; display: flex; justify-content: space-between; align-items: center; padding: 0 40px; border-bottom: 1px solid #223; }
        .logo { font-size: 24px; font-weight: bold; color: white; display: flex; align-items: center; gap: 10px; }
        .logo img { height: 35px; }
        .user-stats { display: flex; gap: 20px; font-size: 14px; background: #16233e; padding: 8px 16px; border-radius: 20px; border: 1px solid #00bfff; }
 
        /* ── CATÁLOGO ── */
        .contenido { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .grid-lecciones { display: flex; flex-direction: column; gap: 25px; margin-top: 30px; }
        .card-leccion { background: #16233e; border-radius: 15px; padding: 30px; border: 1px solid #223; transition: 0.3s; display: flex; justify-content: space-between; align-items: center; }
        .card-leccion.disponible:hover { border-color: #00bfff; box-shadow: 0 0 15px rgba(0, 191, 255, 0.2); }
        .card-leccion.bloqueada { opacity: 0.5; background: #0f182b; cursor: not-allowed; }
        .info-leccion h3 { font-size: 22px; margin-top: 0; margin-bottom: 10px; color: white; }
        .info-leccion p { color: #aaa; line-height: 1.5; margin-bottom: 15px; }
        .rewards { display: flex; gap: 15px; font-size: 13px; font-weight: bold; }
        .reward-xp { color: #00bfff; }
        .reward-coin { color: #ffbc00; }
        .btn-entrar { background: transparent; border: 2px solid #00bfff; color: white; padding: 12px 30px; font-weight: bold; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        .btn-entrar:hover { background: #00bfff; color: #0c1220; box-shadow: 0 0 15px #00bfff; }
        .status-badge { font-weight: bold; padding: 6px 14px; border-radius: 20px; font-size: 13px; }
        .badge-done { background: rgba(40, 167, 69, 0.2); color: #28a745; border: 1px solid #28a745; }
        .badge-lock { background: rgba(220, 53, 69, 0.2); color: #dc3545; border: 1px solid #dc3545; }
 
        /* ── VISOR ── */
        .visor-contenidos { background: #111a2e; border: 1px solid #00bfff; border-radius: 15px; padding: 30px; margin-top: 30px; display: none; }
        .tabs-contenidos { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid #223; padding-bottom: 10px; overflow-x: auto; }
        .tab-btn { background: #16233e; border: 1px solid #223; color: #8892b0; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.2s; white-space: nowrap; }
        .tab-btn.active { background: #00bfff; color: #0c1220; border-color: #00bfff; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
 
        /* ── CONTENIDO DE CADA PARTE ── */
        .parte-header { margin-bottom: 20px; }
        .parte-header h4 { font-size: 26px; margin: 0 0 6px 0; color: #00bfff; }
        .parte-header .parte-subtitulo { color: #8892b0; font-size: 14px; }
 
        /* Bloque de descripción principal */
        .parte-descripcion { line-height: 1.75; color: #e2e8f0; font-size: 16px; margin-bottom: 24px; }
 
        /* ── VIDEO ── */
        .video-wrapper { margin-bottom: 28px; }
        .video-label { font-size: 12px; font-weight: bold; letter-spacing: 1px; color: #00bfff; text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
        .video-label::before { content: ''; display: inline-block; width: 3px; height: 14px; background: #00bfff; border-radius: 2px; }
        .video-container {
            position: relative; width: 100%; padding-bottom: 56.25%;
            height: 0; border-radius: 12px; overflow: hidden;
            border: 1px solid #223; background: #0c1220;
        }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
        .video-placeholder {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 12px; background: #0c1220; color: #8892b0; font-size: 14px;
        }
        .video-placeholder .play-icon { font-size: 48px; opacity: 0.4; }
 
        /* ── CONCEPTOS CLAVE ── */
        .conceptos-clave { margin-bottom: 24px; }
        .conceptos-clave .seccion-titulo {
            font-size: 12px; font-weight: bold; letter-spacing: 1px;
            color: #ffbc00; text-transform: uppercase;
            margin-bottom: 12px; display: flex; align-items: center; gap: 6px;
        }
        .conceptos-clave .seccion-titulo::before { content: ''; display: inline-block; width: 3px; height: 14px; background: #ffbc00; border-radius: 2px; }
        .lista-conceptos { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .lista-conceptos li {
            background: #16233e; border: 1px solid #223; border-radius: 8px; padding: 12px 16px;
            display: flex; align-items: flex-start; gap: 10px;
            color: #e2e8f0; font-size: 15px; line-height: 1.5;
        }
        .lista-conceptos li .bullet { color: #00bfff; font-weight: bold; flex-shrink: 0; margin-top: 1px; }
 
        /* ── TIP ── */
        .tip-box {
            background: linear-gradient(135deg, rgba(0,191,255,0.07), rgba(0,191,255,0.03));
            border: 1px solid rgba(0,191,255,0.35); border-left: 4px solid #00bfff;
            border-radius: 10px; padding: 18px 20px; font-size: 15px;
            color: #cce8ff; line-height: 1.6;
        }
        .tip-box strong { color: #00bfff; }
 
        /* ── FOOTER NAVEGACIÓN ── */
        .visor-footer { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 20px; border-top: 1px solid #223; align-items: center; flex-wrap: wrap; gap: 10px; }
        .btn-nav { background: #16233e; border: 1px solid #223; color: white; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-nav:hover:not(:disabled) { border-color: #00bfff; color: #00bfff; }
        .btn-nav:disabled { opacity: 0.3; cursor: not-allowed; }
        .btn-completar { background: #28a745; color: white; border: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; display: none; transition: 0.3s; }
        .btn-completar:hover { background: #218838; box-shadow: 0 0 15px rgba(40, 167, 69, 0.5); }
 
        /* ── PROGRESO DE PARTES ── */
        .progreso-partes { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #8892b0; }
        .progreso-partes strong { color: #00bfff; }
    </style>
</head>
<body>
 
    <header class="topbar">
        <div class="logo">
            <img src="../imagenes/logogris.png" alt="JBRD">
            <span>JBRD <span style="color:#00bfff;">ACADEMY</span></span>
        </div>
        <div class="user-stats">
            <span>👤 <strong id="ui-username"><?= htmlspecialchars($usuario['usuario']) ?></strong> (<span id="ui-rol"><?= htmlspecialchars($usuario['nombre_rol']) ?></span>)</span>
            <span style="color:#00bfff;">🧠 <span id="ui-xp"><?= (int)$usuario['experiencia'] ?></span> XP</span>
            <span style="color:#ffbc00;">🪙 <span id="ui-creditos"><?= (int)$usuario['creditos'] ?></span> JBRDCN</span>
        </div>
    </header>
 
    <main class="contenido">
 
        <!-- ── CATÁLOGO ── -->
        <div id="seccion-catalogo">
            <button class="btn-nav" onclick="window.location.href='../index.php'" style="margin-bottom: 20px;">🏠 Volver al Inicio</button>
            <h1>Ruta de Aprendizaje</h1>
            <p style="color: #8892b0; margin-top: -5px;">Completa los módulos secuencialmente para ganar recompensas y subir de nivel.</p>
 
            <div class="grid-lecciones">
                <?php 
                $proxima_disponible = true; 
                foreach ($lecciones as $lec):
                    $is_completed = in_array($lec['id_leccion'], $completadas);
                    if ($is_completed) {
                        $estado     = 'completado';
                        $clase_card = 'disponible';
                    } elseif ($proxima_disponible) {
                        $estado     = 'disponible';
                        $clase_card = 'disponible';
                        $proxima_disponible = false;
                    } else {
                        $estado     = 'bloqueado';
                        $clase_card = 'bloqueada';
                    }
                ?>
                    <div class="card-leccion <?= $clase_card ?>" id="leccion-card-<?= (int)$lec['id_leccion'] ?>">
                        <div class="info-leccion">
                            <h3>Módulo <?= (int)$lec['orden'] ?>: <?= htmlspecialchars($lec['nombre_leccion']) ?></h3>
                            <p><?= htmlspecialchars($lec['descripcion']) ?></p>
                            <div class="rewards">
                                <span class="reward-xp">🧠 +<?= (int)$lec['recompensa_experiencia'] ?> XP</span>
                                <span class="reward-coin">🪙 +<?= (int)$lec['recompensa_creditos'] ?> JBRDCN</span>
                            </div>
                        </div>
                        <div class="accion-leccion">
                            <?php if ($estado === 'completado'): ?>
                                <span class="status-badge badge-done">✅ Completado</span>
                            <?php elseif ($estado === 'disponible'): ?>
                                <button class="btn-entrar" onclick="abrirLeccion(<?= (int)$lec['id_leccion'] ?>, <?= htmlspecialchars(json_encode($lec['nombre_leccion'])) ?>)">🔓 Acceder</button>
                            <?php else: ?>
                                <span class="status-badge badge-lock">🔒 Bloqueado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
 
        <!-- ── VISOR DE CONTENIDOS ── -->
        <div id="seccion-visor" class="visor-contenidos">
            <button class="btn-nav" onclick="regresarAlCatalogo()" style="margin-bottom: 20px;">⬅ Volver al Catálogo</button>
            <h2 id="visor-titulo-leccion" style="margin: 0 0 20px 0; color: white;"></h2>
 
            <div class="tabs-contenidos" id="visor-tabs"></div>
            <div id="visor-paneles"></div>
 
            <div class="visor-footer">
                <button id="btn-ant" class="btn-nav" onclick="cambiarContenido(actualIndex - 1)">← Anterior</button>
                <span class="progreso-partes" id="progreso-label"></span>
                <button id="btn-sig" class="btn-nav" onclick="cambiarContenido(actualIndex + 1)">Siguiente →</button>
                <!-- Al llegar a la última parte, redirige al examen en página aparte -->
                <button id="btn-finalizar" class="btn-completar" onclick="irAlExamen()">📝 Realizar Examen</button>
            </div>
        </div>
 
    </main>
 
    <script>
        let contenidosActuales = [];
        let actualIndex        = 0;
        let leccionIdActual    = null;
 
        // ── Abre la lección y construye los paneles ──────────────────────────
        function abrirLeccion(idLeccion, tituloLeccion) {
            fetch(`get_contenidos.php?id_leccion=${idLeccion}`)
                .then(r => r.json())
                .then(data => {
                    if (data.length === 0) {
                        Swal.fire('Aviso', 'Esta lección aún no tiene contenidos registrados.', 'info');
                        return;
                    }
 
                    // Deduplicar por orden: preferir el registro que tenga video_id
                    const porOrden = new Map();
                    data.forEach(cont => {
                        const existente  = porOrden.get(cont.orden);
                        const tieneVideo = !!(cont.video_id && cont.video_id.trim() !== '');
                        if (!existente) { porOrden.set(cont.orden, cont); return; }
                        const existenteTieneVideo = !!(existente.video_id && existente.video_id.trim() !== '');
                        if (tieneVideo && !existenteTieneVideo) porOrden.set(cont.orden, cont);
                    });
                    data = Array.from(porOrden.values()).sort((a, b) => a.orden - b.orden);
 
                    contenidosActuales = data;
                    leccionIdActual    = idLeccion;
                    actualIndex        = 0;
 
                    document.getElementById('visor-titulo-leccion').textContent = tituloLeccion;
 
                    const tabsContainer    = document.getElementById('visor-tabs');
                    const panelesContainer = document.getElementById('visor-paneles');
                    tabsContainer.innerHTML    = '';
                    panelesContainer.innerHTML = '';
 
                    contenidosActuales.forEach((cont, index) => {
                        const btn = document.createElement('button');
                        btn.className  = `tab-btn ${index === 0 ? 'active' : ''}`;
                        btn.textContent = `Parte ${cont.orden}`;
                        btn.id  = `tab-btn-${index}`;
                        btn.onclick = () => cambiarContenido(index);
                        tabsContainer.appendChild(btn);
 
                        const panel = document.createElement('div');
                        panel.className = `tab-panel ${index === 0 ? 'active' : ''}`;
                        panel.id        = `tab-panel-${index}`;
                        panel.innerHTML = construirPanel(cont);
                        panelesContainer.appendChild(panel);
                    });
 
                    document.getElementById('seccion-catalogo').style.display = 'none';
                    document.getElementById('seccion-visor').style.display    = 'block';
                    actualizarBotonesNavegacion();
                })
                .catch(err => {
                    console.error('Error al obtener contenidos:', err);
                    Swal.fire('Error', 'No se pudo conectar con el servidor de contenidos.', 'error');
                });
        }
 
        // ── Construye el HTML de cada panel ──────────────────────────────────
        function construirPanel(cont) {
            const videoId = cont.video_id ? cont.video_id.trim() : '';
 
            const videoHtml = videoId
                ? `<iframe
                        src="https://www.youtube-nocookie.com/embed/${videoId}?rel=0&modestbranding=1"
                        title="${escHtml(cont.nombre_contenido)}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>`
                : `<div class="video-placeholder">
                       <span class="play-icon">▶</span>
                       <span>Video no disponible para esta parte</span>
                   </div>`;
 
            let conceptosHtml = '';
            if (cont.conceptos) {
                let items = [];
                try { items = JSON.parse(cont.conceptos); }
                catch (_) { items = cont.conceptos.split('|').map(s => s.trim()).filter(Boolean); }
                if (items.length > 0) {
                    const liItems = items.map(c => `<li><span class="bullet">▸</span>${escHtml(c)}</li>`).join('');
                    conceptosHtml = `
                        <div class="conceptos-clave">
                            <div class="seccion-titulo">📌 Conceptos clave</div>
                            <ul class="lista-conceptos">${liItems}</ul>
                        </div>`;
                }
            }
 
            return `
                <div class="parte-header">
                    <h4>${escHtml(cont.nombre_contenido)}</h4>
                    <div class="parte-subtitulo">Parte ${cont.orden} · Módulo activo</div>
                </div>
                <div class="video-wrapper">
                    <div class="video-label">Video de la lección</div>
                    <div class="video-container">${videoHtml}</div>
                </div>
                <p class="parte-descripcion">${escHtml(cont.descripcion)}</p>
                ${conceptosHtml}
                <div class="tip-box">
                    💡 <strong>Consejo JBRD:</strong> Analiza a fondo esta información.
                    Te ayudará a tomar mejores decisiones en tus próximas simulaciones financieras.
                </div>`;
        }
 
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
 
        // ── Navegación entre partes ───────────────────────────────────────────
        function cambiarContenido(nuevoIndex) {
            if (nuevoIndex < 0 || nuevoIndex >= contenidosActuales.length) return;
            document.getElementById(`tab-btn-${actualIndex}`).classList.remove('active');
            document.getElementById(`tab-panel-${actualIndex}`).classList.remove('active');
            actualIndex = nuevoIndex;
            document.getElementById(`tab-btn-${actualIndex}`).classList.add('active');
            document.getElementById(`tab-panel-${actualIndex}`).classList.add('active');
            actualizarBotonesNavegacion();
            document.getElementById('seccion-visor').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
 
        function actualizarBotonesNavegacion() {
            const total = contenidosActuales.length;
            document.getElementById('btn-ant').disabled = (actualIndex === 0);
            document.getElementById('progreso-label').innerHTML =
                `Parte <strong>${actualIndex + 1}</strong> de <strong>${total}</strong>`;
            const esUltima = (actualIndex === total - 1);
            document.getElementById('btn-sig').style.display       = esUltima ? 'none'  : 'inline-block';
            document.getElementById('btn-finalizar').style.display = esUltima ? 'block' : 'none';
        }
 
        function regresarAlCatalogo() {
            document.getElementById('seccion-visor').style.display    = 'none';
            document.getElementById('seccion-catalogo').style.display = 'block';
        }
 
        // ── Redirige al examen en página separada ─────────────────────────────
        function irAlExamen() {
            window.location.href = `get_preguntas.php?id_leccion=${leccionIdActual}`;
        }
    </script>
</body>
</html>