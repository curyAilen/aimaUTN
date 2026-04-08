<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php?redir=carrito");
    exit();
}

$u_id = $_SESSION['id_usuario'];

// Obtener el email directamente de la base para sesiones antiguas
$email_query = mysqli_query($conexion,"SELECT email FROM users WHERE id = '$u_id'");
$email_data = mysqli_fetch_assoc($email_query);
$email_usuario = $email_data ? $email_data['email'] : '';

$u_id = mysqli_real_escape_string($conexion, $_SESSION['id_usuario']);

if (isset($_GET['action'])) {
    $p_id = mysqli_real_escape_string($conexion, $_GET['id']);

    if ($_GET['action'] == 'add') {
        mysqli_query($conexion,"UPDATE carrito SET cantidad = cantidad + 1 WHERE user_id = '$u_id' AND product_id = '$p_id'");
    }
    elseif ($_GET['action'] == 'sub') {
        $check = mysqli_query($conexion,"SELECT cantidad FROM carrito WHERE user_id = '$u_id' AND product_id = '$p_id'");
        if ($row = mysqli_fetch_assoc($check)) {
            if ($row['cantidad'] > 1) {
                mysqli_query($conexion,"UPDATE carrito SET cantidad = cantidad - 1 WHERE user_id = '$u_id' AND product_id = '$p_id'");
            }
            else {
                mysqli_query($conexion,"DELETE FROM carrito WHERE user_id = '$u_id' AND product_id = '$p_id'");
            }
        }
    }
    elseif ($_GET['action'] == 'del') {
        mysqli_query($conexion,"DELETE FROM carrito WHERE user_id = '$u_id' AND product_id = '$p_id'");
    }
    header("Location: carrito.php");
    exit();
}

$query = mysqli_query($conexion,"SELECT c.product_id, c.cantidad, p.nombre, p.precio, p.imagen 
                                  FROM carrito c 
                                  JOIN products p ON c.product_id = p.id 
                                  WHERE c.user_id = '$u_id'");
$total_compra = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - AIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<main class="container mt-5 mb-5">
    <h1 class="text-center mb-5">Mi Carrito de Compras</h1>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <div class="table-container-aima table-responsive">
            <table class="table table-aima align-middle">
                <thead>
                    <tr>
                        <th class="ps-3">Producto</th>
                        <th>Precio</th>
                        <th class="text-center">Cantidad</th>
                        <th>Subtotal</th>
                        <th class="text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
    while ($row = mysqli_fetch_assoc($query)):
        $subtotal = $row['precio'] * $row['cantidad'];
        $total_compra += $subtotal;
?>
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="public/productos/<?php echo $row['imagen']; ?>" onerror="this.src='public/img/logo_aima.png'" class="img-thumbnail-aima" alt="<?php echo $row['nombre']; ?>">
                                <strong ><?php echo strtoupper($row['nombre']); ?></strong>
                            </div>
                        </td>
                        <td>$<?php echo number_format($row['precio'], 2, ',', '.'); ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="carrito.php?action=sub&id=<?php echo $row['product_id']; ?>" class="btn-aima-outline px-2 py-1"><i class="fa fa-minus"></i></a>
                                <span class="fw-bold fs-5 px-2"><?php echo $row['cantidad']; ?></span>
                                <a href="carrito.php?action=add&id=<?php echo $row['product_id']; ?>" class="btn-aima-outline px-2 py-1"><i class="fa fa-plus"></i></a>
                            </div>
                        </td>
                        <td><strong >$<?php echo number_format($subtotal, 2, ',', '.'); ?></strong></td>
                        <td class="text-center">
                            <a href="carrito.php?action=del&id=<?php echo $row['product_id']; ?>" class="text-danger fs-5">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
    endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="row align-items-start g-4">
            <div class="col-lg-7">
                <div class="form-container-aima">
                    <h3 class="mb-4">
                        <i class="fa-solid fa-truck-fast text-muted me-2"></i> Detalles del Pedido
                    </h3>
                    <form action="cargar_carrito.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label-aima">Teléfono de Contacto <span class="text-danger">*</span></label>
                                <input type="tel" name="telefono" class="form-control-aima" required placeholder="Cod Área + Número">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-aima">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control-aima text-muted input-readonly-aima" required 
                                       value="<?php echo htmlspecialchars($email_usuario); ?>" 
                                       readonly>
                            </div>
                        </div>

                        <label class="form-label-aima">Logística de Entrega <span class="text-danger">*</span></label>
                        <select name="logistica" id="selectLogistica" class="form-control-aima" required>
                            <option value="" disabled selected>Selecciona logística...</option>
                            <option value="Retiro en Local">Retiro en Local</option>
                            <option value="Envío a domicilio">Envío a domicilio</option>
                        </select>

                        <label class="form-label-aima">Método de Pago <span class="text-danger">*</span></label>
                        <select name="metodo_pago" class="form-control-aima" required>
                            <option value="" disabled selected>Selecciona medio de pago...</option>
                            <option value="Efectivo">Efectivo (Solo en Retiro)</option>
                            <option value="Transferencia">Transferencia Bancaria</option>
                            <option value="Mercado Pago">Mercado Pago</option>
                        </select>

                        <button type="submit" class="btn-aima w-100 fs-5 mt-2 py-3">Finalizar y Procesar Pedido</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="form-container-aima">
                    <h4 >Total del Pedido</h4>
                    <h2 class="mt-3 mb-4">$<?php echo number_format($total_compra, 2, ',', '.'); ?></h2>
                    
                    <div id="shippingNotice" class="mt-4 p-3 rounded d-none">
                        <i class="fa fa-info-circle me-2"></i>
                        <?php if ($total_compra >= 80000): ?>
                            <span class="badge"><i class="fa fa-gift me-1"></i> Envío Bonificado</span>
                            <br><small class="opacity-75 mt-1 d-block">Tu compra supera los $80.000.</small>
                        <?php
    else: ?>
                            El <strong>costo de envío corre por cuenta del cliente</strong>.
                            <br><small class="opacity-75">(Envío gratis superando los $80.000).</small>
                        <?php
    endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php
else: ?>
        <div class="empty-state-aima">
            <i class="fa fa-shopping-basket empty-state-icon"></i>
            <h3 >Tu carrito está vacío</h3>
            <p class="text-muted mt-2 subtitle">Visita el catálogo para descubrir nuestros aromas.</p>
            <a href="catalogo.php" class="btn-aima mt-3 px-5">Ir al Catálogo</a>
        </div>
    <?php
endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logistica = document.getElementById('selectLogistica');
    const notice = document.getElementById('shippingNotice');
    if(logistica && notice) {
        logistica.addEventListener('change', function() {
            if(this.value === 'Envío a domicilio') {
                notice.classList.remove('d-none');
            } else {
                notice.classList.add('d-none');
            }
        });
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
