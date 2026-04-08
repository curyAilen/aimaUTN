<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$nombre      = mysqli_real_escape_string($conexion, $_POST['nombre']);
$precio      = (float)$_POST['precio'];
$stock       = (int)$_POST['stock'];
$descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $temp_img  = $_FILES['imagen']['tmp_name'];
    $ext       = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $ext_lower = strtolower($ext);

    // Verificar extensiones permitidas
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext_lower, $allowed)) {
        die("Error: Extensión de imagen no permitida. Use JPG, PNG o WEBP.");
    }

    // Crear carpeta si no existe (entorno nuevo o limpio)
    if (!is_dir('public/productos')) {
        mkdir('public/productos', 0755, true);
    }

    $nombre_final = time() . "_" . strtolower(str_replace(" ", "_", $nombre)) . "." . $ext_lower;
    $ruta_destino = "public/productos/" . $nombre_final;

    if (move_uploaded_file($temp_img, $ruta_destino)) {
        $consulta = "INSERT INTO products (nombre, descripcion, precio, imagen, stock)
                     VALUES ('$nombre', '$descripcion', '$precio', '$nombre_final', '$stock')";

        if (mysqli_query($conexion, $consulta)) {
            header("Location: alta_producto.php?ok=1");
            exit();
        } else {
            die("Error en la base de datos: " . mysqli_error($conexion));
        }
    } else {
        die("Error: No se pudo guardar el archivo. Verifica permisos en la carpeta 'public/productos/'.");
    }
} else {
    $codigo = $_FILES['imagen']['error'] ?? 'desconocido';
    die("Error: Problema con el archivo subido. Código: $codigo");
}
?>