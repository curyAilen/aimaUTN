<?php
session_start();
include 'conexion.php';

// Solo procesar agregar al carrito si está logueado
if (isset($_POST['agregar_carrito'])) {
    if (!isset($_SESSION['id_usuario'])) {
        header("Location: login.php?msj=debe_iniciarse_sesion");
        exit();
    }
    $p_id = (int)$_POST['product_id'];
    $qty = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
    $u_id = $_SESSION['id_usuario'];
    $check = mysqli_query($conexion,"SELECT id FROM carrito WHERE user_id = '$u_id' AND product_id = '$p_id'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conexion,"UPDATE carrito SET cantidad = cantidad + $qty WHERE user_id = '$u_id' AND product_id = '$p_id'");
    }
    else {
        mysqli_query($conexion,"INSERT INTO carrito (user_id, product_id, cantidad) VALUES ('$u_id', '$p_id', $qty)");
    }
    header("Location: catalogo.php");
    exit();
}

$busqueda = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql ="SELECT * FROM products WHERE status = 1";
if ($busqueda !== '') {
    $search = mysqli_real_escape_string($conexion, $busqueda);
    $sql .=" AND (nombre LIKE '%$search%' OR descripcion LIKE '%$search%')";
}
$resultado = mysqli_query($conexion, $sql);

$logueado = isset($_SESSION['id_usuario']);
$esAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - AIMA AROMAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
<?php require_once 'nav.php'; ?>

<main id="catalogo" class="container mt-4 mb-5">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h1 >Nuestro Catálogo</h1>
        <?php if (!$logueado): ?>
            <a href="login.php" class="btn-aima px-4 py-2">
                <i class="fa fa-user me-1"></i> Iniciar sesión para comprar
            </a>
        <?php
endif; ?>
    </div>

    <?php if (isset($_GET['msj']) && $_GET['msj'] == 'debe_iniciarse_sesion'): ?>
        <div class="alert mb-4 text-center">
            <i class="fa fa-lock me-2"></i> Necesitás <a href="login.php" >iniciar sesión</a> para agregar productos al carrito.
        </div>
    <?php
endif; ?>

    <!-- Grilla -->
    <div class="row g-4">

    <?php 
    if (mysqli_num_rows($resultado) == 0):
    ?>
        <div class="col-12 text-center py-5">
            <h3 style="font-family: 'Poppins', sans-serif; color: #8F8C8A;">No se encuentran productos disponibles en este momento.</h3>
        </div>
    <?php else: ?>
    <?php while ($producto = mysqli_fetch_assoc($resultado)):
    $sinStock = ($producto['stock'] <= 0);
?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card-aima position-relative h-100">

                <?php if ($sinStock): ?>
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                         >
                        <span >SIN STOCK</span>
                    </div>
                <?php
    endif; ?>

                <a href="#" data-bs-toggle="modal" data-bs-target="#modalProducto<?php echo $producto['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                    <div class="card-aima-img-container">
                        <img src="public/productos/<?php echo htmlspecialchars($producto['imagen']); ?>"
                             onerror="this.src='public/img/logo_aima.png'"
                             alt="<?php echo strtoupper($producto['nombre']); ?>" class="img-producto">
                    </div>

                    <h2 class="card-title-aima mt-3"><?php echo strtoupper($producto['nombre']); ?></h2>
                </a>

                <div class="card-price-aima mb-3">
                    $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                </div>

                <?php if ($esAdmin): ?>
                    <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn-aima w-100 mt-auto">
                        <i class="fa fa-edit me-1"></i> Editar
                    </a>

                <?php
    elseif ($logueado): ?>
                    <!-- Usuario logueado: formulario normal -->
                    <form method="post" class="mt-auto">
                        <input type="hidden" name="product_id" value="<?php echo $producto['id']; ?>">
                        <div class="qty-container w-100 justify-content-center">
                            <input type="number" name="cantidad" value="1" min="1" class="qty-input"
                                   <?php echo $sinStock ? 'disabled' : ''; ?>>
                            <button type="submit" name="agregar_carrito" class="btn-pastilla-add"
                                    <?php echo $sinStock ? 'disabled' : ''; ?> title="Agregar al carrito">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </form>

                <?php
    else: ?>
                    <!-- No logueado: botón redirige a login -->
                    <div class="mt-auto text-center">
                        <a href="login.php?msj=debe_iniciarse_sesion" class="btn-pastilla-add w-100 d-flex align-items-center justify-content-center py-2"
                           
                           title="Iniciá sesión para comprar">
                            <i class="fa fa-plus"></i>
                        </a>
                        <small class="d-block mt-2">
                            <i class="fa fa-lock me-1"></i>Iniciá sesión para comprar
                        </small>
                    </div>
                <?php
    endif; ?>

            </div>
        </div>

        <!-- Modal for detail view -->
        <div class="modal fade" id="modalProducto<?php echo $producto['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content" style="background-color: #FDFBF9; border-radius: 25px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15); color: #4A4238;">
                    <button type="button" class="btn-close bg-white shadow p-2 position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 15px; right: 15px; z-index: 1050; border: 1px solid #ddd; width: 32px; height: 32px; border-radius: 50%; opacity: 1;"></button>
                    
                    <div class="modal-body p-0" style="max-height: 85vh; overflow-y: hidden;">
                        <div class="row g-0 h-100">
                            <div class="col-md-4 p-0 d-flex">
                                <img src="public/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" onerror="this.src='public/img/logo_aima.png'" alt="<?php echo strtoupper($producto['nombre']); ?>" class="w-100" style="width: 100%; height: 100%; min-height: 400px; object-fit: cover; border-radius: 25px 0 0 25px;">
                            </div>
                            <div class="col-md-8 p-4 p-md-5 d-flex flex-column" style="max-height: 85vh;">
                                <h3 class="fw-bold mb-2 flex-shrink-0" style="font-family: 'Poppins', sans-serif;"><?php echo strtoupper($producto['nombre']); ?></h3>
                                <p class="mb-3 flex-shrink-0"><i class="fa fa-tag me-2" style="color: #56D9CD;"></i>Aroma & Tipo</p>
                                
                                <div class="mb-4 pe-md-4" style="overflow-y: auto; flex-grow: 1;">
                                    <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.85rem; letter-spacing: 1px; color:#8F8C8A;">Descripción del Producto</h6>
                                    <p style="font-size: 1.1rem; line-height: 1.6; font-weight: 300; text-align: left; color:#4A4238;"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                                </div>
                                
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-auto pt-3 border-top border-light flex-shrink-0">
                                    <div class="d-flex flex-column">
                                        <span class="d-block text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Precio</span>
                                        <div class="d-flex align-items-center gap-3">
                                            <h3 class="fw-bold m-0" style="color: #4A4238;">$<?php echo number_format($producto['precio'], 2, ',', '.'); ?></h3>
                                            <?php if ($producto['stock'] > 0): ?>
                                                <span class="badge rounded-pill" style="background-color: #56D9CD; color: #fff; font-size: 0.8rem; padding: 6px 12px;"><i class="fa fa-check me-1"></i> <?php echo $producto['stock']; ?> disp.</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-danger" style="color: white; font-size: 0.8rem; padding: 6px 12px;"><i class="fa fa-times me-1"></i> Sin Stock</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 mt-md-0">
                                        <?php if ($esAdmin): ?>
                                            <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn px-4 py-2 rounded-pill fw-bold text-white shadow-sm" style="background-color: #FF6B35;">
                                                <i class="fa fa-edit me-1"></i> Editar Producto
                                            </a>
                                        <?php elseif ($logueado): ?>
                                            <form method="post" class="d-flex align-items-center gap-2 m-0">
                                                <input type="hidden" name="product_id" value="<?php echo $producto['id']; ?>">
                                                <div class="d-flex align-items-center px-3 py-2 bg-white" style="border-radius: 25px; border: 1px solid #e0e0e0; min-width: 100px;">
                                                    <label for="cantMod<?php echo $producto['id']; ?>" class="me-2 text-muted" style="font-size:0.9rem; margin-bottom: 0;">Cant.</label>
                                                    <input type="number" id="cantMod<?php echo $producto['id']; ?>" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" class="border-0 bg-transparent text-center p-0 m-0 w-100" <?php echo $sinStock ? 'disabled' : ''; ?> style="outline: none; font-weight: bold; color: #4A4238;">
                                                </div>
                                                <button type="submit" name="agregar_carrito" class="btn text-white px-4 py-2 rounded-pill fw-bold text-uppercase shadow-sm" style="border: none; cursor:pointer; background-color: #FF6B35; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'" <?php echo $sinStock ? 'disabled' : ''; ?> title="Agregar al carrito">
                                                    Agregar <i class="fa fa-shopping-cart ms-1"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="login.php?msj=debe_iniciarse_sesion" class="btn px-4 py-2 rounded-pill fw-bold text-uppercase text-white shadow-sm" title="Iniciá sesión para comprar" style="background-color: #FF6B35;">
                                                <i class="fa fa-lock me-1"></i> Iniciar sesión
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->
    <?php
endwhile; endif; ?>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
