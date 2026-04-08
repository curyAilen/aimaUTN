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

// Historial estrictamente de finalizados cerrados
$sql ="SELECT o.id as order_id, o.total, o.estado, o.estado_pago, o.metodo_pago, o.created_at, o.telefono, o.email as order_email, o.logistica,
               p.id as product_id, p.nombre as producto, p.imagen, oi.cantidad, u.nombre as cliente_nombre, u.email as user_email
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.estado = 'finalizado' $where_fecha
        ORDER BY o.created_at DESC, o.id DESC";

$query = mysqli_query($conexion, $sql);

// Matriz agrupada
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
            'cliente_nombre' => !empty($row['cliente_nombre']) ? $row['cliente_nombre'] : 'Cargado Manual.',
            'email_contacto' => !empty($row['order_email']) ? $row['order_email'] : (!empty($row['user_email']) ? $row['user_email'] : 'Sin email'),
            'telefono_contacto' => !empty($row['telefono']) ? $row['telefono'] : 'Sin tel.',
            'items' => []
        ];
    }
    $img_src = 'public/productos/' . $row['imagen'];
    $pedidos[$oid]['items'][] = [
        'nombre' => $row['producto'],
        'imagen_src' => $img_src,
        'cantidad' => $row['cantidad']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial CRM - AIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body >

<?php require_once 'nav.php'; ?>

<main class="container mt-5 mb-5">
    <h2 class="mb-4 text-center"><i class="fa fa-archive me-2"></i> Histórico Finalizados</h2>

    <div class="form-container-aima mb-5 p-4 shadow-sm">
        <form method="GET" class="row g-3 align-items-end justify-content-center">
            <div class="col-md-6">
                <label class="form-label text-muted"><i class="fa fa-calendar me-1"></i> FILTRAR HISTORIA POR FECHA</label>
                <input type="date" name="fecha" class="form-control px-3 py-2" value="<?php echo $_GET['fecha'] ?? ''; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn w-100 py-2 text-white">Ejecutar Búsqueda</button>
            </div>
            <?php if (!empty($_GET['fecha'])): ?>
            <div class="col-md-2">
                <a href="finalizarpedidos.php" class="btn btn-outline-secondary w-100 py-2"><i class="fa fa-times"></i></a>
            </div>
            <?php
endif; ?>
        </form>
    </div>

    <!-- Feedback Finalizado -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'finalizado'): ?>
        <div class="alert alert-success mt-3 mb-4 text-center shadow-sm">
            <i class="fa fa-check-circle me-1"></i> El sistema ha sellado tu pedido y lo ha trasladado al CRM histórico con éxito.
        </div>
    <?php
endif; ?>

    <?php if (empty($pedidos)): ?>
        <div class="empty-state-aima">
            <i class="fa fa-box-open empty-state-icon text-muted"></i>
            <h3 >El archivo histórico está en blanco</h3>
            <p class="text-muted mt-2 subtitle">Nadie ha despachado órdenes recientemente.</p>
        </div>
    <?php
else: ?>
        <div class="row w-100 justify-content-center m-0">
            <div class="col-md-10 p-0">
                <?php foreach ($pedidos as $p): ?>
                    <div class="card p-4 mb-5 shadow-sm">
                        
                        <!-- Encabezado Oscurecido para Diferenciar de Activos -->
                        <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 border-bottom pb-3">
                            <div class="mb-3 mb-md-0">
                                <h4 >Registro Archivado #<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></h4>
                                <div class="text-muted mt-2">
                                    <strong><i class="fa fa-user me-1"></i> <?php echo htmlspecialchars($p['cliente_nombre']); ?></strong><br>
                                    <i class="fa fa-envelope me-1"></i> <?php echo htmlspecialchars($p['email_contacto']); ?><br>
                                    <i class="fa fa-calendar me-1"></i> Confirmado: <?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-column gap-2 text-end">
                                <div>
                                    <span class="badge badge-finalizado px-3 py-2 rounded-pill shadow-sm text-uppercase"><i class="fa fa-check-double me-1"></i> CERRADO</span>
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-light text-dark border"><i class="fa fa-truck me-1 text-muted"></i> <?php echo ucfirst($p['logistica']); ?></span>
                                    <span class="badge bg-light text-dark border"><i class="fa fa-credit-card me-1 text-muted"></i> <?php echo ucfirst($p['metodo_pago']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Lista Aislada -->
                        <div class="px-2 mb-3">
                            <div class="row g-2 opacity-75">
                                <?php foreach ($p['items'] as $item): ?>
                                    <div class="col-12 col-md-4">
                                        <div class="d-flex align-items-center p-2 rounded">
                                            <img src="public/productos/<?php echo $item['imagen']; ?>" onerror="this.src='public/img/logo_aima.png'" class="rounded shadow-sm me-2">
                                            <div >
                                                <strong ><?php echo strtoupper($item['nombre']); ?></strong>
                                                <span class="text-muted">Cantidad: <?php echo $item['cantidad']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php
        endforeach; ?>
                            </div>
                        </div>

                        <!-- Pie: Importe de Archivo -->
                        <div class="d-flex justify-content-end align-items-center mb-0 mt-2 px-2">
                            <span class="text-muted me-3">INGRESO DEL REGISTRO:</span>
                            <div class="text-end">
                                <strong >$<?php echo number_format($p['total'], 2, ',', '.'); ?></strong>
                                <?php if ($p['total'] >= 80000): ?>
                                    <div class="mt-1"><span class="badge text-dark opacity-50 px-2 py-1 rounded-pill"><i class="fa fa-gift me-1"></i> Incluyó Envío Bonificado</span></div>
                                <?php
        endif; ?>
                            </div>
                        </div>

                        <!-- Opción de Retorno (Gestión Extraordinaria) -->
                        <div class="mt-3 pt-3 text-end">
                            <a href="cambiar_estado.php?id=<?php echo $p['id']; ?>&nuevo_estado=procesando" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill" onclick="return confirm('¿Devolver este registro a la etapa Activa/Procesando?');" ><i class="fa fa-undo"></i> Reactivar Ciclo Logístico</a>
                        </div>
                    </div>
                <?php
    endforeach; ?>
            </div>
        </div>
    <?php
endif; ?>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
