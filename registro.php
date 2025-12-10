<?php include "conexion.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Alex Componentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="media/logopag.png">
    <style>
        body { background-color: #ffffff !important; }
        .navbar-white { background-color: #ffffff !important; border-bottom: 2px solid #ddd; }
        .navbar-white .nav-link { color: #000 !important; font-weight:600; }
        .navbar-white .nav-link:hover { color: #ff6600 !important; }
        .btn-orange { background-color: #ff6600 !important; color: white !important; border: none; }
        .btn-orange:hover { background-color: #e55c00 !important; }
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

<header class="text-white text-center py-5" style="background: linear-gradient(to bottom right, #ff6600, #ff8844);">
    <h1 class="display-4 fw-bold">Registro</h1>
    <p class="lead">Crea tu cuenta para comprar</p>
</header>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h3 class="mb-4">Registro de Cliente</h3>

                <?php
                if (isset($_POST['registrar'])) {

                    // Recoger datos
                    $correo = $_POST['correo'];
                    $nombre = $_POST['nombre'];
                    $apellidos = $_POST['apellidos'];
                    $contrasena = $_POST['contrasena'];

                    // Campos adicionales obligatorios en la tabla que los añado para que en la consulta a la hora de crear el cliente
                    // como no es necesario pues los dejo asi
                    $telefono = "";
                    $direccion = "";
                    $cod_postal = "";

                    // Comprobar que el cliente existe
                    $stmt = $conexion->prepare("SELECT id_cliente FROM clientes WHERE correo_electronico = ?");
                    $stmt->bind_param("s", $correo);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $existe = $resultado->fetch_assoc();
                    $stmt->close();

                    if (!empty($existe)) {
                        // Si el cliente existe le manda el mensaje
                        echo '<div class="alert alert-warning">Ya eres cliente. Inicia sesión.</div>';
                    } else {
                        // Si no existe hace lo siguiente
                        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

                        $stmt = $conexion->prepare("INSERT INTO clientes (correo_electronico, nombre, apellidos, telefono, contraseña, direccion, cod_postal)
                                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssss", 
                            $correo, $nombre, $apellidos, $telefono, $hash, $direccion, $cod_postal
                        );

                        if ($stmt->execute()) {
                            echo '<div class="alert alert-success">Registro completado correctamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al registrar.</div>';
                        }

                        $stmt->close();
                    }
                }
                ?>

                <form method="post" action="registro.php">

                 <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="contrasena" class="form-control" required>
                </div>

    <input class="btn btn-orange w-100" type="submit" name="registrar" value="Registrar">
</form>
                <p class="mt-3 text-center">
                    ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
                </p>

            </div>
        </div>
    </div>
</div>

<footer class="text-center py-3 text-white mt-4" style="background-color:#ff6600">
    © 2025 Alex Componentes - Todos los derechos reservados
</footer>

</body>
</html>
