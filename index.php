<?php
    session_start();
    include "conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alex Componentes - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="media/logopag.png">

    <style>
        body { background-color: #ffffff !important; }
        .navbar-white { background-color: #ffffff !important; border-bottom: 2px solid #ddd; }
        .navbar-white .nav-link { color: #000 !important; font-weight:600; }
        .navbar-white .nav-link:hover { color: #ff6600 !important; }
        .btn-orange { background-color: #ff6600 !important; color: white !important; border: none; }
        .btn-orange:hover { background-color: #e55c00 !important; }
        .card { border-radius: 8px; }

        .card-img-wrap {
            height: 220px;
            overflow: hidden;
            background: #f5f5f5;
        }
        .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
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

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'cliente'): ?>
                    <li class="nav-item"><a class="nav-link" href="cliente_panel.php">Página principal</a></li>
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>
                    <li class="nav-item nav-link">Bienvenido, <strong><?php echo $_SESSION['user_name']; ?></strong></li>
                    <li class="nav-item"><a class="nav-link" href="cerrarsesion.php">Cerrar sesión</a></li>

                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin_panel.php">Panel Admin</a></li>
                    <li class="nav-item"><a class="nav-link" href="cerrarsesion.php">Cerrar sesión</a></li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<header class="text-white text-center py-5" style="background: linear-gradient(to bottom right, #ff6600, #ff8844);">
    <h1 class="display-4 fw-bold">Bienvenido a Alex Componentes</h1>
    <p class="lead">Tu tienda de hardware de confianza</p>
    <a href="productos.php" class="btn btn-orange btn-lg mt-3">Ver productos</a>
</header>

<div class="container my-5">
    <h2 class="mb-4">Productos Destacados</h2>

    <div class="row">

        <?php
            // Cogemos 3 productos de la base de datos para mostrarlos
            $destacados = $conexion->query("SELECT * FROM productos LIMIT 3");
            while ($p = $destacados->fetch_assoc()) {
            $rutaImagen = $p['ruta_imagen'];
        ?>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">

                <div class="card-img-wrap">
                    <img src="<?php echo $rutaImagen; ?>" alt="producto">
                </div>

                <div class="card-body">
                    <h5 class="card-title"><?php echo $p['nombre']; ?></h5>

                    <p class="text-muted"><?php echo $p['descripcion_producto']; ?></p>

                </div>
            </div>
        </div>

        <?php } ?>

    </div>
</div>

<footer class="text-center py-3 text-white mt-4" style="background-color:#ff6600">
    © 2025 Alex Componentes - Todos los derechos reservados
</footer>

</body>
</html>
