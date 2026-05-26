<?php
// php/login_validar.php
session_name('jbrd');
session_start();

// Centralizamos la conexión: subimos un nivel si fuera necesario, 
// pero como conexion.php vive en la misma carpeta php/, solo lo llamamos directamente.
require_once 'conexion.php'; 

if (isset($_POST['ingresar'])) {

    $usuario  = trim($_POST['usuario']);
    $password = $_POST['password'];

    try {
        // Consultamos usando el estándar PDO y respetando fielmente tu columna nombre_rol
        $stmt = $pdo->prepare("
            SELECT c.*, r.nombre_rol
            FROM cuentas c
            JOIN roles r ON c.id_rol = r.id_rol
            WHERE c.usuario = :usuario AND c.estado = 1
        ");
        $stmt->execute(['usuario' => $usuario]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el usuario existe en la base de datos
        if ($datos) {
            
            // Verificamos si la contraseña coincide con el hash almacenado
            if (password_verify($password, $datos['password'])) {

                // Guardamos todas las variables en la sesión global
                $_SESSION['id_cuenta']   = $datos['id_cuenta'];
                $_SESSION['id_usuario']  = $datos['id_usuario'];
                $_SESSION['usuario']     = $datos['usuario'];
                $_SESSION['id_rol']      = $datos['id_rol'];
                $_SESSION['nombre_rol']  = $datos['nombre_rol'];
                $_SESSION['creditos']    = $datos['creditos'];
                $_SESSION['experiencia'] = $datos['experiencia'];

                // Redirección exitosa: sube un nivel hacia la raíz donde está el index.php
                header("Location: ../index.php");
                exit();

            } else {
                // Contraseña incorrecta -> regresa a login.php en la raíz
                header("Location: ../login.php?error=1");
                exit();
            }
        } else {
            // Usuario no encontrado o inactivo -> regresa a login.php en la raíz
            header("Location: ../login.php?error=1");
            exit();
        }

    } catch (PDOException $e) {
        die("Error en la consulta de inicio de sesión: " . $e->getMessage());
    }
}
?>