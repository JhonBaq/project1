<?php
require __DIR__ . '/core/connection.php'; // Conexión a la base de datos

// Iniciar sesión para guardar los datos del usuario
session_start();
$name_app = "Tickets - ABC";

// Comprobamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperamos el email y la contraseña del formulario
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Comprobamos si el email y la contraseña no están vacíos
    if (!empty($email) && !empty($password)) {
        // Preparamos la consulta para verificar si existe el agente con ese email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verificamos si el usuario existe y si la contraseña es correcta
        if ($user && password_verify($password, $user['password'])) {
            // Guardamos los datos del usuario en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre_completo'] = $user['nombre_completo'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['imagen'] = $user['imagen'];

            // Redirigir a la página principal (o dashboard) después de un inicio de sesión exitoso
            header("Location: views/view_users.php");
            exit;
        } else {
            // Si la contraseña o el email no coinciden, mostramos un mensaje de error
            $error_message = "El correo electrónico o la contraseña son incorrectos.";
        }
    } else {
        $error_message = "Por favor ingresa tu correo electrónico y contraseña.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets - ABC</title>
    <link rel="shortcut icon" href="assets/img/icon_logo.png" />
    <link rel="stylesheet" href="assets/compiled/css/app.css">
    <link rel="stylesheet" href="assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="assets/compiled/css/auth.css">
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo d-flex align-items-center">
                        <a href="index.html" class="d-flex align-items-center text-decoration-none">
                            <img src="assets/img/icon_logo.png" alt="Logo" class="me-3" style="height: 100px;">
                            <span class="fs-1 fw-bold text-primary"><?php echo isset($name_app) ? $name_app : ""; ?></span>
                        </a>
                    </div>

                    <h1 class="auth-title">Bienvenid@</h1>
                    <p class="auth-subtitle mb-5">¡Todo listo para empezar! Inicia sesión y comienza a trabajar.</p>

                    <!-- Si hay un mensaje de error, lo mostramos aquí -->
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" name="email" id="email" placeholder="Email" required>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" name="password" id="password" placeholder="Contraseña" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Ingresar</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                </div>
            </div>
        </div>
    </div>
</body>

</html>