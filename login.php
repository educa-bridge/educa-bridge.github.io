<?php
session_start();
include 'config.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Verificar si el usuario existe
    $sql = "SELECT id, nome, categoria, password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Guardar datos en la sesión
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nome'];
            $_SESSION['usuario_categoria'] = $user['categoria'];

            // Redirigir al panel correspondiente
            switch ($user['categoria']) {
                case 'estudante':
                    header("Location: painel_estudante.php");
                    break;
                case 'investidor':
                    header("Location: painel_investidor.php");
                    break;
                case 'universidade':
                    header("Location: painel_universidade.php");
                    break;
                case 'empresa':
                    header("Location: painel_empresa.php");
                    break;
                case 'admin':
                    header("Location: painel_admin.php");
                    break;
                default:
                    header("Location: login.php?error=categoria_invalida");
                    break;
            }
            exit();
        } else {
            // Contraseña incorrecta
            $_SESSION['login_error'] = "Contraseña incorrecta.";
        }
    } else {
        // Usuario no encontrado
        $_SESSION['login_error'] = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBridge - Login</title>
    <link rel="icon" type="image/x-icon" href="img/edu.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="login.css">
</head>

<body class="fade-in">
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" href="index.html">
            <img src="img/EduBridge-logo.png" alt="EduBridge Logo" class="logo">
        </a>
    </nav>

    <section class="container d-flex justify-content-center align-items-center" style="min-height: 90vh; margin-top: 6rem;">
        <div class="form-box active" id="loginBox">
            <h2>Iniciar sesión</h2>
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="error-message">
                    <?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Iniciar sesión</button>
            </form>
            <p>¿No tienes cuenta? <a href="signup.html">Regístrate</a></p>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p class="text-center">&copy; 2025 EduBridge. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>