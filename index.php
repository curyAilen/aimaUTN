<?php
session_start();
include("conexion.php");

$query_banner ="SELECT * FROM banner_home ORDER BY orden ASC";
$res_banner = mysqli_query($conexion, $query_banner);
$banners = [];
while ($b = mysqli_fetch_assoc($res_banner)) {
    $banners[] = $b;
}

// Los 10 productos más recientes (ordenados por id DESC)
$query_recientes = mysqli_query($conexion,"SELECT id, nombre, descripcion, precio, imagen FROM products WHERE status = 1 ORDER BY id DESC LIMIT 10");
$productos_recientes = [];
while ($pr = mysqli_fetch_assoc($query_recientes)) {
    // Resolver ruta de imagen
    $pr['img_src'] = 'public/productos/' . $pr['imagen'];
    $productos_recientes[] = $pr;
}
// Dividir en dos filas de máximo 5
$fila1 = array_slice($productos_recientes, 0, 5);
$fila2 = array_slice($productos_recientes, 5, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - AIMA AROMAS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<!-- BANNER PRINCIPAL: CARRUSEL -->
<header class="container-fluid p-0" style="overflow: hidden;">
    <div id="aimaCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#aimaCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#aimaCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#aimaCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            
            <?php foreach ($banners as $i => $ban): ?>
            <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                <?php $banner_img_path = 'public/banner/' . basename($ban['imagen']); ?>
                <div class="d-flex align-items-center justify-content-center" style="position: relative; width: 100%; height: 500px; overflow: hidden;">
                    <img src="<?php echo htmlspecialchars($banner_img_path); ?>?v=<?php echo time(); ?>" 
                         onerror="this.src='public/img/logo_aima.png'"
                         alt="Banner" 
                         style="position: absolute; top: 0; left: 0; width: 100%; height: 500px; object-fit: cover; z-index: 0;">
                    
                    <div class="carousel-caption-aima text-center mx-3" style="position: relative; z-index: 1;">
                        <h2 class="display-5 fw-bold mb-3" style="color: var(--color-tierra-oscuro);"><?php echo htmlspecialchars($ban['titulo']); ?></h2>
                        <p class="fs-5" style="color: var(--color-tierra-oscuro);"><?php echo htmlspecialchars($ban['subtitulo']); ?></p>
                        <a href="catalogo.php" class="btn-aima mt-3 px-4 shadow-sm">Ver Catálogo</a>
                    </div>
                </div>
            </div>
            <?php
endforeach; ?>

        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#aimaCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#aimaCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
</header>

<main class="container py-5 my-4">
    
    <div class="text-center mb-5">
        <h3 class="mb-2">Nuestros Productos</h3>
        <p class="subtitle">Hechos a mano con dedicación y amor</p>
    </div>

    <!-- FILA 1: primeros 5 más recientes -->
    <?php if (!empty($fila1)): ?>
    <div class="mb-5">
        <h4 class="mb-4 home-section-title text-turquesa">Agregados Recientemente</h4>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4 justify-content-center">
            <?php foreach ($fila1 as $prod): ?>
            <div class="col">
                <a href="catalogo.php" class="home-grid-item">
                    <img src="<?php echo htmlspecialchars($prod['img_src']); ?>"
                         onerror="this.src='public/img/logo_aima.png'"
                         alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                         class="img-producto shadow-sm">
                    <h5 class="home-grid-title"><?php echo htmlspecialchars($prod['nombre']); ?></h5>
                    <p class="text-center mb-0">
                        $<?php echo number_format($prod['precio'], 0, ',', '.'); ?>
                    </p>
                </a>
            </div>
            <?php
    endforeach; ?>
        </div>
    </div>
    <?php
endif; ?>

    <!-- FILA 2: siguientes 5 -->
    <?php if (!empty($fila2)): ?>
    <div class="mb-4">
        <h4 class="mb-4 home-section-title text-naranja">También te puede gustar</h4>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4 justify-content-center">
            <?php foreach ($fila2 as $prod): ?>
            <div class="col">
                <a href="catalogo.php" class="home-grid-item">
                    <img src="<?php echo htmlspecialchars($prod['img_src']); ?>"
                         onerror="this.src='public/img/logo_aima.png'"
                         alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                         class="img-producto shadow-sm">
                    <h5 class="home-grid-title"><?php echo htmlspecialchars($prod['nombre']); ?></h5>
                    <p class="text-center mb-0">
                        $<?php echo number_format($prod['precio'], 0, ',', '.'); ?>
                    </p>
                </a>
            </div>
            <?php
    endforeach; ?>
        </div>
    </div>
    <?php
endif; ?>

    <?php if (mysqli_num_rows($query_recientes) == 0): ?>
    <div class="col-12 text-center py-5">
        <h3 style="font-family: 'Poppins', sans-serif; color: #8F8C8A;">No se encuentran productos disponibles en este momento.</h3>
    </div>
    <?php
endif; ?>

</main>

<footer class="text-center py-4 mt-5 bg-white border-top">
    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> - Ailen Aldana Cury</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
