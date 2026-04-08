<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Consultas - AIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body >

<?php require_once 'nav.php'; ?>

<main class="container mt-5 mb-5">
    <div class="row">
        <!-- Columna Consultas -->
        <div class="col-md-6">
            <h2 class="text-center mb-4">Consultas Recibidas</h2>
            <?php
            // Evitamos usar 'estado' ya que la tabla original no lo posee
            $consultasSQL ="SELECT * FROM consultas ORDER BY id DESC";
            $resultado_cons = mysqli_query($conexion, $consultasSQL);

            // Intentar idConsultas si falla
            if (!$resultado_cons) {
                $consultasSQL ="SELECT * FROM consultas ORDER BY idConsultas DESC";
                $resultado_cons = mysqli_query($conexion, $consultasSQL);
            }

            if (!$resultado_cons || mysqli_num_rows($resultado_cons) === 0) {
                echo '<div class="alert text-center">No hay consultas pendientes.</div>';
            } else {
                while ($fila = mysqli_fetch_assoc($resultado_cons)) {
                    $id_cons = $fila['id'] ?? $fila['idConsultas'] ?? 0;
            ?>
                    <div class="card p-4 mb-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 >
                                <i class="fa fa-user-circle me-2 text-muted"></i><?php echo htmlspecialchars($fila['nombre'] ?? 'Cliente'); ?> <?php echo htmlspecialchars($fila['apellido'] ?? ''); ?>
                            </h5>
                            <a href="enviar_consulta.php?check=<?php echo $id_cons; ?>" class="btn btn-sm btn-outline-success" title="Marcar como resuelto" onclick="return confirm('¿Descartar esta consulta?');">
                                <i class="fa fa-check"></i>
                            </a>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border me-1"><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($fila['email'] ?? 'Sin email'); ?></span>
                            <?php if(!empty($fila['fecha'])) { ?>
                            <span class="text-muted"><i class="fa fa-calendar"></i> <?php echo htmlspecialchars($fila['fecha']); ?></span>
                            <?php } ?>
                        </div>
                        <p >
                            <?php echo nl2br(htmlspecialchars($fila['mensaje'] ?? '')); ?>
                        </p>
                    </div>
            <?php 
                }
            }
            ?>
        </div>

        <!-- Columna Comentarios -->
        <div class="col-md-6">
            <h2 class="text-center mb-4">Comentarios Públicos</h2>
            <?php
            $comentariosSQL ="SELECT * FROM comentarios ORDER BY id DESC";
            $resultado_coms = mysqli_query($conexion, $comentariosSQL);
            
            // Fallback for old schema variables
            if (!$resultado_coms) {
                $comentariosSQL ="SELECT * FROM comentarios ORDER BY id_comentario DESC";
                $resultado_coms = mysqli_query($conexion, $comentariosSQL);
            }

            if (!$resultado_coms || mysqli_num_rows($resultado_coms) === 0) {
                echo '<div class="alert text-center">No hay comentarios registrados.</div>';
            } else {
                while ($fila = mysqli_fetch_assoc($resultado_coms)) {
                    $id_com = $fila['id'] ?? $fila['id_comentario'] ?? 0;
            ?>
                    <div class="card p-4 mb-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 >
                                <i class="fa fa-comment me-2 text-muted"></i><?php echo htmlspecialchars($fila['nombre'] ?? 'Anónimo'); ?>
                            </h5>
                            <a href="guardar.php?borrar=<?php echo $id_com; ?>" class="btn btn-sm btn-outline-danger" title="Eliminar Comentario" onclick="return confirm('¿Eliminar permanentemente este comentario?');">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                        <div class="mb-2">
                            <?php if(!empty($fila['fecha'])) { ?>
                            <span class="text-muted"><i class="fa fa-calendar"></i> <?php echo htmlspecialchars($fila['fecha']); ?></span>
                            <?php } ?>
                        </div>
                        <p >
                            <?php echo nl2br(htmlspecialchars($fila['mensaje'] ?? $fila['comentario'] ?? '')); ?>
                        </p>
                    </div>
            <?php 
                }
            }
            ?>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
