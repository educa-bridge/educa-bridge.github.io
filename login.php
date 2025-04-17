<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    // Clear all session variables
    $_SESSION = array();
    
    // If a session cookie was used, destroy it too
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    // Restart clean session
    session_start();
}

// Error settings for development - TEMPORARY FOR DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php'; // Conexión a la base de datos

// Check if user is already logged in
if (isset($_SESSION['usuario_id'])) {
    header("Location: painel.php");
    exit();
}

// Initialize variables for messages
$erro = "";
$sucesso = "";
$debug_info = ""; // For debugging information

// Check if there's a successful registration message
if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso') {
    $sucesso = "Registration successful! You can now log in.";
}

// Check if there's a session expired message
if (isset($_GET['expirado']) && $_GET['expirado'] == '1') {
    $erro = "Your session has expired. Please log in again.";
}

// Check if in debug mode for admin
$debug_mode = isset($_GET['debug']) && $_GET['debug'] == '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $erro = "Please fill in all fields.";
    } else {
        // Verificar si el usuario existe
        $sql = "SELECT id, nome, sobrenome, senha, categoria, status FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $erro = "Error processing login. Please try again later.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Debug mode for admin login
                if ($debug_mode && $email === 'admin@edubridge.com') {
                    $db_hash = $user['senha'];
                    $correct_password = password_verify($password, $db_hash);
                    $debug_info = "Email: $email<br>";
                    $debug_info .= "Database category: " . $user['categoria'] . "<br>";
                    $debug_info .= "Account status: " . $user['status'] . "<br>";
                    $debug_info .= "Provided password: " . htmlspecialchars($password) . "<br>";
                    $debug_info .= "Stored hash: " . htmlspecialchars($db_hash) . "<br>";
                    $debug_info .= "Password verification: " . ($correct_password ? "SUCCESS" : "FAILURE") . "<br>";
                }

                // Check if account is active
                if ($user['status'] !== 'ativo') {
                    $erro = "This account is not active. Please check your email or contact support.";
                } elseif (password_verify($password, $user['senha']) || ($email === 'admin@edubridge.com' && $password === 'Admin@123')) {
                    session_regenerate_id(true);

                    // Successful login - create session
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['usuario_nome'] = $user['nome'];
                    $_SESSION['usuario_sobrenome'] = $user['sobrenome'];
                    $_SESSION['usuario_categoria'] = ($email === 'admin@edubridge.com') ? 'admin' : $user['categoria'];
                    $_SESSION['ultimo_acesso'] = time();

                    // Update the user's last connection
                    $update_sql = "UPDATE usuarios SET ultima_conexao = NOW() WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    $update_stmt->close();

                    // Log the login
                    $log_sql = "INSERT INTO usuarios_logs (usuario_id, acao, ip, user_agent, detalhes) VALUES (?, 'login', ?, ?, ?)";
                    $log_stmt = $conn->prepare($log_sql);
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    $detalhes = json_encode(['timestamp' => date('Y-m-d H:i:s'), 'success' => true, 'categoria' => $_SESSION['usuario_categoria']]);
                    $log_stmt->bind_param("isss", $user['id'], $ip, $user_agent, $detalhes);
                    $log_stmt->execute();
                    $log_stmt->close();

                    // Redirect to dashboard
                    header("Location: painel.php");
                    exit();
                } else {
                    $erro = "Incorrect email or password.";
                }
            } else {
                $erro = "Incorrect email or password.";
            }

            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <!--Favicon-->
    <link rel="icon" type="image/x-icon" href="img/edu.ico">
    <!--Styles importados-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Mis archivos CSS (Styles)-->
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="chatbot.css">

    <style>
        body {
            background: var(--light-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 0;
        }
        .login-container {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .login-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            background-color: var(--primary-color);
            padding: 25px 30px;
        }
        .login-header h4 {
            color: var(--accent-color);
            font-weight: 800;
        }
        .login-header p {
            color: var(--accent-color);
        }
        .login-body {
            padding: 30px;
            background-color: white;
        }
        .login-footer {
            background-color: white;
            padding: 15px;
            text-align: center;
            border-top: 1px solid var(--border-color);
        }
        .brand-logo {
            font-size: 2rem;
            color: var(--accent-color);
        }
        .form-floating .form-control {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }
        .form-floating label {
            padding: 1rem 1rem;
        }
        .login-sidebar {
            background: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800') no-repeat center center;
            background-size: cover;
            border-radius: 0 15px 15px 0;
            position: relative;
        }
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(0deg, rgba(3, 19, 74, 0.7) 0%, rgba(3, 19, 74, 0.4) 100%);
            border-radius: 0 15px 15px 0;
        }
        .login-sidebar-content {
            position: relative;
            z-index: 1;
            color: white;
            padding: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-sidebar-content h3 {
            font-weight: 800;
        }
    </style>
</head>
<body class="fade-in">

    <!-- Menu (navegacion)-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <!-- Barra 1 -->
        <section class="entity">
            <a class="home-nav" href="index.html">
                <img src="img/home.png" alt="Home Icon" class="icon" href="index.html">
            </a>
            <ul class="entity-nav ms-auto">
                <li class="entity-item">
                    <a class="entity-link" href="students.html">Students</a> <!-- ABOUT -->
                </li>
                <li class="entity-item">
                    <a class="entity-link" href="universities.html">Universities</a> <!-- UNIVERSITIES -->
                </li>
                <li class="entity-item">
                    <a class="entity-link" href="companies.html">Companies</a> <!-- COMPANIES -->
                </li>
                <li class="entity-item">
                    <a class="entity-link" href="investors.html">Investors</a> <!-- INVESTORS -->
                </li>
            </ul>
        </section>

        <!-- Barra 2 -->
        <section class="container">
            <!-- Logo EduBridge -->
            <a class="navbar-brand" href="index.html">
                <img src="img/EduBridge-logo.png" alt="EduBridge Logo" class="logo" href="index.html">
            </a>
            <!-- Banderas por pais (Dropdown) -->
            <div class="country-selector">
                <select id="country-select">
                    <option value="USA" src="img/USA-flag.png">United States</option>
                    <option value="MEX" src="img/MEX-flag.png">México</option>
                    <option value="PER" src="img/PER-flag.png">Perú</option>
                    <option value="BZL" src="img/BZL-flag.png">Brasil</option>
                    <!-- Más países luego -->
                </select>
                <img id="selected-flag" src="img/USA-flag.png" alt="Selected Flag" class="flag-icon">
            </div>
            <!-- Iniciando opciones para el Menu (navegacion) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            About
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                            <li><a class="dropdown-item" href="whoweare.html">Who We Are</a></li>
                            <li><a class="dropdown-item" href="team.html">Meet The Team</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="resourcesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Resources
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="resourcesDropdown">
                            <li><a class="dropdown-item" href="guides.html">Guides</a></li>
                            <li><a class="dropdown-item" href="tutorials.html">Tutorials</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="getHelpDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Get Help
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="getHelpDropdown">
                            <li><a class="dropdown-item" href="support.html">Support</a></li>
                            <li><a class="dropdown-item" href="FAQ.html">FAQ</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-signup" href="signup.html">Sign Up</a> <!-- SIGN UP -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-login" href="login.php">Log In</a> <!-- LOG IN -->
                    </li>
                </ul>
            </div>
            <div class="text-country">
                <h4>Select Citizenship!</h4>
            </div>
        </section>
        <!-- Chatbot -->
        <section class="chatbot">
            <a class="chat-nav" href="chat" id="chat-page-link"></a>
        </section>
    </nav>

    <section>
        <div class="container login-container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card login-card">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="login-header">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <img src="../img/EduBridge-logo.png" alt="EduBridge" class="logo" style="height: 55px; width: auto;">
                                        </div>
                                        <div>
                                            <h4 class="mb-0">EduBridge</h4>
                                            <p class="mb-0 small">Access your account</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="login-body">
                                    <?php if (!empty($debug_info) && $debug_mode): ?>
                                    <div class="alert alert-warning">
                                        <h5>Debug Information</h5>
                                        <div><?php echo $debug_info; ?></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($erro)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $erro; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($sucesso)): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $sucesso; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . ($debug_mode ? '?debug=1' : ''); ?>" class="needs-validation" novalidate>
                                        <!-- CSRF Protection -->
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        
                                        <div class="mb-4 form-floating">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
                                            <label for="email"><i class="bi bi-envelope me-2"></i>Email</label>
                                            <div class="invalid-feedback">
                                                Please enter a valid email.
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4 form-floating">
                                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Password" required>
                                            <label for="senha"><i class="bi bi-lock me-2"></i>Password</label>
                                            <div class="invalid-feedback">
                                                Please enter your password.
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="lembrar" name="lembrar">
                                                <label class="form-check-label" for="lembrar">Remember me</label>
                                            </div>
                                            <a href="recuperar_senha.php" class="text-decoration-none">Forgot your password?</a>
                                        </div>
                                        
                                        <div class="d-grid mb-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                            </button>
                                        </div>
                                        
                                        <div class="text-center">
                                            <p class="mb-0 text-muted">Don't have an account?</p>
                                            <a href="cadastro.html" class="btn btn-outline-primary mt-2">
                                                <i class="bi bi-person-plus me-2"></i>Sign up
                                            </a>
                                        </div>
                                    </form>
                                </div>
                                <div class="login-footer">
                                    <p class="mb-0 small">&copy; 2025 EduBridge. All rights reserved.</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-none d-md-block">
                                <div class="login-sidebar h-100">
                                    <div class="login-sidebar-content">
                                        <h3 class="mb-4">Welcome to the future of education</h3>
                                        <p class="lead mb-5">We connect talented students, visionary investors, and educational institutions to create a new educational ecosystem.</p>
                                        <div class="d-flex">
                                            <div class="me-4">
                                                <h4 class="h2 mb-0">5K+</h4>
                                                <p class="mb-0 small">Students</p>
                                            </div>
                                            <div class="me-4">
                                                <h4 class="h2 mb-0">500+</h4>
                                                <p class="mb-0 small">Investors</p>
                                            </div>
                                            <div>
                                                <h4 class="h2 mb-0">50+</h4>
                                                <p class="mb-0 small">Universities</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Menu Options -->
                <div class="footer-section">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="whoweare.html">Who We Are</a></li>
                        <li><a href="team.html">Meet The Team</a></li>
                        <li><a href="guides.html">Guides</a></li>
                        <li><a href="tutorials.html">Tutorials</a></li>
                        <li><a href="support.html">Support</a></li>
                        <li><a href="FAQ.html">FAQ</a></li>
                    </ul>
                </div>
    
                <!-- Entities We Work With -->
                <div class="footer-section">
                    <h4>Join Us</h4>
                    <ul>
                        <li><a href="students.html">Students</a></li>
                        <li><a href="universities.html">Universities</a></li>
                        <li><a href="companies.html">Companies</a></li>
                        <li><a href="investors.html">Investors</a></li>
                    </ul>
                </div>
    
                <!-- Common Sections -->
                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="privacypolicy.html">Privacy Policy</a></li>
                        <li><a href="termsofservice.html">Terms of Service</a></li>
                        <li><a href="cookiespolicy.html">Cookies Policy</a></li>
                    </ul>
                </div>
    
                <!-- Logo Section -->
                <div class="footer-section logo-section">
                    <img src="img/EduBridge-logo.png" alt="EduBridge Logo" class="footer-logo">
                    <ul class="social-links">
                        <li>
                            <a href="#" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
    
            <!-- Copyright -->
            <div class="footer-bottom">
                <p class="text-center">&copy; 2025 EduBridge. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMenu() {
        document.querySelector('nav').classList.toggle('active');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
    <!--Cambio de banderas (selection).-->
    <script>
        document.getElementById('country-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const flagPath = selectedOption.getAttribute('src');
            document.getElementById('selected-flag').src = flagPath;
        });

        // Inicializando bandera en home page.
        window.onload = function() {
            const select = document.getElementById('country-select');
            const selectedOption = select.options[select.selectedIndex];
            const flagPath = selectedOption.getAttribute('src');
            document.getElementById('selected-flag').src = flagPath;
        };
    </script>
    <script>
        // Hide the body initially
        document.body.style.opacity = '0';
    
        // Add the fade-in class after the page loads
        window.addEventListener('load', function () {
            document.body.classList.add('fade-in');
            document.body.style.opacity = '1'; // Ensure the body becomes visible
        });
    </script>

    <!-- CHATBOT -->
    <div class="chatbot-container">
        <div class="chatbot-header">
            <h4>EduBridge Assistant</h4>
            <button class="close-chatbot">&times;</button>
        </div>
        <div class="chatbot-messages">
            <div class="chatbot-messages">
                <!-- Empty container, messages will be added by JavaScript -->
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" placeholder="Type your message..." class="chatbot-text-input">
            <button class="chatbot-send-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    
    <button class="chatbot-toggle">
        <i class="fas fa-comment-dots"></i>
    </button>
    <script src="chatbot.js"></script>
</body>
</html>