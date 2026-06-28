<?php
session_name('jbrd');
session_start();
 
require_once '../php/conexion.php';
 
if (!isset($_SESSION['id_cuenta'])) {
    header("Location: ../login.php");
    exit();
}
 
$id_cuenta = $_SESSION['id_cuenta'];
 
// Validar que se recibió un id_leccion
$id_leccion = isset($_GET['id_leccion']) ? (int)$_GET['id_leccion'] : 0;
if (!$id_leccion) {
    header("Location: lecciones.php");
    exit();
}
 
// Datos del estudiante
$stmtUser = $pdo->prepare("
    SELECT c.usuario, c.experiencia, c.creditos, r.nombre_rol 
    FROM cuentas c 
    JOIN roles r ON c.id_rol = r.id_rol 
    WHERE c.id_cuenta = :id_cuenta
");
$stmtUser->execute(['id_cuenta' => $id_cuenta]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
 
// Datos de la lección
$stmtLec = $pdo->prepare("SELECT * FROM lecciones WHERE id_leccion = :id_leccion");
$stmtLec->execute(['id_leccion' => $id_leccion]);
$leccion = $stmtLec->fetch(PDO::FETCH_ASSOC);
 
if (!$leccion) {
    header("Location: lecciones.php");
    exit();
}
 
// Si el request es AJAX (fetch), devolver preguntas en JSON
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    || isset($_GET['json'])
) {
    $stmtP = $pdo->prepare("SELECT * FROM preguntas WHERE id_leccion = :id_leccion ORDER BY id_pregunta ASC");
    $stmtP->execute(['id_leccion' => $id_leccion]);
    $preguntas = $stmtP->fetchAll(PDO::FETCH_ASSOC);
 
    header('Content-Type: application/json');
    echo json_encode($preguntas);
    exit();
}
 
// Vista HTML del examen
$stmtP = $pdo->prepare("SELECT * FROM preguntas WHERE id_leccion = :id_leccion ORDER BY id_pregunta ASC");
$stmtP->execute(['id_leccion' => $id_leccion]);
$preguntas = $stmtP->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen – <?= htmlspecialchars($leccion['nombre_leccion']) ?> | JBRD Academy</title>
    <script>
        if (localStorage.getItem('tema') === 'claro') {
            document.documentElement.classList.add('tema-claro');
        }
    </script>
    <link rel="stylesheet" href="../CSS/main.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../JS/app.js" defer></script>
    <style>
        body { background: #0c1220; color: white; font-family: Arial, sans-serif; margin: 0; }
 
        /* ── TOPBAR ── */
        .topbar { height: 70px; background: #111a2e; display: flex; justify-content: space-between; align-items: center; padding: 0 40px; border-bottom: 1px solid #223; }
        .logo { font-size: 24px; font-weight: bold; color: white; display: flex; align-items: center; gap: 10px; }
        .logo img { height: 35px; }
        .user-stats { display: flex; gap: 20px; font-size: 14px; background: #16233e; padding: 8px 16px; border-radius: 20px; border: 1px solid #00bfff; }
 
        /* ── CONTENEDOR PRINCIPAL ── */
        .contenido { max-width: 800px; margin: 40px auto; padding: 0 20px; }
 
        .examen-header { background: #111a2e; border: 1px solid #00bfff; border-radius: 15px; padding: 28px 30px; margin-bottom: 30px; }
        .examen-header h1 { margin: 0 0 8px 0; font-size: 24px; color: white; }
        .examen-header p  { margin: 0; color: #8892b0; line-height: 1.6; font-size: 15px; }
 
        /* ── PREGUNTAS ── */
        .pregunta-examen { background: #16233e; border: 1px solid #223; border-radius: 10px; padding: 22px 24px; margin-bottom: 18px; transition: border-color .2s; }
        .pregunta-examen:hover { border-color: #00bfff33; }
        .texto-pregunta { color: #e2e8f0; font-size: 16px; margin: 0 0 14px 0; line-height: 1.5; }
        .opciones-respuesta { display: flex; flex-direction: column; gap: 10px; }
        .opciones-respuesta label {
            display: flex; align-items: center; gap: 10px;
            background: #0f182b; border: 1px solid #223;
            border-radius: 8px; padding: 10px 14px;
            cursor: pointer; color: #cce8ff;
            transition: border-color .2s, background .2s;
        }
        .opciones-respuesta label:hover { border-color: #00bfff; background: #16233e; }
        .opciones-respuesta input[type="radio"] { accent-color: #00bfff; width: 16px; height: 16px; flex-shrink: 0; }
 
        /* ── FOOTER ── */
        .examen-footer {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
            background: #111a2e; border: 1px solid #223;
            border-radius: 12px; padding: 20px 24px; margin-top: 10px;
        }
        .btn-nav {
            background: #16233e; border: 1px solid #223; color: white;
            padding: 10px 20px; border-radius: 6px; cursor: pointer;
            font-weight: bold; transition: .3s; text-decoration: none; display: inline-block;
        }
        .btn-nav:hover { border-color: #00bfff; color: #00bfff; }
        .btn-enviar {
            background: #28a745; color: white; border: none;
            padding: 12px 28px; border-radius: 6px;
            font-weight: bold; cursor: pointer; font-size: 15px; transition: .3s;
        }
        .btn-enviar:hover { background: #218838; box-shadow: 0 0 15px rgba(40,167,69,.5); }
        .btn-enviar:disabled { opacity: .5; cursor: not-allowed; }
 
        /* Sin preguntas */
        .sin-preguntas { text-align: center; padding: 60px 20px; color: #8892b0; font-size: 18px; }
    </style>
</head>
<body>
 
    <header class="topbar">
        <div class="logo">
            <img src="../imagenes/logogris.png" alt="JBRD">
            <span>JBRD <span style="color:#00bfff;">ACADEMY</span></span>
        </div>
        <div class="user-stats">
            <span>👤 <strong id="ui-username"><?= htmlspecialchars($usuario['usuario']) ?></strong>
                  (<span id="ui-rol"><?= htmlspecialchars($usuario['nombre_rol']) ?></span>)</span>
            <span style="color:#00bfff;">🧠 <span id="ui-xp"><?= (int)$usuario['experiencia'] ?></span> XP</span>
            <span style="color:#ffbc00;">🪙 <span id="ui-creditos"><?= (int)$usuario['creditos'] ?></span> JBRDCN</span>
        </div>
    </header>
 
    <main class="contenido">
 
        <div class="examen-header">
            <h1>📝 Examen — <?= htmlspecialchars($leccion['nombre_leccion']) ?></h1>
            <p>Responde todas las preguntas correctamente para obtener tus recompensas completas.
               Si fallas alguna, ganarás solo 50 XP por el intento y deberás volver a intentarlo.</p>
        </div>
 
        <?php if (empty($preguntas)): ?>
            <div class="sin-preguntas">
                <p>⚠️ Esta lección aún no tiene preguntas registradas.</p>
                <a href="lecciones.php" class="btn-nav" style="margin-top: 16px;">⬅ Volver al Catálogo</a>
            </div>
        <?php else: ?>
 
            <form id="form-examen">
                <?php foreach ($preguntas as $i => $p): ?>
                    <div class="pregunta-examen">
                        <p class="texto-pregunta">
                            <strong><?= $i + 1 ?>.</strong>
                            <?= htmlspecialchars($p['pregunta']) ?>
                        </p>
                        <div class="opciones-respuesta">
                            <?php foreach (['a','b','c','d'] as $letra): ?>
                                <?php if (!empty($p['opcion_' . $letra])): ?>
                                    <label>
                                        <input type="radio"
                                               name="pregunta_<?= (int)$p['id_pregunta'] ?>"
                                               value="<?= strtoupper($letra) ?>">
                                        <?= htmlspecialchars($p['opcion_' . $letra]) ?>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </form>
 
            <div class="examen-footer">
                <a href="lecciones.php" class="btn-nav">⬅ Volver al Catálogo</a>
                <button class="btn-enviar" id="btn-enviar" onclick="enviarExamen()">✅ Enviar Examen</button>
            </div>
 
        <?php endif; ?>
 
    </main>
 
    <script>
        const LECCION_ID = <?= (int)$id_leccion ?>;
        const TOTAL_PREGUNTAS = <?= count($preguntas) ?>;
 
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
 
        function enviarExamen() {
            const checks = document.querySelectorAll('#form-examen input[type=radio]:checked');
 
            if (checks.length < TOTAL_PREGUNTAS) {
                Swal.fire('Faltan respuestas', 'Debes responder todas las preguntas antes de enviar.', 'warning');
                return;
            }
 
            const respuestas = {};
            checks.forEach(inp => {
                const idPregunta = inp.name.replace('pregunta_', '');
                respuestas[idPregunta] = inp.value;
            });
 
            document.getElementById('btn-enviar').disabled = true;
 
            fetch('../php/lecciones_procesar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_leccion: LECCION_ID, respuestas })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: '¡Módulo Completado!',
                        html: `Respondiste correctamente ${data.aciertos}/${data.total}.<br><br>
                               🎁 <strong>Recompensas sumadas:</strong><br>
                               🧠 +${data.xp_ganada} XP<br>
                               🪙 +${data.creditos_ganados} JBRDCN`,
                        icon: 'success',
                        confirmButtonColor: '#00bfff',
                        confirmButtonText: 'Continuar'
                    }).then(() => window.location.href = 'lecciones.php');
 
                } else if (data.status === 'retry') {
                    Swal.fire({
                        title: 'Casi lo logras',
                        html: `Obtuviste ${data.aciertos} de ${data.total} respuestas correctas.<br>
                               Necesitas el 100% para completar el módulo.<br><br>
                               🧠 Ganaste +${data.xp_ganada} XP por el intento.`,
                        icon: 'info',
                        confirmButtonColor: '#00bfff',
                        confirmButtonText: 'Intentar de nuevo'
                    }).then(() => {
                        document.getElementById('ui-xp').textContent = data.total_xp;
                        document.getElementById('btn-enviar').disabled = false;
                        // Reinicia selecciones
                        document.querySelectorAll('#form-examen input[type=radio]').forEach(r => r.checked = false);
                    });
 
                } else {
                    Swal.fire('Información', data.message || 'Ocurrió un problema.', 'info')
                        .then(() => window.location.href = 'lecciones.php');
                }
            })
            .catch(err => {
                console.error('Error al enviar examen:', err);
                document.getElementById('btn-enviar').disabled = false;
                Swal.fire('Error', 'Ocurrió un problema al enviar el examen.', 'error');
            });
        }
    </script>
 
</body>
</html>