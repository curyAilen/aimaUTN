<?php
include 'conexion.php';

$sql1 = "CREATE TABLE IF NOT EXISTS `banner_home` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `imagen_url` VARCHAR(255) NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `subtitulo` TEXT NOT NULL,
  `orden` INT DEFAULT 0
)";

if (mysqli_query($conexion, $sql1)) {
    echo "Tabla creada.\n";
} else {
    echo "Error creando tabla: " . mysqli_error($conexion) . "\n";
}

$check = mysqli_query($conexion, "SELECT count(*) as c FROM banner_home");
$row = mysqli_fetch_assoc($check);
if ($row['c'] == 0) {
    mysqli_query($conexion, "INSERT INTO banner_home (imagen_url, titulo, subtitulo, orden) VALUES ('public/deliciaFR.png', 'AIMA AROMAS', 'Rituales para el bienestar y la pausa.', 1)");
    mysqli_query($conexion, "INSERT INTO banner_home (imagen_url, titulo, subtitulo, orden) VALUES ('public/flores.png', 'JABONES ARTESANALES', 'Aromas suaves y materiales nobles.', 2)");
    mysqli_query($conexion, "INSERT INTO banner_home (imagen_url, titulo, subtitulo, orden) VALUES ('public/chocolateCream.png', 'VELAS DE SOJA', 'Conecta con tu momento de calma.', 3)");
    echo "Registros base insertados.\n";
} else {
    echo "Registros ya existen.\n";
}
?>
