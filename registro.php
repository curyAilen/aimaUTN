<?php
session_start();
include("conexion.php");

if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$msj ="";
$error ="";

if ($_SERVER["REQUEST_METHOD"] =="POST") {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $celular = mysqli_real_escape_string($conexion, trim($_POST['celular']));
    $email = mysqli_real_escape_string($conexion, trim($_POST['email']));
    $password_ingresada = $_POST['password'];
    $direccion = isset($_POST['direccion']) ? mysqli_real_escape_string($conexion, trim($_POST['direccion'])) : '';
    
    if (empty($nombre) || empty($celular) || empty($email) || empty($password_ingresada)) {
        $error ="Todos los campos obligatorios (*) deben ser completados.";
    } else {
        $checkEmail = mysqli_query($conexion,"SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $error ="El email ya está registrado.";
        } else {
            // Aplicar hash
            $pass_hash = password_hash($password_ingresada, PASSWORD_DEFAULT);
            $rol = 'cliente';

            // Usando backticks para evitar conflicto con palabra reservada de DB, y exactamente las columnas solicitadas
            $sql ="INSERT INTO users (nombre, celular, email, `password`, direccion, rol) VALUES ('$nombre', '$celular', '$email', '$pass_hash', '$direccion', '$rol')";
            
            if (mysqli_query($conexion, $sql)) {
                header("Location: login.php?msj=registro_ok");
                exit();
            } else {
                $error ="Error al completar tu registro. Revisa los datos e intenta nuevamente. Detalle:" . mysqli_error($conexion);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - AIMA AROMAS</title>
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
        <h1 class="text-center mb-4 text-rojo">Crear Cuenta</h1>
        <p class="text-center text-azul mb-4">Unite a AIMA y guardá tus direcciones y pedidos.</p>
        
        <div class="alert mb-4 text-center shadow-sm">
            <p class="mb-0">
                <i class="fa fa-info-circle me-1"></i> Nota: Los datos ingresados aquí (Teléfono y Dirección) serán los utilizados por defecto para tus futuros pedidos y envíos. Asegúrate de que sean correctos.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger p-2 text-center shadow-sm"><i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($msj): ?>
            <div class="alert alert-success p-2 text-center shadow-sm">
                <i class="fa fa-check-circle"></i> <?php echo $msj; ?>
                <div class="mt-2"><a href="login.php" class="btn btn-sm btn-aima">Ir a Iniciar Sesión</a></div>
            </div>
        <?php else: ?>

        <form method="post" action="registro.php">
            <div class="mb-3">
                <label class="form-label-aima">Nombre y Apellido <span class="text-rojo">*</span></label>
                <input type="text" name="nombre" class="form-control-aima" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label-aima">Celular de Contacto <span class="text-rojo">*</span></label>
                <input type="tel" name="celular" class="form-control-aima" placeholder="Ej: 11 1234 5678" required>
            </div>

            <div class="mb-3">
                <label class="form-label-aima">Correo Electrónico <span class="text-rojo">*</span></label>
                <input type="email" name="email" class="form-control-aima" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label-aima">Contraseña <span class="text-rojo">*</span></label>
                    <input type="password" name="password" class="form-control-aima" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label-aima">Dirección</label>
                    <input type="text" name="direccion" class="form-control-aima" placeholder="Opcional">
                </div>
            </div>

            <button type="submit" class="btn w-100 py-3 text-white">
                Completar Registro
            </button>
            <div class="text-center mt-3">
                <a href="login.php" class="text-muted">¿Ya tienes cuenta? Ingresa aquí.</a>
            </div>
        </form>

        <?php endif; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
