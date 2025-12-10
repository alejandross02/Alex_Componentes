<?php
session_start();
include "conexion.php";

// Esto lo implemente por tema de seguridad para que poniendo esto en el navegador localhost/ALEX-COMPONENTES/cliente_panel.php NO se pueda acceder y requiera logearte
// lo vi buen punto para ponerlo.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'cliente') {
    header("Location: login.php");
}

$cliente_id = (int)$_SESSION['user_id'];
$error_msg = '';
$ok_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Primero actualizamos los datos
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        // Recogemos los datos
        $correo    = isset($_POST['correo']) ? $_POST['correo'] : '';
        $nombre    = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
        $telefono  = isset($_POST['telefono']) ? $_POST['telefono'] : '';
        $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
        $cod_postal= isset($_POST['cod_postal']) ? $_POST['cod_postal'] : '';

        $stmt = $conexion->prepare("UPDATE clientes SET correo_electronico = ?, nombre = ?, apellidos = ?, telefono = ?, direccion = ?, cod_postal = ? WHERE id_cliente = ?");
        if ($stmt) {
            $stmt->bind_param("ssssssi", $correo, $nombre, $apellidos, $telefono, $direccion, $cod_postal, $cliente_id);
            if ($stmt->execute()) {
                // Actualizar nombre en sesión para que se vea el cambio en la web
                $_SESSION['user_name'] = $nombre;
                $ok_msg = "Datos guardados correctamente.";
                // recargar para evitar reenvío de formulario
                header("Location: gestionarcuenta.php");
            } else {
                $error_msg = "Error al guardar los datos.";
            }
            $stmt->close();
        } else {
            $error_msg = "Error en la base de datos.";
        }
    }

    // Para darse de baja
    if (isset($_POST['action']) && $_POST['action'] === 'baja') {

        // borrar lineas de pedido relacionadas
        $stmt1 = $conexion->prepare(
            "DELETE lp FROM linea_pedido lp
             JOIN pedidos p ON lp.id_pedido = p.id_pedido
             WHERE p.id_cliente = ?"
        );
        $ok1 = true;
        if ($stmt1) {
            $stmt1->bind_param("i", $cliente_id);
            $ok1 = $stmt1->execute();
            $stmt1->close();
        } else {
            $ok1 = false;
        }

        // borrar pedidos
        $stmt2 = $conexion->prepare("DELETE FROM pedidos WHERE id_cliente = ?");
        $ok2 = true;
        if ($stmt2) {
            $stmt2->bind_param("i", $cliente_id);
            $ok2 = $stmt2->execute();
            $stmt2->close();
        } else {
            $ok2 = false;
        }

        // borrar cliente
        $stmt3 = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $ok3 = true;
        if ($stmt3) {
            $stmt3->bind_param("i", $cliente_id);
            $ok3 = $stmt3->execute();
            $stmt3->close();
        } else {
            $ok3 = false;
        }

        if ($ok1 !== false && $ok2 !== false && $ok3 !== false) {
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit;
        } else {
            $error_msg = "Error al eliminar la cuenta. Inténtalo de nuevo.";
        }
    }
}

// Cargar datos actuales para mostrar en el formulario
$res = $conexion->query("SELECT correo_electronico, nombre, apellidos, telefono, direccion, cod_postal FROM clientes WHERE id_cliente = $cliente_id");
$c = $res ? $res->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar cuenta - Alex Componentes</title>
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

<nav class="navbar navbar-expand-lg navbar-white mb-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="cliente_panel.php"><img src="media/logo.jpg" height="45" alt="Logo"></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-2"><a class="nav-link" href="productos.php">Productos</a></li>
                <li class="nav-item me-2"><a class="nav-link" href="carrito.php">Carrito</a></li>
                <li class="nav-item"><a class="btn btn-outline-secondary" href="cliente_panel.php">Volver</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-3">Gestionar cuenta</h2>

    <?php
        if ($error_msg !== '') {
            echo '<div class="alert alert-danger">' . $error_msg . '</div>';
        } elseif ($ok_msg !== '') {
            echo '<div class="alert alert-success">' . $ok_msg . '</div>';
        }
        ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h5>Modificar datos</h5>

                <form method="post" action="gestionarcuenta.php">
                    <input type="hidden" name="action" value="update">

                    <div class="mb-2">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control" 
                            value="<?php echo $c ? $c['correo_electronico'] : ''; ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" 
                            value="<?php echo $c ? $c['nombre'] : ''; ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" 
                            value="<?php echo $c ? $c['apellidos'] : ''; ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" 
                            value="<?php echo $c ? $c['telefono'] : ''; ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" 
                            value="<?php echo $c ? $c['direccion'] : ''; ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Código postal</label>
                        <input type="text" name="cod_postal" class="form-control" 
                            value="<?php echo $c ? $c['cod_postal'] : ''; ?>">
                    </div>

                    <button class="btn btn-orange mt-2" type="submit">Guardar cambios</button>
                </form>

            </div>

            <div class="card p-3 mb-3">
                <h5>Eliminar cuenta</h5>
                <p class="text-muted">Al pulsar el botón se eliminará tu cuenta y todo su historial.</p>

                <form method="post" action="gestionarcuenta.php">
                    <input type="hidden" name="action" value="baja">
                    <button class="btn btn-danger" type="submit">Darme de baja</button>
                </form>
            </div>

        </div>

        <div class="col-md-6">
            <h5>Información</h5>
            <p class="text-muted">Esta acción es irreversible: se borrarán tus pedidos y tu cuenta.</p>
        </div>
    </div>
</div>

</body>
</html>
