<?php
session_start();
include "conexion.php";

// Esto lo implemente por tema de seguridad para que poniendo esto en el navegador localhost/ALEX-COMPONENTES/cliente_panel.php NO se pueda acceder y requiera logearte
// lo vi buen punto para ponerlo.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'cliente') {
    header("Location: login.php");
}

$cliente_id = $_SESSION['user_id'];
$cliente_nombre = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Cliente - Alex Componentes</title>
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

        .card-img-wrap {
            height: 220px;
            overflow: hidden;
            background: #f5f5f5;
        }
        .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-white mb-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="cliente_panel.php">
            <img src="media/logo.jpg" height="45" alt="Logo">
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item me-2">
                    <a class="nav-link" href="productos.php">Productos</a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link" href="carrito.php">Carrito</a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link" href="pedidos.php">Pedidos</a>
                </li>

                <li class="nav-item nav-link me-3">
                    Bienvenido, <strong><?php echo $_SESSION['user_name']; ?></strong>
                </li>

                <li class="nav-item me-3">
                    <a href="gestionarcuenta.php" class="btn btn-outline-secondary btn-sm">Mi cuenta</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-danger" href="cerrarsesion.php">Cerrar sesión</a>
                </li>

            </ul>
        </div>
    </div>
</nav>


<div class="container mt-3">
    <div class="alert alert-success">
        Bienvenido a AlexComponentes <strong><?php echo $cliente_nombre; ?></strong>
    </div>
</div>

<header class="text-white text-center py-5 mb-4" style="background: linear-gradient(to bottom right, #ff6600, #ff8844);">
    <h1 class="display-4 fw-bold">Bienvenido a Alex Componentes</h1>
    <p class="lead">Explora productos, gestiona tu cuenta y revisa tus pedidos.</p>
</header>

<div class="container my-4">
    <h2 class="mb-4">Productos Destacados</h2>

    <div class="row">
        <?php
        $productos = $conexion->query("SELECT * FROM productos");
        while ($p = $productos->fetch_assoc()) { ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-img-wrap">
                        <img src="<?php echo $p['ruta_imagen']; ?>" alt="<?php echo $p['nombre']; ?>">
                    </div>

                    <div class="card-body">
                        <h5 class="card-title"><?php echo $p['nombre']; ?></h5>
                        <p class="text-muted"><?php echo $p['descripcion_producto']; ?></p>
                        <!-- El number_format lo puse porque sino los precios se mostraban raros. -->
                        <p class="fw-bold"><?php echo number_format($p['precio_unidad'], 2, ',', '.'); ?> €</p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
