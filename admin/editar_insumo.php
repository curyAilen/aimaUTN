<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}
include('../conexion.php');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $q  = mysqli_query($conexion, "SELECT * FROM insumos WHERE id=$id LIMIT 1");
    if ($row = mysqli_fetch_assoc($q)) {
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'No encontrado']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre'] ?? ''));
    $tipo   = in_array($_POST['tipo'] ?? '', ['unidad','volumen','peso']) ? $_POST['tipo'] : 'unidad';
    $precio = (float)($_POST['precio_total_compra'] ?? 0);
    $lote   = (float)($_POST['cantidad_lote']       ?? 1);
    $cont   = (float)($_POST['contenido_por_unidad'] ?? 1);
    $prov   = mysqli_real_escape_string($conexion, trim($_POST['proveedor']    ?? ''));
    $fecha  = mysqli_real_escape_string($conexion, trim($_POST['fecha_compra'] ?? ''));
    $fecha_sql = !empty($fecha) ? "'$fecha'" : 'NULL';

    if ($id > 0 && !empty($nombre) && $precio > 0 && $lote > 0 && $cont > 0) {
        $ok = mysqli_query($conexion, "UPDATE insumos SET
          nombre='$nombre', tipo='$tipo',
          precio_total_compra=$precio, cantidad_lote=$lote, contenido_por_unidad=$cont,
          proveedor='$prov', fecha_compra=$fecha_sql
          WHERE id=$id");
        echo json_encode(['ok' => (bool)$ok]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
    }
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
