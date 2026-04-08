<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $titulo = mysqli_real_escape_string($conexion, trim($_POST['titulo']));
    $subtitulo = mysqli_real_escape_string($conexion, trim($_POST['subtitulo']));
    
    // Validar extensiones de imagen
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // Actualizar textos
    $update_text = "UPDATE banner_home SET titulo = '$titulo', subtitulo = '$subtitulo' WHERE id = $id";
    mysqli_query($conexion, $update_text);
    
    // Actualizar archivo fotográfico si es subido
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $filename = $_FILES['imagen']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            // Guardar dentro de public/banner/
            $new_name = "banner_" . $id . "_" . time() . "." . $file_ext;
            $destination = "public/banner/" . $new_name;
            
            // Subir al directorio final
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destination)) {
                $update_img = "UPDATE banner_home SET imagen = '$new_name' WHERE id = $id";
                mysqli_query($conexion, $update_img);
            }
        }
    }
    
    header("Location: gestionar_banner.php?msj=ok");
    exit();
}
header("Location: gestionar_banner.php");
exit();
?>
