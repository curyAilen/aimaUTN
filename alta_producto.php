<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMA - Nuevo Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<main class="container d-flex justify-content-center align-items-center flex-column">
    
    <div class="card p-5 border-0 shadow-sm mb-4">
        <h1 class="text-center mb-4">
            <i class="fa fa-plus-circle text-naranja me-2"></i>Nuevo Producto
        </h1>
        
        <?php if (isset($_GET['ok'])) : ?>
            <div class="alert alert-success mt-1 mb-4 p-2 text-center">
                <i class="fa fa-check-circle me-1"></i> ¡Producto cargado con éxito al catálogo!
            </div>
        <?php endif; ?>

        <form action="guardar_producto.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="nombre" class="form-label-aima">Nombre del Aroma</label>
                <input type="text" name="nombre" id="nombre" class="form-control-aima" placeholder="Ej: Vainilla &amp; Coco" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="precio" class="form-label-aima">Precio Unitario</label>
                    <input type="number" name="precio" id="precio" class="form-control-aima" step="0.01" placeholder="0.00" required>
                </div>
                <div class="col-md-6">
                    <label for="stock" class="form-label-aima">Stock Inicial</label>
                    <input type="number" name="stock" id="stock" class="form-control-aima" placeholder="Cantidad" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label-aima">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control-aima" placeholder="Describe las notas olfativas..." required></textarea>
            </div>

            <div class="mb-4">
                <label for="imagen" class="form-label-aima">
                    <i class="fa fa-image me-1 text-turquesa"></i> Imagen del producto
                </label>
                <input class="form-control" type="file" name="imagen" id="imagen" accept="image/*" required>
            </div>

            <button type="submit" class="btn-aima w-100 py-3 mt-2 fs-6">
                <i class="fa fa-upload me-2"></i>Cargar al Catálogo
            </button>
        </form>
    </div>
    
    <a href="mostrar_contenido.php" class="btn-aima-outline py-2 px-4 shadow-sm">
        <i class="fa fa-arrow-left me-1"></i> Volver al Panel
    </a>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>