<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$where_fecha ="";
if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $fecha_filtro = mysqli_real_escape_string($conexion, $_GET['fecha']);
    $where_fecha =" AND DATE(o.created_at) = '$fecha_filtro'";
}

// Solo Procesando o Cancelados
$sql ="SELECT o.id as order_id, o.total, o.estado, o.estado_pago, o.metodo_pago,
               o.created_at, o.telefono, o.email as order_email, o.logistica, o.descripcion,
               p.id as product_id, p.nombre as producto, p.imagen, p.stock,
               oi.cantidad, oi.precio_unitario,
               u.nombre as cliente_nombre, u.email as user_email
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.estado != 'finalizado' $where_fecha
        ORDER BY o.created_at DESC, o.id DESC";

$query = mysqli_query($conexion, $sql);

$pedidos = [];
while ($row = mysqli_fetch_assoc($query)) {
    $oid = $row['order_id'];
    // Calcular si el pedido fue creado en las últimas 5 horas
    $horas_diff = (time() - strtotime($row['created_at'])) / 3600;
    $es_reciente = $horas_diff < 5;

    if (!isset($pedidos[$oid])) {
        $pedidos[$oid] = [
            'id' => $oid,
            'fecha' => $row['created_at'],
            'total' => $row['total'],
            'estado_pago' => strtolower($row['estado_pago']),
            'estado' => strtolower($row['estado']),
            'logistica' => $row['logistica'],
            'metodo_pago' => $row['metodo_pago'],
            'descripcion' => $row['descripcion'],
            'cliente_nombre' => !empty($row['cliente_nombre']) ? $row['cliente_nombre'] : 'Cargado Manual/Ventanilla',
            'email_contacto' => !empty($row['order_email']) ? $row['order_email'] : (!empty($row['user_email']) ? $row['user_email'] : 'Sin email'),
            'telefono_contacto' => !empty($row['telefono']) ? $row['telefono'] : 'Sin tel.',
            'es_reciente' => $es_reciente,
            'items' => []
        ];
    }
    $img_src = 'public/productos/' . $row['imagen'];

    $pedidos[$oid]['items'][] = [
        'nombre' => $row['producto'],
        'imagen_src' => $img_src,
        'cantidad' => $row['cantidad'],
        'precio_unitario' => $row['precio_unitario'],
        'stock' => $row['stock'],
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Activos - AIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
    
</head>
<body >

<?php require_once 'nav.php'; ?>

<main class="container mt-5 mb-5">
    <h2 class="mb-4 text-center">
        <i class="fa fa-clipboard-list me-2"></i> Panel de Pedidos Activos
    </h2>

    <!-- Filtros -->
    <div class="form-container-aima mb-4 p-4 shadow-sm">
        <form method="GET" class="row g-3 align-items-end justify-content-center">
            <div class="col-md-6">
                <label class="form-label text-muted">
                    <i class="fa fa-calendar me-1"></i> FILTRAR POR FECHA EXACTA
                </label>
                <input type="date" name="fecha" class="form-control px-3 py-2"
                       
                       value="<?php echo $_GET['fecha'] ?? ''; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn w-100 py-2 text-white"
                        >
                    Aplicar Filtro
                </button>
            </div>
            <?php if (!empty($_GET['fecha'])): ?>
            <div class="col-md-2">
                <a href="verpedidos.php" class="btn btn-outline-secondary w-100 py-2">
                    <i class="fa fa-times"></i> Limpiar
                </a>
            </div>
            <?php
endif; ?>
        </form>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'actualizado'): ?>
        <div class="alert alert-success mt-3 mb-4 text-center shadow-sm">
            <i class="fa fa-check-circle me-1"></i> El pedido ha sido actualizado con éxito.
        </div>
    <?php
endif; ?>

    <?php if (empty($pedidos)): ?>
        <div class="empty-state-aima">
            <i class="fa fa-box-open empty-state-icon"></i>
            <h3 >No hay pedidos activos</h3>
            <p class="text-muted mt-2 subtitle">Todos los pedidos han sido finalizados o cancelados.</p>
        </div>
    <?php
else: ?>

        <p class="text-muted mb-3">
            <i class="fa fa-hand-pointer me-1"></i>
            Hacé click en cualquier pedido para ver el detalle completo.
            <?php if (count(array_filter($pedidos, fn($p) => $p['es_reciente']))): ?>
                <span class="badge badge-reservado ms-2 px-2 py-1 rounded-pill">
                    <i class="fa fa-clock me-1"></i> Stock Reservado = pedidos &lt; 5hs
                </span>
            <?php
    endif; ?>
        </p>

        <?php foreach ($pedidos as $p): ?>
        <?php
        $estado_pago = $p['estado_pago'];
        $estado = $p['estado'];
        $card_class = $estado === 'cancelado' ? 'cancelado' : '';
?>
        <!-- CARD HORIZONTAL -->
        <div class="pedido-card-horizontal <?php echo $card_class; ?>"
             data-bs-toggle="modal"
             data-bs-target="#modalPedido<?php echo $p['id']; ?>">
            <div class="pedido-num">#<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></div>
            <div class="pedido-fecha">
                <i class="fa fa-calendar me-1"></i><?php echo date('d/m/Y', strtotime($p['fecha'])); ?><br>
                <span ><?php echo date('H:i', strtotime($p['fecha'])); ?>hs</span>
            </div>
            <div class="pedido-cliente">
                <i class="fa fa-user me-1 text-muted"></i><?php echo htmlspecialchars($p['cliente_nombre']); ?>
            </div>
            <div class="pedido-badges">
                <?php if ($p['es_reciente']): ?>
                    <span class="badge badge-reservado px-2 py-1 rounded-pill" title="Stock reservado: pedido reciente (<5hs)">
                        <i class="fa fa-clock"></i> Reservado
                    </span>
                <?php
        endif; ?>
                <?php if ($estado === 'cancelado'): ?>
                    <span class="badge badge-cancelado px-2 py-1 rounded-pill"><i class="fa fa-ban me-1"></i>Cancelado</span>
                <?php
        else: ?>
                    <span class="badge badge-procesando px-2 py-1 rounded-pill">
                        <i class="fa fa-box-open me-1"></i><?php echo ucfirst($estado); ?>
                    </span>
                <?php
        endif; ?>
                <?php if ($estado_pago === 'pagado'): ?>
                    <span class="badge badge-pagado px-2 py-1 rounded-pill"><i class="fa fa-check me-1"></i>Pagado</span>
                <?php
        else: ?>
                    <span class="badge badge-pendiente px-2 py-1 rounded-pill"><i class="fa fa-clock me-1"></i>Pendiente</span>
                <?php
        endif; ?>
                <span class="badge bg-light text-dark border"><i class="fa fa-truck me-1 text-muted"></i><?php echo htmlspecialchars($p['logistica']); ?></span>
            </div>
            <div class="pedido-total">$<?php echo number_format($p['total'], 0, ',', '.'); ?></div>
            <div >
                <i class="fa fa-chevron-right"></i>
            </div>
        </div>

        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modalPedido<?php echo $p['id']; ?>" tabindex="-1"
             aria-labelledby="modalLabel<?php echo $p['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalLabel<?php echo $p['id']; ?>">
                                <i class="fa fa-receipt me-2"></i>
                                Pedido #<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?>
                            </h5>
                            <small >
                                <?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?>hs
                            </small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <!-- Info del cliente -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3">
                                    <p class="mb-1">Cliente</p>
                                    <p class="mb-1 fw-bold"><?php echo htmlspecialchars($p['cliente_nombre']); ?></p>
                                    <p class="mb-1 text-muted">
                                        <i class="fa fa-envelope me-1"></i><?php echo htmlspecialchars($p['email_contacto']); ?>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($p['telefono_contacto']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-3">
                                    <p class="mb-1">Envío & Pago</p>
                                    <p class="mb-1">
                                        <i class="fa fa-truck me-1 text-muted"></i>
                                        <strong><?php echo htmlspecialchars($p['logistica']); ?></strong>
                                    </p>
                                    <p class="mb-1">
                                        <i class="fa fa-credit-card me-1 text-muted"></i>
                                        <?php echo htmlspecialchars($p['metodo_pago'] ?: 'No especificado'); ?>
                                    </p>
                                    <?php if (!empty($p['descripcion'])): ?>
                                    <p class="mb-0 text-muted">
                                        <i class="fa fa-sticky-note me-1"></i><?php echo htmlspecialchars($p['descripcion']); ?>
                                    </p>
                                    <?php
        endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Items del pedido -->
                        <h6 class="text-muted mb-2">
                            Productos del pedido
                        </h6>
                        <?php foreach ($p['items'] as $item): ?>
                        <div class="modal-item-row">
                            <img src="<?php echo $item['imagen_src']; ?>" onerror="this.src='public/img/logo_aima.png'" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                            <div >
                                <strong ><?php echo strtoupper($item['nombre']); ?></strong>
                                <span class="text-muted">
                                    Cant: <?php echo $item['cantidad']; ?>
                                    &nbsp;·&nbsp; Precio unit.: $<?php echo number_format($item['precio_unitario'], 0, ',', '.'); ?>
                                </span>
                                <?php if ($p['es_reciente']): ?>
                                    <span class="badge badge-reservado ms-2 rounded-pill">
                                        <i class="fa fa-lock me-1"></i>Stock Reservado
                                    </span>
                                <?php
            endif; ?>
                            </div>
                            <div class="text-end">
                                <strong >
                                    $<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 0, ',', '.'); ?>
                                </strong>
                            </div>
                        </div>
                        <?php
        endforeach; ?>

                        <!-- Total -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3">
                            <span class="text-muted fw-500">TOTAL</span>
                            <strong >
                                $<?php echo number_format($p['total'], 0, ',', '.'); ?>
                            </strong>
                        </div>
                        <?php if ($p['total'] >= 80000): ?>
                        <div class="text-end mt-1">
                            <span class="badge px-2 py-1 rounded-pill"
                                  >
                                <i class="fa fa-gift me-1"></i> ENVÍO BONIFICADO
                            </span>
                        </div>
                        <?php
        endif; ?>
                    </div>
                    <!-- Acciones en el footer del modal -->
                    <div class="modal-footer">
                        <?php if ($estado_pago !== 'pagado'): ?>
                            <a href="cambiar_estado.php?id=<?php echo $p['id']; ?>&pago=pagado"
                               class="btn btn-outline-success btn-sm px-3 rounded-pill"
                               onclick="return confirm('¿Confirmar pago de $<?php echo $p['total']; ?>?');">
                                <i class="fa fa-dollar-sign me-1"></i> Marcar Pagado
                            </a>
                        <?php
        else: ?>
                            <a href="cambiar_estado.php?id=<?php echo $p['id']; ?>&pago=pendiente"
                               class="btn btn-outline-warning btn-sm px-3 rounded-pill"
                               onclick="return confirm('¿Revertir a Pago Pendiente?');">
                                <i class="fa fa-undo me-1"></i> Desmarcar Pago
                            </a>
                        <?php
        endif; ?>

                        <?php if ($estado !== 'cancelado'): ?>
                            <a href="cambiar_estado.php?id=<?php echo $p['id']; ?>&nuevo_estado=finalizado"
                               class="btn btn-success btn-sm px-4 rounded-pill ms-auto"
                               onclick="return confirm('¿Finalizar y enviar al historial?');">
                                <i class="fa fa-check-double me-1"></i> Finalizar Pedido
                            </a>
                            <a href="cambiar_estado.php?id=<?php echo $p['id']; ?>&nuevo_estado=cancelado"
                               class="btn btn-outline-danger btn-sm px-3 rounded-pill"
                               onclick="return confirm('¿Cancelar permanentemente este pedido?');">
                                <i class="fa fa-ban me-1"></i> Cancelar
                            </a>
                        <?php
        endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endforeach; ?>

    <?php
endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>