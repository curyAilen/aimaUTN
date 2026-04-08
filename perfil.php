<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id_usuario'];

$query = mysqli_query($conexion,"
    SELECT o.id as order_id, o.total, o.estado_pago, o.estado, o.logistica, o.metodo_pago, o.created_at,
           p.nombre, p.imagen, oi.cantidad, oi.precio_unitario
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = '$user_id'
    ORDER BY o.created_at DESC");

// Agrupar items por pedido
$pedidos = [];
while ($row = mysqli_fetch_assoc($query)) {
    $oid = $row['order_id'];
    if (!isset($pedidos[$oid])) {
        $pedidos[$oid] = [
            'id' => $oid,
            'fecha' => $row['created_at'],
            'total' => $row['total'],
            'estado_pago' => strtolower($row['estado_pago']),
            'estado' => strtolower($row['estado']),
            'logistica' => strtolower($row['logistica']),
            'metodo_pago' => $row['metodo_pago'],
            'items' => []
        ];
    }
    $pedidos[$oid]['items'][] = [
        'nombre' => $row['nombre'],
        'imagen' => $row['imagen'],
        'cantidad' => $row['cantidad'],
        'precio_unitario' => $row['precio_unitario']
    ];
}

// Lógica de fechas (+2 días hábiles)
function sumarDiasHabiles($fecha_string, $dias_a_sumar) {
    // Tomamos la fecha de creación como referencia base para el pago.
    $date = new DateTime($fecha_string);
    $agregados = 0;
    while ($agregados < $dias_a_sumar) {
        $date->modify('+1 day');
        if ($date->format('N') < 6) { // Lunes a Viernes es 1-5
            $agregados++;
        }
    }
    return $date->format('d/m/Y');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - AIMA</title>
    <!-- Bootstrap minimalista -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body >

<?php require_once 'nav.php'; ?>

<main class="container mt-5 mb-5">
    <h2 class="mb-5 text-center"><i class="fa fa-shopping-bag me-2"></i> Mi Historial de Compras</h2>

    <?php if (empty($pedidos)): ?>
        <div class="empty-state-aima">
            <i class="fa fa-shopping-basket empty-state-icon text-muted"></i>
            <h3 >No tienes pedidos aún</h3>
            <p class="text-muted mt-2 subtitle">Explora nuestro catálogo y descubre el aroma ideal para ti.</p>
            <a href="catalogo.php" class="btn-aima px-5 py-2 mt-3">Ir al Catálogo</a>
        </div>
    <?php else: ?>
        <div class="row w-100 justify-content-center m-0">
            <div class="col-md-9 p-0">
                <?php foreach ($pedidos as $p): ?>
                    <div class="card p-4 mb-4 shadow-sm" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'">
                        
                        <!-- Cabecera -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div>
                                <h5 >Pedido #<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></h5>
                                <span class="text-muted"><i class="fa fa-calendar me-1"></i><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></span>
                            </div>
                            <div class="d-flex gap-2 mt-2 mt-md-0">
                                <!-- Estado de Pago -->
                                <?php if ($p['estado_pago'] === 'pagado'): ?>
                                    <span class="badge badge-pagado px-3 py-2 rounded-pill shadow-sm"><i class="fa fa-check me-1"></i> Pagado</span>
                                <?php else: ?>
                                    <span class="badge badge-pendiente px-3 py-2 rounded-pill shadow-sm"><i class="fa fa-clock me-1"></i> Pendiente</span>
                                <?php endif; ?>

                                <!-- Estado Logístico -->
                                <?php if ($p['estado'] === 'cancelado'): ?>
                                    <span class="badge badge-cancelado px-3 py-2 rounded-pill shadow-sm"><i class="fa fa-times me-1"></i> Cancelado</span>
                                <?php else: ?>
                                    <span class="badge badge-estado-entrega px-3 py-2 rounded-pill shadow-sm text-uppercase"><?php echo $p['estado']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Detalles -->
                        <div class="mb-4">
                            <span class="badge bg-light text-dark border me-2"><i class="fa fa-truck me-1 text-muted"></i> <?php echo ucfirst($p['logistica']); ?></span>
                            <span class="badge bg-light text-dark border"><i class="fa fa-credit-card me-1 text-muted"></i> <?php echo ucfirst($p['metodo_pago']); ?></span>
                        </div>

                        <!-- Lógica Especial de Retiro -->
                        <?php if ($p['estado_pago'] === 'pagado' && strpos(strtolower($p['logistica']), 'retiro') !== false && $p['estado'] !== 'cancelado'): ?>
                            <div class="alert px-4 py-3 mb-4 shadow-sm d-flex align-items-center">
                                <i class="fa fa-info-circle me-3 fs-3 text-warning"></i>
                                <div >
                                    <strong >¡Tu orden está casi lista!</strong><br>
                                    Su pedido estará de forma garantizada listo para retirar el día <strong ><?php echo sumarDiasHabiles($p['fecha'], 2); ?></strong> en el horario habitual.
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Resumen Artículos -->
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    <?php foreach ($p['items'] as $item): ?>
                                    <tr>
                                        <td >
                                            <img src="public/productos/<?php echo $item['imagen']; ?>" onerror="this.src='public/img/logo_aima.png'" class="rounded shadow-sm">
                                        </td>
                                        <td>
                                            <strong ><?php echo strtoupper($item['nombre']); ?></strong>
                                        </td>
                                        <td class="text-center text-muted col-2">
                                            x<?php echo $item['cantidad']; ?>
                                        </td>
                                        <td class="text-end pe-3 col-3 fw-bold">
                                            $<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2, ',', '.'); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr >
                                        <td colspan="3" class="text-end pt-3">
                                            <span class="text-muted">Total de la Compra:</span>
                                        </td>
                                        <td class="text-end pt-3 pe-3">
                                            <strong >$<?php echo number_format($p['total'], 2, ',', '.'); ?></strong>
                                            <?php if ($p['total'] >= 80000): ?>
                                                <div class="mt-1"><span class="badge"><i class="fa fa-gift me-1"></i> Envío Bonificado</span></div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
