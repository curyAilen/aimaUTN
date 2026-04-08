<?php
session_start();
include("conexion.php");

// Seguridad: Solo Admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] =="POST") {
    // 1. Recolección de datos maestros del pedido
    $user_id = $_SESSION['id_usuario'];
    $metodo_pago = mysqli_real_escape_string($conexion, trim($_POST['metodo_pago']));
    $logistica = mysqli_real_escape_string($conexion, trim($_POST['logistica'] ?? 'Cargado Manual'));
    $telefono = mysqli_real_escape_string($conexion, trim($_POST['telefono'] ?? 'No especificado'));
    $email = mysqli_real_escape_string($conexion, trim($_POST['email'] ?? 'No especificado'));
    $fecha = date("Y-m-d H:i:s");

    // Arrays de productos y cantidades
    $productos = $_POST['producto']; // Array de nombres
    $cantidades = $_POST['cantidad']; // Array de cantidades integrados

    $total_pago = 0;
    $order_items = []; // Acumulador temporal para insertar tras la cabecera

    // 2. Iteración y Validación de precios
    foreach ($productos as $index => $nombre_prod) {
        $nombre_prod_clean = mysqli_real_escape_string($conexion, trim($nombre_prod));
        $cant = (int)$cantidades[$index];

        if (empty($nombre_prod_clean) || $cant <= 0) {
            continue; // Saltamos filas vacías
        }

        $query_p = mysqli_query($conexion,"SELECT id, precio FROM products WHERE nombre = '$nombre_prod_clean'");
        if ($p_data = mysqli_fetch_assoc($query_p)) {
            $subtotal = $p_data['precio'] * $cant;
            $total_pago += $subtotal;

            $order_items[] = [
                'pid' => $p_data['id'],
                'cant' => $cant,
                'precio' => $p_data['precio']
            ];
        }
    }

    if (empty($order_items)) {
        $error_msj ="Revise los productos seleccionados. No detectamos artículos válidos.";
    }
    else {
        // 3. Inserción Única en 'orders' (Cabecera)
        $sql_order ="INSERT INTO orders (user_id, total, metodo_pago, logistica, telefono, email, estado, created_at) 
                      VALUES ('$user_id', '$total_pago', '$metodo_pago', '$logistica', '$telefono', '$email', 'procesando', '$fecha')";

        if (mysqli_query($conexion, $sql_order)) {
            $id_pedido_nuevo = mysqli_insert_id($conexion);

            // 4. Bucle para popular 'order_items' (Detalle)
            $hubo_error_detalle = false;
            foreach ($order_items as $item) {
                $item_pid = $item['pid'];
                $item_cant = $item['cant'];
                $item_precio = $item['precio'];

                $sql_items ="INSERT INTO order_items (order_id, product_id, cantidad, precio_unitario) 
                              VALUES ('$id_pedido_nuevo', '$item_pid', '$item_cant', '$item_precio')";

                if (!mysqli_query($conexion, $sql_items)) {
                    $hubo_error_detalle = true;
                    $error_msj ="Error en detalle (Producto $item_pid):" . mysqli_error($conexion);
                }
            }

            if (!$hubo_error_detalle) {
                // 5. Descontar stock de cada producto (validación anti-sobreventa)
                foreach ($order_items as $item) {
                    $item_pid = $item['pid'];
                    $item_cant = $item['cant'];
                    mysqli_query($conexion,"UPDATE products SET stock = GREATEST(0, stock - $item_cant) WHERE id = '$item_pid'");
                }
                header("Location: mostrar_contenido.php?msj=pedido_ok");
                exit();
            }

        }
        else {
            $error_msj ="Error al asentar la orden central:" . mysqli_error($conexion);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AIMA - Error de Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
    <?php require_once 'nav.php'; ?>
    <main class="container d-flex justify-content-center align-items-center flex-column">
        <div class="card p-5 border-0 shadow-sm text-center">
            <h1 class="mb-4">Hubo un problema</h1>
            <div class="alert alert-danger px-4 py-3 text-center mb-4">
                <?php echo isset($error_msj) ? $error_msj : 'Error desconocido.'; ?>
            </div>
            <a href="realizar_pedidos.php" class="btn w-100 py-2">
                Volver a intentar
            </a>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>