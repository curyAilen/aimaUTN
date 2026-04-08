<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['id_usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: carrito.php");
    exit();
}

$u_id = mysqli_real_escape_string($conexion, $_SESSION['id_usuario']);
$telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
$email = mysqli_real_escape_string($conexion, $_POST['email']);
$logistica = mysqli_real_escape_string($conexion, $_POST['logistica']);
$metodo_pago = mysqli_real_escape_string($conexion, $_POST['metodo_pago']);

// 1. Obtener productos del carrito y calcular total
$query_cart = mysqli_query($conexion, "SELECT c.product_id, c.cantidad, p.precio 
                                       FROM carrito c 
                                       JOIN products p ON c.product_id = p.id 
                                       WHERE c.user_id = '$u_id'");

if (mysqli_num_rows($query_cart) == 0) {
    header("Location: carrito.php");
    exit();
}

$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($query_cart)) {
    $subtotal = $row['precio'] * $row['cantidad'];
    $total += $subtotal;
    $items[] = $row;
}

mysqli_begin_transaction($conexion);

try {
    // 2. Insertar en orders
    $sql_order = "INSERT INTO orders (user_id, total, metodo_pago, logistica, telefono, email, estado, created_at) 
                  VALUES ('$u_id', '$total', '$metodo_pago', '$logistica', '$telefono', '$email', 'procesando', NOW())";
    mysqli_query($conexion, $sql_order);
    $order_id = mysqli_insert_id($conexion);

    // 3. Insertar en order_items y restar stock
    foreach ($items as $item) {
        $p_id = $item['product_id'];
        $cant = $item['cantidad'];
        $precio = $item['precio'];
        
        $sql_item = "INSERT INTO order_items (order_id, product_id, cantidad, precio_unitario) 
                     VALUES ('$order_id', '$p_id', '$cant', '$precio')";
        mysqli_query($conexion, $sql_item);

        // Restar stock
        mysqli_query($conexion, "UPDATE products SET stock = stock - $cant WHERE id = '$p_id'");
    }

    // 4. Vaciar carrito
    mysqli_query($conexion, "DELETE FROM carrito WHERE user_id = '$u_id'");

    mysqli_commit($conexion);
    $exito = true;

} catch (Exception $e) {
    mysqli_rollback($conexion);
    $exito = false;
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando Pedido - AIMA</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    
</head>
<body>

<script>
    <?php if ($exito): ?>
        Swal.fire({
            title: '¡Pedido Confirmado!',
            text: 'Tu pedido fue enviado con éxito, a la brevedad se comunicarán con usted vía WhatsApp.',
            icon: 'success',
            confirmButtonText: 'Volver al Inicio',
            confirmButtonColor: '#5D4037',
            background: '#ffffff',
            customClass: {
                title: 'font-weight-bold',
                popup: 'rounded-4 shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php';
            }
        });
    <?php else: ?>
        Swal.fire({
            title: 'Error',
            text: 'Hubo un problema procesando el pedido. Intente nuevamente. <?php echo addslashes($error ?? ""); ?>',
            icon: 'error',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#5D4037'
        }).then(() => {
            window.location.href = 'carrito.php';
        });
    <?php endif; ?>
</script>

</body>
</html>
