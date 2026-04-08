<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query ="SELECT * FROM banner_home ORDER BY orden ASC";
$result = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMA - Gestionar Banner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<main class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestionar Banner Principal</h1>
        <a href="mostrar_contenido.php" class="btn-aima-outline px-4 py-2">
            <i class="fa fa-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>

    <?php if (isset($_GET['msj']) && $_GET['msj'] == 'ok'): ?>
        <div class="alert alert-success shadow-sm mb-4">
            <i class="fa fa-check-circle me-1"></i> ¡El banner ha sido actualizado con éxito!
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-12">
            <div class="banner-admin-card">
                <form action="procesar_banner.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    
                    <div class="row align-items-center g-3">
                        <!-- Miniatura -->
                        <div class="col-md-2 text-center">
                            <p class="banner-slide-label mb-2">Slide #<?php echo $row['orden']; ?></p>
                            <?php $banner_img_path = (strpos($row['imagen'], 'public/') === 0) ? $row['imagen'] : 'public/banner/' . $row['imagen']; ?>
                            <img src="<?php echo htmlspecialchars($banner_img_path); ?>?v=<?php echo time(); ?>"
                                 onerror="this.src='public/img/logo_aima.png'"
                                 alt="Banner Preview"
                                 class="banner-thumb img-thumbnail">
                        </div>

                        <!-- Edición de Textos -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label">
                                    Título Principal <small class="text-muted fw-light">(Poppins Bold)</small>
                                </label>
                                <input type="text" name="titulo" class="form-control"
                                       value="<?php echo htmlspecialchars($row['titulo']); ?>"
                                        required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Subtítulo / Texto descriptivo <small class="text-muted">(Poppins Light)</small>
                                </label>
                                <textarea name="subtitulo" class="form-control" rows="2"
                                           required><?php echo htmlspecialchars($row['subtitulo']); ?></textarea>
                            </div>
                            <div>
                                <label class="form-label">
                                    <i class="fa fa-image me-1 text-turquesa"></i> Reemplazar Imagen
                                    <small class="text-muted fw-light">(Dejar vacío si no aplica)</small>
                                </label>
                                <input class="form-control form-control-sm" type="file" 
                                       name="imagen" accept=".jpg,.jpeg,.png,.webp"
                                       >
                            </div>
                        </div>

                        <!-- Acción guardar -->
                        <div class="col-md-3 text-center">
                            <button type="submit" class="banner-save-btn">
                                <i class="fa fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

</main>

<footer class="text-center py-4 mt-5 bg-white border-top">
    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> - Ailen Aldana Cury</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
