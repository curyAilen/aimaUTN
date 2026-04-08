<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-aima sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">AIMA AROMAS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAima" aria-controls="navAima" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navAima">
            
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
                <li class="nav-item"><a class="nav-link" href="nosotras.php">Nosotras</a></li>

                <!-- Buscador Integrado -->
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <form class="d-flex" action="catalogo.php" method="GET">
                        <input class="form-control me-2" type="search" name="search" placeholder="Buscar producto, aroma, tipo..." aria-label="Search">
                        <button class="btn btn-aima-outline" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </li>
            </ul>

            <ul class="navbar-nav align-items-center">
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link btn-nav-action mx-1" href="mostrar_contenido.php">Panel Admin</a></li>
                    <?php elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente'): ?>
                        <li class="nav-item"><a class="nav-link mx-1 text-azul" href="perfil.php"><i class="fa fa-user me-1"></i>Mis Pedidos</a></li>
                        <li class="nav-item"><a class="nav-link btn-nav-action mx-1" href="carrito.php"><i class="fa fa-shopping-cart"></i> Carrito</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link text-naranja" href="logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link btn-nav-action mx-1" href="login.php">Iniciar Sesión / Registro</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>