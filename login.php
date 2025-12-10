<?php
session_start();
include "conexion.php";

// Variable donde guardaremos mensajes de errores o alertas, la uso mas abajo para informar al usuario si algo falla.
$mensaje_html = "";

// Cuando le da a login
if (isset($_GET['login'])) {

    // Recogemos los valores enviados por GET que son correo y contraseña
    // vi que añadiendo : ''; evitas de que el campo vaya vacio y lo añadi
    $correo = isset($_GET['correo']) ? $_GET['correo'] : '';
    $pass   = isset($_GET['contraseña']) ? $_GET['contraseña'] : '';

    // Para evitar consultas con valores vacios he añadido esta validacion por si falta algo muestra un aviso
    if ($correo === '' || $pass === '') {
        $mensaje_html = '<div class="alert alert-warning mt-3">Rellena correo y contraseña.</div>';
    } else {

        // Comprobar si existe un admin con ese correo en tabla de admin
        $stmt = $conexion->prepare("SELECT id_admin, nombre, contraseña FROM admin WHERE correo_electronico = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $res = $stmt->get_result();
        $admin = $res->fetch_assoc();
        $stmt->close();

        // Si encontramos un admin con ese correo
        if (!empty($admin)) {

            // Comprobamos la contraseña con password_verify que compara la contraseña en texto que introducimos y
            // con el hash guardado en la base de datos.
            if (password_verify($pass, $admin['contraseña'])) {
                // Si es correcta, guardamos en la sesión los datos:
                // id (para consultas), nombre (para mostrar)
                $_SESSION['user_id']   = $admin['id_admin'];
                $_SESSION['user_name'] = $admin['nombre'];
                $_SESSION['role']      = 'admin';
                header("Location: admin_panel.php");
                exit;
            } else {
                // Si la contraseña es incorrecta para admin muestra el mensaje de error.
                $mensaje_html = '<div class="alert alert-danger mt-3">Contraseña o correo incorrecto.</div>';
            }

        } else {

            // Si no es admin lo que introducimos entonces buscamos en la tabla clientes.
            $stmt = $conexion->prepare("SELECT id_cliente, nombre, contraseña FROM clientes WHERE correo_electronico = ?");
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $res = $stmt->get_result();
            $cliente = $res->fetch_assoc();
            $stmt->close();

            // Si existe un cliente que coincida con el correo introducido
            if (!empty($cliente)) {

                // Comprobamos la contraseña del cliente con password_verify.
                if (password_verify($pass, $cliente['contraseña'])) {
                    // Guardamos en sesión los datos del cliente
                    $_SESSION['user_id']   = $cliente['id_cliente'];
                    $_SESSION['user_name'] = $cliente['nombre'];
                    $_SESSION['role']      = 'cliente';
                    header("Location: cliente_panel.php");
                    exit;
                } else {
                    // Si la contraseña del cliente es incorrecta
                    $mensaje_html = '<div class="alert alert-danger mt-3">Contraseña o correo incorrecto.</div>';
                }
            } else {
                // No existe ningún usuario con ese correo
                $mensaje_html = '<div class="alert alert-warning mt-3">No existe el usuario introducido.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Alex Componentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="media/logopag.png">
    <style>
        body { background-color: #ffffff !important; }
        .navbar-white { background-color: #ffffff !important; border-bottom: 2px solid #ddd; }
        .navbar-white .nav-link { color: #000 !important; font-weight:600; }
        .navbar-white .nav-link:hover { color: #ff6600 !important; }
        .btn-orange { background-color: #ff6600 !important; color:white !important; border:none; }
        .btn-orange:hover { background-color:#e55c00 !important; }
        .card { border-radius: 8px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-white">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <img src="media/logo.jpg" alt="Logo" height="45">
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
                <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container d-flex justify-content-center align-items-center" style="height: 80vh;">
    <div class="card p-4 shadow" style="width: 380px;">
        <h3 class="text-center mb-4">Iniciar sesión</h3>

        <form action="login.php" method="get" novalidate>
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo" class="form-control" placeholder="usuario@mail.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="contraseña" class="form-control" required>
            </div>

            <input class="btn btn-orange w-100" type="submit" name="login" value="Entrar">
        </form>

        <p class="text-center mt-3">
            ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
        </p>

        <?php if ($mensaje_html !== "") echo $mensaje_html; ?>
    </div>
</div>

</body>
</html>
