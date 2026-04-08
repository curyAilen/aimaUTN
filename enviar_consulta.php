<?php
include 'conexion.php';

// CASO 1: Marcar como Leído (desde ver_consultas.php)
if (isset($_GET['check'])) {
    $id = (int)$_GET['check'];

    // Como no existe la columna 'estado', se da de baja la consulta una vez resuelta
    $deleteSQL = "DELETE FROM consultas WHERE id = $id OR idConsultas = $id";
    mysqli_query($conexion, $deleteSQL);

    // Redirigir nuevamente a la lista de consultas
    header("Location: ver_consultas.php");
    exit;

}

// CASO 2: Nueva Consulta (desde contacto.php)
if (isset($_POST['nombre'])) {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $apellido = isset($_POST['apellido']) ? mysqli_real_escape_string($conexion, trim($_POST['apellido'])) : '';
    $nombreCompleto = $nombre . ' ' . $apellido;
    
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $mensaje = mysqli_real_escape_string($conexion, $_POST['mensaje']);
    $fecha = date("Y-m-d");

    // Corrección para la tabla 'consultas: (id, nombre, email, mensaje, fecha)'
    $insertSQL = "INSERT INTO consultas (nombre, email, mensaje, fecha) VALUES ('$nombreCompleto', '$email', '$mensaje', '$fecha')";
    mysqli_query($conexion, $insertSQL);

    header("Location: OLD/contacto.php?ok=1");
    exit;
}

mysqli_close($conexion);
?>
