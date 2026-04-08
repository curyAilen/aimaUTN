<?php
session_start();
include 'conexion.php';
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] =="POST") {
    $id = (int)$_POST['id'];
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $desc = mysqli_real_escape_string($conexion, $_POST['descripcion']);

    if (!empty($_FILES['imagen']['name'])) {
        // Subida de imagen segura
        if (!is_dir('public/productos')) mkdir('public/productos', 0755, true);
        $img_name = time() ."_" . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'],"public/productos/" . $img_name);
        $sql ="UPDATE products SET nombre='$nombre', precio='$precio', stock='$stock', descripcion='$desc', imagen='$img_name' WHERE id=$id";
    } else {
        $sql ="UPDATE products SET nombre='$nombre', precio='$precio', stock='$stock', descripcion='$desc' WHERE id=$id";
    }

    if (mysqli_query($conexion, $sql)) {
        header("Location: editar_producto.php?id=$id&res=ok");
        exit();
    } else {
        $msj_error ="Error al actualizar:" . mysqli_error($conexion);
    }
}

$id_prod = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$query = mysqli_query($conexion,"SELECT * FROM products WHERE id = $id_prod");
$producto = mysqli_fetch_assoc($query);

if (!$producto) {
    header("Location: catalogo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMA - Editar Producto</title>
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
            <i class="fa fa-pencil text-naranja me-2"></i>Editar Producto
        </h1>

        <?php if (isset($_GET['res']) && $_GET['res'] == 'ok'): ?>
            <div class="alert p-2 text-center mb-4">
                <i class="fa fa-check-circle me-1"></i> ¡Producto modificado exitosamente!
            </div>
        <?php endif; ?>

        <?php if (!empty($msj_error)): ?>
            <div class="alert alert-danger p-2 text-center mb-4">
                <i class="fa fa-exclamation-triangle me-1"></i> <?php echo htmlspecialchars($msj_error); ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mb-4">
            <img src="public/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                 onerror="this.src='public/img/logo_aima.png'"
                 class="img-producto shadow-sm">
        </div>

        <form action="editar_producto.php?id=<?php echo $producto['id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            
            <div class="mb-3">
                <label class="form-label-aima">Nombre del Aroma</label>
                <input type="text" name="nombre" class="form-control-aima" 
                       value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label-aima">Precio Unitario</label>
                    <input type="number" name="precio" step="0.01" class="form-control-aima" 
                           value="<?php echo $producto['precio']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-aima">Stock</label>
                    <input type="number" name="stock" class="form-control-aima" 
                           value="<?php echo $producto['stock']; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-aima">Descripción</label>
                <textarea name="descripcion" rows="3" class="form-control-aima" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label-aima">
                    <i class="fa fa-image me-1 text-turquesa"></i> Reemplazar Imagen 
                    <small class="text-muted fw-light">(Opcional)</small>
                </label>
                <input class="form-control" type="file" name="imagen" accept="image/*">
            </div>

            <button type="submit" class="btn-aima w-100 py-3 fs-6">
                <i class="fa fa-save me-2"></i>Guardar Cambios
            </button>
        </form>
    </div>
    
    <a href="catalogo.php" class="btn-aima-outline py-2 px-4 shadow-sm">
        <i class="fa fa-arrow-left me-1"></i> Volver al Catálogo
    </a>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>