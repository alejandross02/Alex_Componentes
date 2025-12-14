<?php
// Ver pedidos del cliente
session_start();
include "conexion.php";

// Esto lo implemente por tema de seguridad para que poniendo esto en el navegador localhost/ALEX-COMPONENTES/pedidos.php NO se pueda acceder y requiera logearte
// lo vi buen punto para ponerlo.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'cliente') {
    header("Location: login.php");
}
// Con la sesion extraemos el id del cliente
$idCliente = (int)$_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos - Alex Componentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

<nav class="navbar navbar-expand-lg navbar-white mb-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="cliente_panel.php"><img src="media/logo.jpg" height="45" alt="Logo"></a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-2"><a class="nav-link" href="productos.php">Productos</a></li>
                <li class="nav-item me-2"><a class="nav-link" href="carrito.php">Carrito</a></li>
                <li class="nav-item me-2"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                <li class="nav-item nav-link">Bienvenido, <strong><?php echo $_SESSION['user_name']; ?></strong></li>
                <li class="nav-item"><a class="nav-link" href="cerrarsesion.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Mis pedidos</h2>

    <?php
    // Obtener pedidos del cliente
    $stmt = $conexion->prepare("SELECT id_pedido, fecha_pedido, precio_total FROM pedidos WHERE id_cliente = ? ORDER BY fecha_pedido DESC");
    $stmt->bind_param("i", $idCliente);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo "<div class='alert alert-info'>No has realizado pedidos aún.</div>";
    } else {
        while ($pedido = $res->fetch_assoc()) {
            $idPed = $pedido['id_pedido'];
            $fecha = $pedido['fecha_pedido'];
            $totalPedido = $pedido['precio_total'];

            echo "<div class='card mb-3'>";
            echo "<div class='card-body'>";
            echo "<div class='d-flex justify-content-between'><strong>Pedido #$idPed</strong><span>$fecha</span></div>";
            echo "<div class='mb-2'>Total: $totalPedido €</div>";

            // Obtener lineas de ese pedido
            $stmt2 = $conexion->prepare("
                SELECT lp.cantidad, lp.precio_unidad, p.nombre
                FROM linea_pedido lp
                LEFT JOIN productos p ON lp.id_producto = p.id_producto
                WHERE lp.id_pedido = ?
            ");
            $stmt2->bind_param("i", $idPed);
            $stmt2->execute();
            $res2 = $stmt2->get_result();

            echo "<table class='table table-sm'><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unidad</th></tr></thead><tbody>";
            while ($linea = $res2->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $linea['nombre'] . "</td>";
                echo "<td>" . $linea['cantidad'] . "</td>";
                echo "<td>" . $linea['precio_unidad'] . " €</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            $stmt2->close();
            echo "</div></div>";
        }
    }

    $stmt->close();
    ?>

</div>

</body>
</html>
