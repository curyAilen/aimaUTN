<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotras - AIMA AROMAS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<main class="container py-5 my-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0 text-center">
            <!-- Espacio para la foto de las fundadoras -->
            <img src="public/flores.png" onerror="this.src='public/img/logo_aima.png'" alt="Equipo AIMA" class="img-fluid">
        </div>
        <div class="col-lg-6 px-lg-5">
            <h1 class="display-4 fw-bold mb-4 text-rojo">Quiénes Somos</h1>
            <p class="lead mb-4 text-azul">
                Detrás de AIMA AROMAS hay pasión por los detalles y la tranquilidad que aporta un espacio perfumado. Nacimos con la idea de llevar un poquito de magia y calma a cada hogar a través de productos artesanales.
            </p>
            <p class="mb-4">
                Cada vela y jabón que fabricamos está hecho a mano, prestando atención a los ingredientes, eligiendo materiales nobles y priorizando el cuidado de nuestro entorno. Trabajamos de manera dedicada para que, al encender una de nuestras velas o usar uno de nuestros jabones, sientas esa pequeña pausa en tu rutina que tanto merecés. 
            </p>
            <a href="catalogo.php" class="btn-aima mt-2">Conocé nuestros productos</a>
        </div>
    </div>
</main>

<footer class="text-center py-4 text-muted bg-white border-top">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> - Ailen Aldana Cury</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>