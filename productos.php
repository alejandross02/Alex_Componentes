<?php
    session_start();
    include "conexion.php";

// Variable donde guardaremos mensajes de errores o alertas, la uso mas abajo para informar al usuario si algo falla.
$mensajeUsuario = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['añadir_carrito'])) {

    // Recoger ID del producto enviado desde el formulario
    $idProducto = (int)$_POST['id_producto'];

    // Cantidad seleccionada por el cliente
    $cantidadSeleccionada = (int)$_POST['cantidad'];

    // Evitar cantidades menores de 1
    if ($cantidadSeleccionada < 1) {
        $cantidadSeleccionada = 1;
    }

    // Consultar en la BD el producto para obtener stock disponible
    $consultaProducto = $conexion->prepare("SELECT nombre, stock FROM productos WHERE id_producto = ?");
    $consultaProducto->bind_param("i", $idProducto);
    $consultaProducto->execute();
    $resultado = $consultaProducto->get_result();
    $producto = $resultado->fetch_assoc();
    $consultaProducto->close();

    // Si no existe ese producto en la BD
    if (!$producto) {
        $mensajeUsuario = "<div class='alert alert-danger'>No se pudo encontrar el producto.</div>";

    } else {
        // Convertir a número entero el stock disponible
        $stockDisponible = (int)$producto['stock'];

        // Si no hay stock disponible
        if ($stockDisponible <= 0) {
            $mensajeUsuario = "<div class='alert alert-warning'>No hay stock disponible.</div>";

        } else {

            // Si no existe el carrito en la sesión, lo creamos
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            // Cantidad que hay actualmente de este producto en el carrito
            $cantidadActual = isset($_SESSION['carrito'][$idProducto]) 
                              ? (int)$_SESSION['carrito'][$idProducto] 
                              : 0;

            // Nueva cantidad sumando lo que ya había
            $nuevaCantidad = $cantidadActual + $cantidadSeleccionada;

            // Evitar que supere el stock total
            if ($nuevaCantidad > $stockDisponible) {
                $nuevaCantidad = $stockDisponible;
            }

            // Guardar la cantidad final en el carrito
            $_SESSION['carrito'][$idProducto] = $nuevaCantidad;

            // Mostrar mensaje de éxito
            $mensajeUsuario = "<div class='alert alert-success'>Producto añadido al carrito.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Alex Componentes</title>
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
        .card-img-wrap { height: 220px; overflow: hidden; background: #f5f5f5; }
        .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .cantidad-input { width: 80px; display:inline-block; vertical-align: middle; }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-white mb-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <img src="media/logo.jpg" height="45" alt="Logo">
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>

                <?php 
                // Si el usuario es cliente
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'cliente'): ?>
                    <li class="nav-item"><a class="nav-link" href="cliente_panel.php">Página principal</a></li>
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>
                    <li class="nav-item nav-link">Bienvenido, <strong><?php echo $_SESSION['user_name']; ?></strong></li>
                    <li class="nav-item"><a class="nav-link" href="cerrarsesion.php">Cerrar sesión</a></li>

                <?php 
                // Si es administrador
                elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin_panel.php">Panel Admin</a></li>
                    <li class="nav-item"><a class="nav-link" href="cerrarsesion.php">Cerrar sesión</a></li>

                <?php 
                // Si no hay sesión iniciada
                else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<div class="container">

    <h2 class="mb-3">Productos disponibles</h2>

    <!-- Mostrar mensaje de alerta o de que todo va bien -->
    <?php if ($mensajeUsuario !== "") echo $mensajeUsuario; ?>

    <div class="row">
        <?php
        // Consultar todos los productos de la tabla
        $resultadoProductos = $conexion->query("SELECT * FROM productos");

        // Si hay productos en la BD
        if ($resultadoProductos && $resultadoProductos->num_rows > 0) {

            // Recorrer todos los productos
            while ($producto = $resultadoProductos->fetch_assoc()) {

                $idProducto = $producto['id_producto'];
                $nombreProducto = $producto['nombre'];
                $descripcionProducto = $producto['descripcion_producto'];
                $precioProducto = $producto['precio_unidad'];
                $stockProducto = $producto['stock'];
                $imagenProducto = $producto['ruta_imagen'];
        ?>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">

                <div class="card-img-wrap">
                    <img src="<?php echo $imagenProducto; ?>" alt="<?php echo $nombreProducto; ?>">
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo $nombreProducto; ?></h5>

                    <p class="text-muted"><?php echo $descripcionProducto; ?></p>

                    <p class="fw-bold mb-2"><?php echo $precioProducto; ?> €</p>

                    <div class="mt-auto">

                        <?php if ($stockProducto > 0) { ?>
                            <form method="post" action="productos.php">

                                <input type="hidden" name="id_producto" value="<?php echo $idProducto; ?>">

                                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $stockProducto; ?>" 
                                       class="form-control form-control-sm cantidad-input">

                                <button name="añadir_carrito" class="btn btn-orange btn-sm">Añadir al carrito</button>

                            </form>

                        <?php } else { ?>
                            <!-- Boton desactivado si no hay stock -->
                            <button class="btn btn-secondary btn-sm" disabled>Sin stock</button>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>

        <?php
            }
        } else {
            // En caso de que no haya productos
            echo "<div class='alert alert-info'>No hay productos disponibles.</div>";
        }
        ?>
    </div>

</div>

</body>
</html>
