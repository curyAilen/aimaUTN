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
        die("Error: Formato no permitido. Use JPG, PNG o WEBP.");
    }

    // Normalización de Nombres de Archivo
    $nombre_original = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME);
    $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $nombre_limpio = strtr($nombre_original, $unwanted_array);
    $nombre_limpio = str_replace(" ", "_", $nombre_limpio);
    // Eliminar caracteres especiales exceptuando guion bajo y alfanuméricos
    $nombre_limpio = preg_replace('/[^A-Za-z0-9_]/', '', $nombre_limpio);
    $nombre_archivo_limpio = time() . "_" . strtolower($nombre_limpio) . "." . $ext_lower;

    // Verificación y Creación Automática de Carpetas
    $directorio = "public/productos/";
    if (!file_exists($directorio)) {
        if (!mkdir($directorio, 0777, true)) {
            die("Error: Carpeta inexistente. No se pudo crear la carpeta de destino.");
        }
    }

    $ruta_destino = $directorio . $nombre_archivo_limpio;

    if (move_uploaded_file($temp_img, $ruta_destino)) {
        $consulta = "INSERT INTO products (nombre, descripcion, precio, imagen, stock)
                     VALUES ('$nombre', '$descripcion', '$precio', '$nombre_archivo_limpio', '$stock')";

        if (mysqli_query($conexion, $consulta)) {
            header("Location: alta_producto.php?ok=1");
            exit();
        } else {
            // Si la base de datos falla, podemos borrar el archivo subido
            if (file_exists($ruta_destino)) {
                unlink($ruta_destino);
            }
            die("Error en la base de datos: " . mysqli_error($conexion));
        }
    } else {
        if (!is_writable($directorio)) {
            die("Error: No se pudo guardar el archivo. Verifica permisos en la carpeta 'public/productos/'.");
        }
        die("Error: No se pudo mover el archivo. Verifica la configuración del servidor.");
    }
} else {
    $codigo = $_FILES['imagen']['error'] ?? 'desconocido';
    die("Error: Problema con el archivo subido. Código: $codigo");
}
?>