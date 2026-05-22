<?php
session_name('jbrd');
session_start();

// Si moviste conexion.php a la carpeta php/, esta ruta es la correcta:
require_once '../php/conexion.php';

if (!isset($_SESSION['id_cuenta'])) {
    header("Location: ../login.php");
    exit();
}

$id_cuenta = $_SESSION['id_cuenta'];

// 1. Datos del estudiante (Usando los nombres exactos de tus tablas 'cuentas' y 'roles')
$stmtUser = $pdo->prepare("
    SELECT c.usuario, c.experiencia, c.creditos, r.nombre_rol 
    FROM cuentas c 
    JOIN roles r ON c.id_rol = r.id_rol 
    WHERE c.id_cuenta = :id_cuenta
");
$stmtUser->execute(['id_cuenta' => $id_cuenta]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

// 2. Traer todas las lecciones ordenadas por su ruta
$stmtLec = $pdo->query("SELECT * FROM lecciones ORDER BY orden ASC");
$lecciones = $stmtLec->fetchAll(PDO::FETCH_ASSOC);

// 3. Buscar qué lecciones ha completado ya este usuario específico
//  CÓDIGO CORREGIDO (Respetando tu base de datos):
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
    <link rel="stylesheet" href="../CSS/main.css">
    <link rel="stylesheet" href="../CSS/ojo.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #0c1220; color: white; font-family: Arial, sans-serif; margin: 0; }
        .topbar { height: 70px; background: #111a2e; display: flex; justify-content: space-between; align-items: center; padding: 0 40px; border-bottom: 1px solid #223; }
        .logo { font-size: 24px; font-weight: bold; color: white; display: flex; align-items: center; gap: 10px; }
        .logo img { height: 35px; }
        .user-stats { display: flex; gap: 20px; font-size: 14px; background: #16233e; padding: 8px 16px; border-radius: 20px; border: 1px solid #00bfff; }
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

        /* Estilos del Visor de Contenidos Interactivos */
        .visor-contenidos { background: #111a2e; border: 1px solid #00bfff; border-radius: 15px; padding: 30px; margin-top: 30px; display: none; }
        .tabs-contenidos { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid #223; padding-bottom: 10px; overflow-x: auto; }
        .tab-btn { background: #16233e; border: 1px solid #223; color: #8892b0; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .tab-btn.active { background: #00bfff; color: #0c1220; border-color: #00bfff; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .tab-panel h4 { font-size: 24px; margin-top: 0; color: #00bfff; margin-bottom: 15px; }
        .tab-panel p { line-height: 1.6; color: #e2e8f0; font-size: 16px; }
        
        .visor-footer { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 20px; border-top: 1px solid #223; align-items: center; }
        .btn-nav { background: #16233e; border: 1px solid #223; color: white; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-nav:hover:not(:disabled) { border-color: #00bfff; color: #00bfff; }
        .btn-nav:disabled { opacity: 0.3; cursor: not-allowed; }
        .btn-completar { background: #28a745; color: white; border: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; display: none; transition: 0.3s; }
        .btn-completar:hover { background: #218838; box-shadow: 0 0 15px rgba(40, 167, 69, 0.5); }
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
            <span style="color:#00bfff;">🧠 <span id="ui-xp"><?= $usuario['experiencia'] ?></span> XP</span>
            <span style="color:#ffbc00;">🪙 <span id="ui-creditos"><?= $usuario['creditos'] ?></span> JBRDCN</span>
        </div>
    </header>

    <main class="contenido">
        
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
                        $estado = 'completado';
                        $clase_card = 'disponible';
                    } elseif ($proxima_disponible) {
                        $estado = 'disponible';
                        $clase_card = 'disponible';
                        $proxima_disponible = false; 
                    } else {
                        $estado = 'bloqueado';
                        $clase_card = 'bloqueada';
                    }
                ?>
                    <div class="card-leccion <?= $clase_card ?>" id="leccion-card-<?= $lec['id_leccion'] ?>">
                        <div class="info-leccion">
                            <h3>Módulo <?= $lec['orden'] ?>: <?= htmlspecialchars($lec['nombre_leccion']) ?></h3>
                            <p><?= htmlspecialchars($lec['descripcion']) ?></p>
                            <div class="rewards">
                                <span class="reward-xp">🧠 +<?= $lec['recompensa_experiencia'] ?> XP</span>
                                <span class="reward-coin">🪙 +<?= $lec['recompensa_creditos'] ?> JBRDCN</span>
                            </div>
                        </div>

                        <div class="accion-leccion">
                            <?php if ($estado === 'completado'): ?>
                                <span class="status-badge badge-done">✅ Completado</span>
                            <?php elseif ($estado === 'disponible'): ?>
                                <button class="btn-entrar" onclick="abrirLeccion(<?= $lec['id_leccion'] ?>, '<?= htmlspecialchars($lec['nombre_leccion']) ?>')">🔓 Acceder</button>
                            <?php else: ?>
                                <span class="status-badge badge-lock">🔒 Bloqueado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="seccion-visor" class="visor-contenidos">
            <button class="btn-nav" onclick="regresarAlCatalogo()" style="margin-bottom: 20px;">⬅ Volver al Catálogo</button>
            <h2 id="visor-titulo-leccion" style="margin: 0 0 20px 0; color: white;"></h2>
            
            <div class="tabs-contenidos" id="visor-tabs"></div>

            <div id="visor-paneles"></div>

            <div class="visor-footer">
                <button id="btn-ant" class="btn-nav" onclick="cambiarContenido(actualIndex - 1)">Anterior</button>
                <button id="btn-sig" class="btn-nav" onclick="cambiarContenido(actualIndex + 1)">Siguiente</button>
                <button id="btn-finalizar" class="btn-completar" onclick="enviarProgreso()">Obtener Recompensas 🪙</button>
            </div>
        </div>

    </main>

    <script>
        let contenidosActuales = [];
        let actualIndex = 0;
        let leccionIdActual = null;

        function abrirLeccion(idLeccion, tituloLeccion) {
            fetch(`get_contenidos.php?id_leccion=${idLeccion}`)
                .then(response => response.json())
                .then(data => {
                    if(data.length === 0) {
                        Swal.fire('Aviso', 'Esta lección aún no tiene contenidos registrados en la base de datos.', 'info');
                        return;
                    }
                    
                    contenidosActuales = data;
                    leccionIdActual = idLeccion;
                    actualIndex = 0;

                    document.getElementById('visor-titulo-leccion').innerText = tituloLeccion;
                    
                    const tabsContainer = document.getElementById('visor-tabs');
                    const panelesContainer = document.getElementById('visor-paneles');
                    tabsContainer.innerHTML = '';
                    panelesContainer.innerHTML = '';

                    contenidosActuales.forEach((cont, index) => {
                        const btn = document.createElement('button');
                        btn.className = `tab-btn ${index === 0 ? 'active' : ''}`;
                        btn.innerText = `Parte ${cont.orden}`;
                        btn.onclick = () => cambiarContenido(index);
                        btn.id = `tab-btn-${index}`;
                        tabsContainer.appendChild(btn);

                        const panel = document.createElement('div');
                        panel.className = `tab-panel ${index === 0 ? 'active' : ''}`;
                        panel.id = `tab-panel-${index}`;
                        panel.innerHTML = `
                            <h4>${cont.nombre_contenido}</h4>
                            <p>${cont.descripcion}</p>
                            <div style="background: #16233e; padding: 20px; border-radius: 8px; margin-top: 25px; border-left: 4px solid #00bfff;">
                                💡 <strong>Consejo de JBRD Coin:</strong> Analiza a fondo esta información. Te servirá para mitigar riesgos en tus próximas simulaciones.
                            </div>
                        `;
                        panelesContainer.appendChild(panel);
                    });

                    document.getElementById('seccion-catalogo').style.display = 'none';
                    document.getElementById('seccion-visor').style.display = 'block';
                    actualizarBotonesNavegacion();
                })
                .catch(err => {
                    console.error("Error al obtener contenidos:", err);
                    Swal.fire('Error', 'No se pudo conectar con el servidor de contenidos.', 'error');
                });
        }

        function cambiarContenido(nuevoIndex) {
            if(nuevoIndex < 0 || nuevoIndex >= contenidosActuales.length) return;
            
            document.getElementById(`tab-btn-${actualIndex}`).classList.remove('active');
            document.getElementById(`tab-panel-${actualIndex}`).classList.remove('active');

            actualIndex = nuevoIndex;
            document.getElementById(`tab-btn-${actualIndex}`).classList.add('active');
            document.getElementById(`tab-panel-${actualIndex}`).classList.add('active');

            actualizarBotonesNavegacion();
        }

        function actualizarBotonesNavegacion() {
            document.getElementById('btn-ant').disabled = (actualIndex === 0);
            
            if(actualIndex === contenidosActuales.length - 1) {
                document.getElementById('btn-sig').style.display = 'none';
                document.getElementById('btn-finalizar').style.display = 'block';
            } else {
                document.getElementById('btn-sig').style.display = 'block';
                document.getElementById('btn-finalizar').style.display = 'none';
            }
        }

        function regresarAlCatalogo() {
            document.getElementById('seccion-visor').style.display = 'none';
            document.getElementById('seccion-catalogo').style.display = 'block';
        }

        function enviarProgreso() {
            const formData = new FormData();
            formData.append('id_leccion', leccionIdActual);

            fetch('../php/lecciones_procesar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({
                        title: '¡Módulo Completado!',
                        html: `Felicidades, has adquirido nuevos conocimientos financieros.<br><br>🎁 <strong>Recompensas sumadas:</strong><br>🧠 +${data.xp_ganada} XP<br>🪙 +${data.creditos_ganados} JBRDCN`,
                        icon: 'success',
                        confirmButtonColor: '#00bfff',
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        document.getElementById('ui-xp').innerText = data.total_xp;
                        document.getElementById('ui-rol').innerText = data.nuevo_rol;
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Información', data.message, 'info').then(() => { regresarAlCatalogo(); });
                }
            })
            .catch(error => {
                console.error('Error en el procesamiento:', error);
                Swal.fire('Error', 'Ocurrió un problema al guardar tus recompensas.', 'error');
            });
        }
    </script>
</body>
</html>