<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Obtener el email del administrador desde la DB; si falla, usar el de sesión
$admin_id = $_SESSION['id_usuario'] ?? 0;
$email_query = mysqli_query($conexion,"SELECT email FROM users WHERE id = '$admin_id'");
$email_data = mysqli_fetch_assoc($email_query);
$email_usuario = $email_data['email'] ?? ($_SESSION['email'] ?? '');

$query_productos = mysqli_query($conexion,"SELECT nombre, stock, imagen FROM products WHERE stock > 0 ORDER BY nombre ASC");
// We need to fetch all rows into an array for the JS dropdown clones
$productos_html = '<option value="" data-img="" disabled selected>Selecciona un aroma...</option>';
while ($p = mysqli_fetch_assoc($query_productos)) {
    $val = htmlspecialchars($p['nombre']);
    $img = htmlspecialchars($p['imagen']);
    $label = strtoupper($p['nombre']) ." (Stock:" . $p['stock'] .")";
    $productos_html .="<option value=\"$val\" data-img=\"$img\">$label</option>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMA - Cargar Pedido Manual</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<main class="container d-flex justify-content-center align-items-center flex-column">
    
    <div class="card p-5 border-0 shadow-sm mb-4">
        <h1 class="text-center mb-4">Cargar Nuevo Pedido</h1>
        
        <form action="cargar_pedido.php" method="POST">
            
            <!-- Contenedor dinámico de Productos -->
            <div id="productos-container">
                <div class="product-row row g-2 mb-3 align-items-end">
                    <div class="col-md-2 text-center pb-2">
                        <img src="" onerror="this.src='public/img/logo_aima.png'" class="img-thumbnail-aima product-preview border shadow-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aroma del Producto</label>
                        <select name="producto[]" class="form-control px-3 py-2 product-select" required>
                            <?php echo $productos_html; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cant.</label>
                        <input class="form-control px-3 py-2" type="number" name="cantidad[]" min="1" placeholder="Ej: 3" required>
                    </div>
                    <div class="col-md-1">
                        <!-- Vacio en la primera fila -->
                    </div>
                </div>
            </div>

            <button type="button" id="add-product-btn" class="btn-aima-outline w-100 py-2 mb-4">
                <i class="fa fa-plus me-1"></i> Añadir otro producto
            </button>

            <!-- Separador -->
            <hr class="mb-4">

            <!-- Datos de Contacto y Envío -->
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Teléfono del Cliente</label>
                    <input class="form-control px-3 py-2" type="text" name="telefono" placeholder="Opcional">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email del Cliente</label>
                    <input type="email" name="email" class="form-control px-3 py-2 text-muted input-readonly-aima" required 
                           value="<?php echo htmlspecialchars($email_usuario); ?>" 
                           readonly >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Logística / Entrega</label>
                <select name="logistica" class="form-control px-3 py-2" required>
                    <option value="" disabled selected>Seleccioná el tipo de entrega...</option>
                    <option value="Retiro por local">🏪 Retiro por local</option>
                    <option value="Envío">🚚 Envío</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Método de Pago</label>
                <select name="metodo_pago" class="form-control px-3 py-2" required>
                    <option value="" disabled selected>Selecciona una opción...</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia bancaria</option>
                    <option value="Mercado Pago">Mercado Pago</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="form-label">Notas del pedido (Opcional)</label>
                <textarea class="form-control px-3 py-2" name="descripcion" id="descripcion" rows="3" placeholder="Detalles de entrega o empaque..."></textarea>
            </div>

            <button type="submit" class="btn w-100 py-3 text-white" onmouseover="this.style.backgroundColor='var(--color-rojo)'" onmouseout="this.style.backgroundColor='var(--color-text-main)'">
                <i class="fa-solid fa-cart-plus me-2"></i> Confirmar y Registrar Pedido
            </button>

        </form>
    </div>
    
    <a href="mostrar_contenido.php" class="btn-aima-outline py-2 px-4 shadow-sm">Volver al Panel</a>
</main>

<!-- JS para cargar imagenes y clonar filas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAdd = document.getElementById('add-product-btn');
    const container = document.getElementById('productos-container');

    function bindImagePreview(row) {
        const select = row.querySelector('.product-select');
        const imgObj = row.querySelector('.product-preview');
        
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const imgSrc = selectedOption.getAttribute('data-img');
            if (imgSrc) {
                imgObj.src = 'public/productos/' + imgSrc;
                imgObj.style.display = 'block';
            } else {
                imgObj.style.display = 'none';
            }
        });
    }

    // Bind a la primera fila que existe pordefault
    const firstRow = document.querySelector('.product-row');
    if(firstRow) bindImagePreview(firstRow);

    btnAdd.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'product-row row g-2 mb-3 align-items-end';
        row.innerHTML = `
            <div class="col-md-2 text-center pb-2">
                <img src="" onerror="this.src='public/img/logo_aima.png'" class="img-thumbnail-aima product-preview border shadow-sm">
            </div>
            <div class="col-md-6">
                <select name="producto[]" class="form-control px-3 py-2 product-select" required>
                    <?php echo addslashes($productos_html); ?>
                </select>
            </div>
            <div class="col-md-3">
                <input class="form-control px-3 py-2" type="number" name="cantidad[]" min="1" placeholder="Ej: 3" required>
            </div>
            <div class="col-md-1 pb-1">
                <button type="button" class="btn btn-outline-danger px-2 py-2 remove-row-btn"><i class="fa fa-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
        
        bindImagePreview(row);

        row.querySelector('.remove-row-btn').addEventListener('click', function() {
            container.removeChild(row);
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>