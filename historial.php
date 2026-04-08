<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$where_fecha ="";
if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $fecha_filtro = mysqli_real_escape_string($conexion, $_GET['fecha']);
    $where_fecha =" AND DATE(o.created_at) = '$fecha_filtro'";
}

$sql ="SELECT o.id as id_pedido, o.total, o.estado, o.estado_pago, o.metodo_pago, o.created_at, o.telefono, o.email as order_email, o.logistica,
               p.nombre as producto, p.imagen, oi.cantidad, u.nombre as cliente_nombre, u.email as user_email
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE 1=1 $where_fecha
        ORDER BY o.created_at DESC";

$query = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMA - Todos los Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<main class="container mt-5 mb-5">
    <h2 class="mb-4 text-center">Historial Completo de Pedidos</h2>

    <div class="form-container-aima mb-4 p-4">
        <form method="GET" class="row g-3 align-items-end justify-content-center">
            <div class="col-md-5">
                <label class="form-label-aima">Filtrar por Fecha</label>
                <input type="date" name="fecha" class="form-control-aima mb-0" value="<?php echo $_GET['fecha'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn-aima w-100 py-3">Aplicar Filtro</button>
            </div>
            <div class="col-md-3">
                <a href="verpedidos.php" class="btn-aima-outline w-100 py-3 d-flex align-items-center justify-content-center">Volver a Pendientes</a>
            </div>
        </form>
    </div>

    <?php if (mysqli_num_rows($query) > 0): ?>
    <div class="table-container-aima">
        <table class="table table-aima align-middle">
            <thead>
                <tr>
                    <th class="ps-3">Aroma</th>
                    <th class="text-center">Cliente / Contacto</th>
                    <th class="text-center">Cant.</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Pago</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query)) { 
                    $clase_fila = '';
                    if($row['estado'] == 'finalizado') $clase_fila = 'row-finalizada';
                    if($row['estado'] == 'cancelado') $clase_fila = 'row-cancelada';
                ?>
                    <tr class="<?php echo $clase_fila; ?>">
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="public/productos/<?php echo $row['imagen']; ?>" onerror="this.src='public/img/logo_aima.png'" class="img-thumbnail-aima" alt="<?php echo $row['producto']; ?>">
                                <strong ><?php echo strtoupper($row['producto']); ?></strong>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php 
                                $email_contacto = !empty($row['order_email']) ? $row['order_email'] : (!empty($row['user_email']) ? $row['user_email'] : 'Sin email');
                                $telefono_contacto = !empty($row['telefono']) ? $row['telefono'] : 'Sin tel.';
                                $cliente = !empty($row['cliente_nombre']) ? $row['cliente_nombre'] : 'Cargado Manual';
                                $logistica = !empty($row['logistica']) ? $row['logistica'] : 'Retiro en Local';
                            ?>
                            <div >
                                <strong><i class="fa fa-user text-muted"></i> <?php echo $cliente; ?></strong><br>
                                <span class="text-muted"><i class="fa fa-envelope"></i> <?php echo $email_contacto; ?></span><br>
                                <span class="text-muted"><i class="fa fa-phone"></i> <?php echo $telefono_contacto; ?></span><br>
                                <span class="badge bg-light text-dark mt-1 border"><i class="fa fa-truck text-muted"></i> <?php echo $logistica; ?></span>
                            </div>
                        </td>
                        <td class="text-center fs-5"><?php echo $row['cantidad']; ?></td>
                        <td><strong >$<?php echo number_format($row['total'], 2, ',', '.'); ?></strong></td>
                        
                        <td class="text-muted">
                            <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                        </td>

                        <td>
                            <?php 
                            if($row['estado'] == 'cancelado'):
                                echo '<span class="badge bg-secondary px-3 py-2 rounded-pill shadow-sm">CANCELADO</span>';
                            elseif($row['estado_pago'] == 'pagado'):
                                echo '<span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">PAGADO</span>';
                            else:
                                echo '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">PENDIENTE</span>';
                            endif;
                            ?>
                        </td>

                        <td><span class="subtitle text-uppercase font-weight-bold"><?php echo $row['estado']; ?></span></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="empty-state-aima">
            <i class="fa fa-box-open empty-state-icon"></i>
            <h3 >No hay pedidos</h3>
            <p class="text-muted mt-2 subtitle">Aún no se han registrado compras o la búsqueda no arrojó resultados.</p>
        </div>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
