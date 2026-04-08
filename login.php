<?php
session_start();
include("conexion.php");
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: mostrar_contenido.php");
    }
    else {
        header("Location: index.php");
    }
    exit();
}

$error ="";

if ($_SERVER["REQUEST_METHOD"] =="POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = mysqli_real_escape_string($conexion, trim($_POST['email']));
        $password_ingresada = $_POST['password'];

        $consulta ="SELECT * FROM users WHERE email = '$email'";
        $resultado = mysqli_query($conexion, $consulta);

        if ($row = mysqli_fetch_assoc($resultado)) {
            $password_db = isset($row['password']) ? $row['password'] : (isset($row['password_hash']) ? $row['password_hash'] : '');
            
            // Verificación unificada y pedida strictamente
            if (password_verify($password_ingresada, $password_db)) {

                $_SESSION['admin'] = $row['nombre'];
                $_SESSION['id_usuario'] = $row['id'];
                $_SESSION['rol'] = $row['rol'];
                $_SESSION['email'] = $row['email'];

                if (isset($_GET['redir']) && $_GET['redir'] == 'carrito') {
                    header("Location: carrito.php");
                }
                elseif ($row['rol'] === 'admin') {
                    header("Location: mostrar_contenido.php");
                }
                else {
                    header("Location: index.php");
                }
                exit();
            }
            else {
                $error ="Contraseña incorrecta.";
            }
        }
        else {
            $error ="El email no está registrado.";
        }
    }
    else {
        $error ="Por favor, complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar - AIMA AROMAS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<?php require_once 'nav.php'; ?>

<main class="container d-flex justify-content-center align-items-center flex-column">
    <div class="card p-5 border-0 shadow-sm">
        <h1 class="text-center mb-4 text-rojo">Ingresar</h1>
        
        <?php if (isset($_GET['msj']) && $_GET['msj'] == 'debe_iniciarse_sesion'): ?>
            <div class="alert mb-4 text-center">
                <i class="fa fa-lock me-2"></i> Necesitás iniciar sesión para agregar productos al carrito.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger p-2 text-center shadow-sm"><i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sesion_cerrada'): ?>
            <div class="alert alert-success p-2 text-center shadow-sm"><i class="fa fa-check-circle"></i> Has cerrado sesión correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['msj']) && $_GET['msj'] == 'registro_ok'): ?>
            <div class="alert alert-info p-2 text-center shadow-sm text-turquesa">
                <i class="fa fa-user-check"></i> Registro completado con éxito. ¡Ya puedes iniciar sesión!
            </div>
        <?php endif; ?>

        <form method="post" action="login.php<?php echo isset($_GET['redir']) ? '?redir='.htmlspecialchars($_GET['redir']) : ''; ?>">
            <div class="mb-3">
                <label class="form-label-aima">Email</label>
                <input type="email" name="email" class="form-control-aima" required>
            </div>
            <div class="mb-4">
                <label class="form-label-aima">Contraseña</label>
                <input type="password" name="password" class="form-control-aima" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">Entrar</button>
        </form>
    </div>

    <!-- Botón de Registro Independiente -->
    <div class="text-center mt-4">
        <a href="registro.php" class="btn btn-secondary px-5 py-3 shadow-sm">
            ¿No tienes cuenta? Regístrate aquí
        </a>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>