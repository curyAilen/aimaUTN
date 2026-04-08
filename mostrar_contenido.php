<?php
session_start();
if(!isset($_SESSION["rol"])||$_SESSION["rol"]!=="admin"){header("Location: login.php");exit();}
include("conexion.php");
$mes_actual=date("Y-m");
$q_mes=mysqli_query($conexion,"SELECT COALESCE(SUM(total),0) as rec FROM orders WHERE estado_pago='pagado' AND DATE_FORMAT(created_at,'%Y-%m')='$mes_actual'");
$recaudacion_mes=mysqli_fetch_assoc($q_mes)["rec"];
$q_act=mysqli_query($conexion,"SELECT COUNT(*) as total FROM orders WHERE estado!='finalizado'");
$pedidos_activos=mysqli_fetch_assoc($q_act)["total"];
$q_prod=mysqli_query($conexion,"SELECT COUNT(*) as total FROM products WHERE status=1");
$total_productos=mysqli_fetch_assoc($q_prod)["total"];
$historico=[];
for($i=0;$i<6;$i++){
$ts=mktime(0,0,0,date("m")-$i,1,date("Y"));
$mes_k=date("Y-m",$ts);$mes_l=date("M Y",$ts);
$qh=mysqli_query($conexion,"SELECT COALESCE(SUM(total),0) as rec FROM orders WHERE estado_pago='pagado' AND DATE_FORMAT(created_at,'%Y-%m')='$mes_k'");
$historico[]=(["label"=>$mes_l,"valor"=>(float)mysqli_fetch_assoc($qh)["rec"],"es_actual"=>($i===0)]);
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Dashboard - AIMA Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="public/styles.css">
</head><body>
<?php require_once"nav.php";?>
<?php if(isset($_GET["msj"])&&$_GET["msj"]==="pedido_ok"):?>
<div class="position-fixed top-0 start-50 translate-middle-x mt-4">
<div class="alert alert-success shadow-lg px-5 py-3 rounded-pill fw-bold"><i class="fa fa-check-circle me-2"></i>Pedido registrado con exito</div>
</div><script>setTimeout(()=>document.querySelector(".alert")?.remove(),3500);</script>
<?php endif;?>
<div class="dashboard-wrapper">
<div class="dash-main">
<h1 ><i class="fa fa-gauge-high me-2"></i>Panel de Administracion</h1>
<div class="kpi-row">
<div class="kpi-card"><div class="kpi-icon blue"><i class="fa fa-dollar-sign"></i></div><div><div class="kpi-label">Recaudado este mes</div><div class="kpi-value">$<?php echo number_format($recaudacion_mes,0,",",".");?></div></div></div>
<div class="kpi-card"><div class="kpi-icon amber"><i class="fa fa-clipboard-list"></i></div><div><div class="kpi-label">Pedidos activos</div><div class="kpi-value"><?php echo $pedidos_activos;?></div></div></div>
<div class="kpi-card"><div class="kpi-icon green"><i class="fa fa-box-open"></i></div><div><div class="kpi-label">Productos activos</div><div class="kpi-value"><?php echo $total_productos;?></div></div></div>
</div>
<div class="container-fluid p-0 mt-2 mb-2">
    <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; background: white;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4" style="color: var(--color-tierra-oscuro);"><i class="fa fa-th-large me-2"></i>Herramientas del Sistema</h5>
            <div class="row row-cols-2 row-cols-md-4 g-3">
                <div class="col"><a href="alta_producto.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-plus-circle d-block mb-1 text-primary"></i>Alta Producto</a></div>
                <div class="col"><a href="catalogo.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-edit d-block mb-1 text-primary"></i>Catálogo & Stock</a></div>
                <div class="col"><a href="verpedidos.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-list-alt d-block mb-1 text-success"></i>Pedidos Activos</a></div>
                <div class="col"><a href="realizar_pedidos.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-cart-plus d-block mb-1 text-success"></i>Cargar Manual</a></div>
                <div class="col"><a href="finalizarpedidos.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-check-double d-block mb-1 text-success"></i>Historial de Ventas</a></div>
                <div class="col"><a href="ver_consultas.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-envelope d-block mb-1 text-warning"></i>Consultas y Reportes</a></div>
                <div class="col"><a href="gestionar_banner.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-images d-block mb-1 text-info"></i>Home Banners</a></div>
                <div class="col"><a href="admin/suministros.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-boxes-stacked d-block mb-1 text-info"></i>Suministros</a></div>
                <div class="col"><a href="admin/calculadora.php" class="btn btn-outline-dark w-100 py-2 fw-medium rounded-4 h-100"><i class="fa fa-calculator d-block mb-1 text-info"></i>Calculadora</a></div>
            </div>
        </div>
    </div>
</div>
</div>
<aside class="dash-sidebar">
<div class="sidebar-card">
<h6><i class="fa fa-chart-line me-1"></i>Recaudacion del Mes</h6>
<div class="rec-amount">$<?php echo number_format($recaudacion_mes,0,",",".");?></div>
<p ><?php echo date("F Y");?> - pagos confirmados</p>
</div>
<div class="sidebar-card">
<h6><i class="fa fa-history me-1"></i>Historico Mensual</h6>
<?php foreach($historico as $h):?>
<div class="hist-row <?php echo $h["es_actual"]?"es-actual":"";?>">
<span class="hist-label"><?php echo $h["es_actual"]?"▸":"";?><?php echo $h["label"];?></span>
<span class="hist-valor">$<?php echo number_format($h["valor"],0,",",".");?></span>
</div>
<?php endforeach;?>
</div>
<div class="sidebar-card">
<h6><i class="fa fa-circle-info me-1"></i>Admin</h6>
<p >Sesion activa:<br><strong ><?php echo htmlspecialchars($_SESSION["email"]??"Admin");?></strong></p>
<a href="logout.php" class="btn btn-outline-danger btn-sm mt-3 w-100 rounded-pill"><i class="fa fa-sign-out me-1"></i>Cerrar Sesion</a>
</div>
</aside>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>