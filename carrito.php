<?php
    session_start();
    include "conexion.php";

    // Esto lo implemente por tema de seguridad para que poniendo esto en el navegador localhost/ALEX-COMPONENTES/carrito.php NO se pueda acceder y requiera logearte ya que fallaria
    // al no tener sesion
    // lo vi buen punto para ponerlo.
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'cliente') {
        header("Location: login.php");
    }

    $mensaje = "";

    // actualizar, eliminar y finalizar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Limpiamos convertimos a int y lo guardamos en la sesion del cliente
        if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
            $idProd = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $cant = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
            if ($cant < 1) $cant = 1;

            // si ese producto ya estaba en el carrito, reemplazamos la cantidad
            if (isset($_SESSION['carrito'][$idProd])) {
                $_SESSION['carrito'][$idProd] = $cant;
                $mensaje = "<div class='alert alert-success'>Cantidad actualizada.</div>";
            }
        }

        // Quita la entrada del array de la sesion
        if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
            $idProd = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            if (isset($_SESSION['carrito'][$idProd])) {
                unset($_SESSION['carrito'][$idProd]);
                $mensaje = "<div class='alert alert-info'>Producto eliminado del carrito.</div>";
            }
        }

        // Cuando finalizamos, primero realiza comprobaciones dentro de la base de datos
        if (isset($_POST['accion']) && $_POST['accion'] === 'finalizar') {
            if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
                // Si el carrito esta vacio no se puede finalizar
                $mensaje = "<div class='alert alert-warning'>Tu carrito está vacío.</div>";
            } else {
                // Copio el carrito a una variable
                $carrito = $_SESSION['carrito'];
                $total = 0.0;
                $stock_ok = true;

                // Ahora validamos el stock y calculamos el total
                // Recorremos cada producto del carrito y consultamos precio y stock en la base de datos
                foreach ($carrito as $id => $cantidad) {
                    $stmt = $conexion->prepare("SELECT precio_unidad, stock FROM productos WHERE id_producto = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $fila = $res->fetch_assoc();
                    $stmt->close();

                    // si el producto no se encuentra en la base de datos, fallo (todo lo implementé por comprobaciones porque simepre va a estar el producto en la base de datos)
                    if (!$fila) { $stock_ok = false; break; }

                    // convertir a tipos correctos
                    $precio = (float)$fila['precio_unidad'];
                    $stock = (int)$fila['stock'];

                    // si la cantidad pedida se pasa del stock, pone fallo
                    if ($cantidad > $stock) { $stock_ok = false; break; }

                    // acumular total precio * cantidad
                    $total += $precio * $cantidad;
                }

                // Si alguna validación de stock falla informamos al cliente con este mensaje
                if (!$stock_ok) {
                    $mensaje = "<div class='alert alert-danger'>No hay stock suficiente para algún producto.</div>";
                } else {
                    // Guardo fecha de ahora con NOW el precio total y el id del cliente que hace el pedido
                    $idCliente = (int)$_SESSION['user_id'];
                    $stmt = $conexion->prepare("INSERT INTO pedidos (fecha_pedido, precio_total, id_cliente) VALUES (NOW(), ?, ?)");
                    $stmt->bind_param("di", $total, $idCliente);
                    $ok = $stmt->execute();

                    if ($ok) {
                        // Obtengo el id del pedido creado para recrear ahora en lineas
                        $idPedido = $conexion->insert_id;
                        $stmt->close();


                        // Insertamos lineas y restamos el stock
                        // Por cada producto del carrito
                        foreach ($carrito as $id => $cantidad) {
                            // consultar precio actual y stock otra vez (lo hice por evitar fallo, de nuevo por si acaso)
                            $stmtp = $conexion->prepare("SELECT precio_unidad, stock FROM productos WHERE id_producto = ?");
                            $stmtp->bind_param("i", $id);
                            $stmtp->execute();
                            $resP = $stmtp->get_result();
                            $fprod = $resP->fetch_assoc();
                            $stmtp->close();

                            // precio que cogemos en la linea
                            $precio_linea = (float)$fprod['precio_unidad'];

                            // Insertar la línea de pedido (id_pedido, id_producto, cantidad, precio_unidad)
                            $stmt2 = $conexion->prepare("INSERT INTO linea_pedido (id_pedido, id_producto, cantidad, precio_unidad) VALUES (?, ?, ?, ?)");
                            $stmt2->bind_param("iiid", $idPedido, $id, $cantidad, $precio_linea);
                            $stmt2->execute();
                            $stmt2->close();

                            // Actualizar stock en la tabla productos restnndo la cantidad vendida
                            $nuevoStock = (int)$fprod['stock'] - $cantidad;
                            $stmt3 = $conexion->prepare("UPDATE productos SET stock = ? WHERE id_producto = ?");
                            $stmt3->bind_param("ii", $nuevoStock, $id);
                            $stmt3->execute();
                            $stmt3->close();
                        }


                        // Una vez hecho todo, vaciamos el carrito de la sesion entendiendo que se fue para pedidos y mensajito para el cliente
                        unset($_SESSION['carrito']);
                        $mensaje = "<div class='alert alert-success'>Pedido realizado correctamente. ID pedido: $idPedido</div>";
                    } else {
                        // Si falla la insercion del pedido mostrar error y cerrar, lo pongo para cubrir errores
                        $mensaje = "<div class='alert alert-danger'>Error al crear el pedido.</div>";
                        $stmt->close();
                    }
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito - Alex Componentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #ffffff !important; }
        .navbar-white { background-color: #ffffff !important; border-bottom: 2px solid #ddd; }
        .navbar-white .nav-link { color: #000 !important; font-weight:600; }
        .navbar-white .nav-link:hover { color: #ff6600 !important; }
        .btn-orange { background-color: #ff6600 !important; color:white !important; border:none; }
        .btn-orange:hover { background-color:#e55c00 !important; }
        .card { border-radius: 8px; }
        .cantidad-input { width: 80px; display:inline-block; vertical-align: middle; }
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
    <h2 class="mb-3">Mi carrito</h2>

    <!-- Muestra mensaje SOLO en caso de que haya algo -->
    <?php if ($mensaje !== "") echo $mensaje; ?>

    <?php
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        echo "<div class='alert alert-info'>Tu carrito está vacío.</div>";
    } else {
        $total = 0.0;
        echo "<table class='table'><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unidad</th><th>Subtotal</th><th></th></tr></thead><tbody>";

        // Recorremos las lineas del carrito almacenados en la sesion
        foreach ($_SESSION['carrito'] as $id => $cant) {
            // Consultamos nombre y precio en la base de datos para mostrarlo
            $stmt = $conexion->prepare("SELECT nombre, precio_unidad FROM productos WHERE id_producto = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $f = $res->fetch_assoc();
            $stmt->close();

            // Si por algun motivo no existe el producto, lo saltamos con el continue, lo puse para evitar errores
            if (!$f) continue;

            $nombre = $f['nombre'];
            $precio = (float)$f['precio_unidad'];
            $subtotal = $precio * $cant;
            $total += $subtotal;

            echo "<tr>";
            echo "<td>$nombre</td>";
            echo "<td>
                    <form method='post' style='display:inline-block;'>
                        <input type='hidden' name='accion' value='actualizar'>
                        <input type='hidden' name='id_producto' value='$id'>
                        <input type='number' name='cantidad' value='$cant' min='1' class='form-control form-control-sm cantidad-input' style='display:inline-block; width:80px;'>
                        <button class='btn btn-sm btn-orange ms-1' type='submit'>Añadir</button>
                    </form>
                  </td>";
            echo "<td>$precio €</td>";
            echo "<td>$subtotal €</td>";
            echo "<td>
                    <form method='post' style='display:inline-block;'>
                        <input type='hidden' name='accion' value='eliminar'>
                        <input type='hidden' name='id_producto' value='$id'>
                        <button class='btn btn-sm btn-secondary' type='submit'>Eliminar</button>
                    </form>
                  </td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "<div class='d-flex justify-content-between align-items-center'>";
        echo "<strong>Total: $total €</strong>";
        echo "<form method='post'>
                <input type='hidden' name='accion' value='finalizar'>
                <button class='btn btn-orange' type='submit'>Finalizar pedido</button>
              </form>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>
