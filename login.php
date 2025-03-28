<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];

$activeForm = $_SESSION['active_form'] ?? 'login';


session_unset();

function showError($error){
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function is_ActiveForm($formName, $activeForm){
    return $formName === $activeForm ? 'active' : '';
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Titulo del Website -->
    <title>EduBridge</title>

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
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="header-footer.css">
</head>

<body>

    <!--Menu (navegacion)
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
         Barra 1 
        <section class="entity">
            <a class="home-nav" href="index.html">
                <img src="img/home.png" alt="Home Icon" class="icon" href="index.html">
            </a>
            <ul class="entity-nav ms-auto">
                <li class="entity-item">
                    <a class="entity-link" href="students.html">Students</a> 
                </li>
                <li class="entity-item">
                    <a class="entity-link" href="universities.html">Universities</a> 
                </li>
                <li class="entity-item">
                    <a class="entity-link" href="companies.html">Companies</a> 
                </li>
                <li class="entity-item">
                    <a class="entity-link" href="investors.html">Investors</a>
                </li>
            </ul>
        </section>
    -->
        

        <!-- Barra 2 
        <section class="container">
            <!-- Logo EduBridge 
            <a class="navbar-brand" href="index.html">
                <img src="img/EduBridge-logo.png" alt="EduBridge Logo" class="logo" href="index.html">
            </a>
            <div class="country-selector">
                <select id="country-select">
                    <option value="USA" src="img/USA-flag.png">United States</option>
                    <option value="MEX" src="img/MEX-flag.png">México</option>
                    <option value="PER" src="img/PER-flag.png">Perú</option>
                    <option value="BZL" src="img/BZL-flag.png">Brasil</option>
                </select>
                <img id="selected-flag" src="img/USA-flag.png" alt="Selected Flag" class="flag-icon">
            </div>
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
                        <a class="nav-signup" href="signup.html">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-login" href="login.html">Log In</a> 
                </ul>
            </div>
            <div class="text-country">
                <h4>Select Citizenship!</h4>
            </div>
        </section>
        <!-- Chatbot 
        <section class="chatbot">
            <a class="chat-nav" href="chat">
                <img src="img/chat.png" alt="Chat Icon" class="chat" href="chat">
            </a>
         </section>
         
    </nav>
    -->
 <div class="container">
    <div class="form-box <?= is_ActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="login_register.php" method="post">
            <h2>Edubridge login</h2>
            <?= showError($errors['login']); ?>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="#" onclick="showForm('register-form')">register</a></p>
        </form>

    </div>
    <div class="form-box <?= is_ActiveForm('register', $activeForm); ?>" id="register-form">
        <form action="login_register.php" method="post">
            <h2>Edubridge Register</h2>
            <?= showError($errors['register']); ?>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="middle_name" placeholder="Middle name" required>
            <input type="text" name="last_name" placeholder="Last name" required>
            <input type="text" name="second_last_name" placeholder="Second last name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select type="role" required>
            <option value="">--Select Role--</option>
            <option value="user">Student</option>
            <option value="admin">Inversor</option>
            </select>

            <button type="submit">Register</button>
            <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
        </form>

    </div>
 </div>
    <!-- Footer 
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Menu Options 
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
    
                <!-- Entities We Work With 
                <div class="footer-section">
                    <h4>Join Us</h4>
                    <ul>
                        <li><a href="students.html">Students</a></li>
                        <li><a href="universities.html">Universities</a></li>
                        <li><a href="companies.html">Companies</a></li>
                        <li><a href="investors.html">Investors</a></li>
                    </ul>
                </div>
    
                <!-- Common Sections
                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="privacypolicy.html">Privacy Policy</a></li>
                        <li><a href="termsofservice.html">Terms of Service</a></li>
                        <li><a href="cookiespolicy.html">Cookies Policy</a></li>
                    </ul>
                </div>
    
                <!-- Logo Section 
                <div class="footer-section logo-section">
                    <img src="img/EduBridge-logo.png" alt="EduBridge Logo" class="footer-logo">
                    <ul class="social-links">
                        <li>
                            <a href="https://instagram.com/edubridge" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://linkedin.com/company/edubridge" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://youtube.com/edubridge" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
    
            <!-- Copyright 
            <div class="footer-bottom">
                <p class="text-center">&copy; 2025 EduBridge. All rights reserved.</p>
            </div>
        </div>
    </footer> -->


    <!-- JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!--JS extensions para carousel, animaciones, etc.-->
    <script>
        function toggleMenu() {
        document.querySelector('nav').classList.toggle('active');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    
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

</body>
</html>