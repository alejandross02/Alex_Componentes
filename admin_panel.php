<?php
    session_start();
    include "conexion.php";

// Esto lo implemente por tema de seguridad para que poniendo esto en el navegador localhost/ALEX-COMPONENTES/admin_panel.php NO se pueda acceder y requiera logearte.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
}

// Variable donde mostraremos mensajes de error o que todo fue bien
$mensaje_admin = "";

// Cuando se pulsa el boton pues actualiza stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {

    // Recogemos el ID del producto y el nuevo stock enviado por el formulario
    $idProducto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
    $nuevoStock = isset($_POST['nuevo_stock']) ? (int)$_POST['nuevo_stock'] : 0;

    // Validación básica para evitar valores incorrectos
    if ($idProducto <= 0 || $nuevoStock < 0) {
        $mensaje_admin = '<div class="alert alert-danger">Datos inválidos para actualizar stock.</div>';

    } else {

        // Primero obtenemos el stock actual del producto
        $stmt0 = $conexion->prepare("SELECT stock FROM productos WHERE id_producto = ?");
        $stmt0->bind_param("i", $idProducto);
        $stmt0->execute();
        $res0 = $stmt0->get_result();
        $fila = $res0->fetch_assoc();
        $stmt0->close();

        // Guardamos el stock anterior para luego calcular la diferencia
        $stockAnterior = ($fila && isset($fila['stock'])) ? (int)$fila['stock'] : 0;

        // Diferencia entre el stock nuevo y el anterior
        $cantidadCambio = $nuevoStock - $stockAnterior;

        // Actualizamos el stock en la tabla productos
        $stmt = $conexion->prepare("UPDATE productos SET stock = ? WHERE id_producto = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $nuevoStock, $idProducto);

            // Si se actualiza correctamente mostramos mensaje de qur todo ha ido bien
            if ($stmt->execute()) {

                $mensaje_admin = '<div class="alert alert-success">Stock actualizado correctamente.</div>';

                // Insertamos el cambio en el histórico de stock
                $idAdmin = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

                if ($idAdmin) {

                    // Registramos también la cantidad cambiada
                    $stmt2 = $conexion->prepare(
                        "INSERT INTO historico_stock (id_producto, id_admin, fecha, cantidad_cambio)
                         VALUES (?, ?, NOW(), ?)"
                    );

                    if ($stmt2) {
                        $stmt2->bind_param("iii", $idProducto, $idAdmin, $cantidadCambio);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                }

            } else {
                // Por si hubiese error muestra mensaje
                $mensaje_admin = '<div class="alert alert-danger">Error al actualizar el stock.</div>';
            }

            $stmt->close();

        } else {
                // Por si hubiese error muestra mensaje
            $mensaje_admin = '<div class="alert alert-danger">Error en la consulta de actualización.</div>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Alex Componentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="media/logopag.png">
    <style>
        body { background-color: #ffffff !important; }
        .navbar-white { background-color: #ffffff !important; border-bottom: 2px solid #ddd; }
        .navbar-white .nav-link { color: #000 !important; font-weight:600; }
        .navbar-white .nav-link:hover { color: #ff6600 !important; }
        .btn-orange { background-color: #ff6600 !important; color:white !important; border:none; }
        .btn-orange:hover { background-color:#e55c00 !important; }
        table { font-size: 14px; }
        .input-stock { width: 90px; display:inline-block; vertical-align:middle; }
        .btn-stock { padding: .25rem .5rem; font-size: .8rem; }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-white mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <img src="media/logo.jpg" alt="Logo" height="45">
        </a>

        <div class="ms-auto d-flex gap-3">
            <a class="btn btn-outline-danger" href="cerrarsesion.php">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="container">

    <?php if ($mensaje_admin !== "") echo $mensaje_admin; ?>

    <h2 class="mb-3">Productos</h2>

    <table class="table table-striped table-bordered shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio (€)</th>
                <th>Stock</th>
            </tr>
        </thead>

        <tbody>
            <?php
            // Obtenemos todos los productos
            $productos = $conexion->query("SELECT * FROM productos");

            // Los mostramos en filas
            while ($p = $productos->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $p['id_producto']; ?></td>
                    <td><?php echo $p['nombre']; ?></td>
                    <td><?php echo $p['precio_unidad']; ?> €</td>

                    <td>
                        <!-- Stock actual -->
                        <?php echo $p['stock']; ?>

                        <form method="post" action="admin_panel.php" style="display:inline-block; margin-left:10px;">
                            <input type="hidden" name="id_producto" value="<?php echo $p['id_producto']; ?>">
                            <input type="number" name="nuevo_stock" min="0" value="<?php echo $p['stock']; ?>" class="form-control form-control-sm input-stock" />
                            <button type="submit" name="update_stock" class="btn btn-sm btn-orange btn-stock ms-1">
                                Actualizar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2 class="mt-5 mb-3">Clientes</h2>

    <table class="table table-striped table-bordered shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Correo</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Código Postal</th>
            </tr>
        </thead>

        <tbody>
            <?php
            // Obtenemos todos los clientes
            $clientes = $conexion->query("SELECT * FROM clientes");

            while ($c = $clientes->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $c['id_cliente']; ?></td>
                    <td><?php echo $c['correo_electronico']; ?></td>
                    <td><?php echo $c['nombre']; ?></td>
                    <td><?php echo $c['apellidos']; ?></td>
                    <td><?php echo $c['telefono']; ?></td>
                    <td><?php echo $c['direccion']; ?></td>
                    <td><?php echo $c['cod_postal']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2 class="mt-5 mb-3">Pedidos</h2>

    <table class="table table-striped table-bordered shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID Pedido</th>
                <th>Fecha</th>
                <th>Precio Total</th>
                <th>ID Cliente</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $pedidos = $conexion->query("SELECT * FROM pedidos ORDER BY fecha_pedido DESC");

            while ($p = $pedidos->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $p['id_pedido']; ?></td>
                    <td><?php echo $p['fecha_pedido']; ?></td>
                    <td><?php echo $p['precio_total']; ?> €</td>
                    <td><?php echo $p['id_cliente']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2 class="mt-5 mb-3">Histórico de Stock</h2>

    <table class="table table-striped table-bordered mb-5 shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID Hist.</th>
                <th>Producto</th>
                <th>Nombre Admin</th>
                <th>Fecha</th>
                <th>Cambio</th>
            </tr>
        </thead>

        <tbody>
            <?php
            // Consulta para mostrar histórico con nombres del producto y admin
            $sql = "
                SELECT hs.id_historico, p.nombre AS nombre_producto,
                       a.nombre AS nombre_admin, hs.fecha, hs.cantidad_cambio
                FROM historico_stock hs
                LEFT JOIN productos p ON hs.id_producto = p.id_producto
                LEFT JOIN admin a ON hs.id_admin = a.id_admin
                ORDER BY hs.id_historico DESC
            ";

            $historico = $conexion->query($sql);

            while ($h = $historico->fetch_assoc()) {

                $cambio = $h['cantidad_cambio'];

                if ($cambio === null) {
                    $textoCambio = "";
                    $claseCambio = "";
                } else {
                    $textoCambio = ($cambio > 0 ? "+$cambio" : "$cambio");
                    // Para ponerlo bonito en caso de cambio positivo o negativo
                    $claseCambio = ($cambio > 0 ? "cambio-positivo" : "cambio-negativo");
                }
            ?>
                <tr>
                    <td><?php echo $h['id_historico']; ?></td>
                    <td><?php echo $h['nombre_producto']; ?></td>
                    <td><?php echo $h['nombre_admin']; ?></td>
                    <td><?php echo $h['fecha']; ?></td>
                    <td class="<?php echo $claseCambio; ?>"><?php echo $textoCambio; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>
