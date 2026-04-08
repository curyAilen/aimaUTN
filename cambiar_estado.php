<?php
session_start();
include("conexion.php");

// Seguridad: Solo Admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Cambiar estado logístico (procesando, finalizado, cancelado)
    if (isset($_GET['nuevo_estado'])) {
        $nuevo_estado = mysqli_real_escape_string($conexion, $_GET['nuevo_estado']);
        $sql = "UPDATE orders SET estado = '$nuevo_estado' WHERE id = '$id'";
        mysqli_query($conexion, $sql);
    }
    
    // Cambiar estado de pago (pendiente, pagado)
    if (isset($_GET['pago'])) {
        $pago = mysqli_real_escape_string($conexion, $_GET['pago']);
        $sql = "UPDATE orders SET estado_pago = '$pago' WHERE id = '$id'";
        mysqli_query($conexion, $sql);
    }
}

// Redirigir de vuelta dependiendo de si se finalizó o no
if (isset($_GET['nuevo_estado']) && $_GET['nuevo_estado'] === 'finalizado') {
    header("Location: finalizarpedidos.php?msg=finalizado");
} else {
    header("Location: verpedidos.php?msg=actualizado");
}
exit();
?>