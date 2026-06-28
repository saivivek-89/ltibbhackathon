<?php
require_once 'config.php';

$signup_error = '';
$login_error = '';
$signup_success = '';
$login_success = '';

// ===== HANDLE SIGNUP =====
if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    // Validation
    if (empty($username) || empty($name) || empty($email) || empty($password)) {
        $signup_error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $signup_error = "Passwords do not match!";
    } elseif (strlen($password) < 4) {
        $signup_error = "Password must be at least 4 characters!";
    } else {
        // Check if username already exists
        $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            $signup_error = "Username or email already exists!";
        } else {
            // Insert new user
            $insert_sql = "INSERT INTO users (username, name, email, password) 
                           VALUES ('$username', '$name', '$email', '$password')";
            
            if ($conn->query($insert_sql) === TRUE) {
                $signup_success = "Account created successfully! Please login.";
            } else {
                $signup_error = "Error: " . $conn->error;
            }
        }
    }
}

// ===== HANDLE LOGIN =====
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['login_username']);
    $password = mysqli_real_escape_string($conn, $_POST['login_password']);
    
    if (empty($username) || empty($password)) {
        $login_error = "Please enter both username and password!";
    } else {
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid username or password!";
        }
    }
}

$page_title = "Sign Up / Login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BloodConnect - Sign Up / Login</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <style>
        :root {
            --primary-red: #dc3545;
            --primary-red-dark: #b02a37;
            --primary-red-light: #f8d7da;
            --primary-dark: #1a1a2e;
            --shadow-md: 0 8px 30px rgba(0,0,0,0.12);
            --shadow-lg: 0 15px 50px rgba(220, 53, 69, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #fce4e4 100%);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 12px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.06);
            transition: var(--transition);
        }
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--primary-dark) !important;
        }
        .navbar-brand .brand-red { color: var(--primary-red); }
        .nav-link {
            font-weight: 500;
            color: #4a4a6a !important;
            padding: 8px 18px !important;
            border-radius: 8px;
            transition: var(--transition);
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary-red) !important;
            background: var(--primary-red-light);
        }
        .btn-donate-nav {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white !important;
            padding: 10px 28px !important;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-donate-nav:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white !important;
        }

        /* Auth Container */
        .auth-wrapper {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 24px;
            padding: 50px;
            box-shadow: var(--shadow-md);
            border: 1px solid #f0f0f0;
        }

        .auth-card {
            padding: 10px;
        }

        .auth-card .auth-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-red-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary-red);
            margin-bottom: 16px;
        }

        .auth-card h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 4px;
        }

        .auth-card p.subtitle {
            color: #6c6c8a;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .auth-card .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e8e8e8;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .auth-card .form-control:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.15);
        }

        .auth-card .form-label {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 0.9rem;
        }

        .auth-card .btn-primary-custom {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 14px;
            border-radius: 50px;
            font-weight: 600;
            width: 100%;
            border: none;
            transition: var(--transition);
            font-size: 1rem;
        }

        .auth-card .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .auth-card .btn-primary-custom:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .auth-divider {
            width: 1px;
            background: #e8e8e8;
            margin: 0 -20px;
        }

        .alert-custom {
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .password-hint {
            font-size: 0.8rem;
            color: #6c6c8a;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .auth-grid {
                grid-template-columns: 1fr;
                padding: 30px 20px;
                gap: 30px;
            }
            .auth-divider {
                width: 100%;
                height: 1px;
                margin: 0;
            }
            .auth-wrapper {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.html">
            <i class="bi bi-droplet-half brand-red"></i>
            Blood<span class="brand-red">Connect</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="donate-blood.php">Donate</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="find-donor.php">Find Donor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="blood-donation-facts.html">Facts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="blood-donation-chart.html">Chart</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-donate-nav" href="signup.php">
                        <i class="bi bi-person-plus me-1"></i> Sign Up / Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ===== AUTH WRAPPER ===== -->
<div class="auth-wrapper">
    <div class="auth-grid">
        
        <!-- ===== SIGNUP SECTION ===== -->
        <div class="auth-card">
            <div class="auth-icon"><i class="bi bi-person-plus"></i></div>
            <h3>Create Account</h3>
            <p class="subtitle">Join our blood donation community</p>
            
            <?php if ($signup_error): ?>
                <div class="alert alert-danger alert-custom">
                    <i class="bi bi-exclamation-circle me-2"></i> <?php echo $signup_error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($signup_success): ?>
                <div class="alert alert-success alert-custom">
                    <i class="bi bi-check-circle me-2"></i> <?php echo $signup_success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person me-1"></i> Full Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-badge me-1"></i> Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Choose a username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-envelope me-1"></i> Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-lock me-1"></i> Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Create a password" required minlength="4">
                    <div class="password-hint">Minimum 4 characters</div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-shield-lock me-1"></i> Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <button type="submit" name="signup" class="btn btn-primary-custom">
                    <i class="bi bi-person-plus me-2"></i> Sign Up
                </button>
            </form>
        </div>

        <!-- ===== DIVIDER ===== -->
        <div class="auth-divider"></div>

        <!-- ===== LOGIN SECTION ===== -->
        <div class="auth-card">
            <div class="auth-icon"><i class="bi bi-box-arrow-in-right"></i></div>
            <h3>Welcome Back</h3>
            <p class="subtitle">Login to access your account</p>
            
            <?php if ($login_error): ?>
                <div class="alert alert-danger alert-custom">
                    <i class="bi bi-exclamation-circle me-2"></i> <?php echo $login_error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-badge me-1"></i> Username</label>
                    <input type="text" class="form-control" name="login_username" placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-lock me-1"></i> Password</label>
                    <input type="password" class="form-control" name="login_password" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary-custom">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </button>
            </form>
            
            <p class="text-center mt-3" style="font-size: 0.9rem; color: #6c6c8a;">
                <i class="bi bi-info-circle me-1"></i> 
                Don't have an account? Use the <strong style="color: var(--primary-red);">Sign Up</strong> form.
            </p>
        </div>
        
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <a href="index.html" class="footer-brand">
                    <i class="bi bi-droplet-half" style="color: var(--primary-red);"></i>
                    Blood<span class="brand-red">Connect</span>
                </a>
                <p style="margin-top: 16px; max-width: 320px; color: rgba(255,255,255,0.5);">
                    Connecting blood donors with those in need. Every drop counts, every life matters.
                </p>
                <div class="footer-social">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6>Quick Links</h6>
                <a href="index.html">Home</a>
                <a href="donate-blood.php">Donate Blood</a>
                <a href="find-donor.php">Find Donor</a>
                <a href="signup.php">Sign Up</a>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6>Resources</h6>
                <a href="blood-donation-facts.html">Blood Facts</a>
                <a href="blood-donation-chart.html">Compatibility</a>
                <a href="bmi/index.html">BMI Calculator</a>
                <a href="weightconverter/index.html">Weight Converter</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6>Contact Us</h6>
                <p style="margin-bottom: 6px;"><i class="bi bi-envelope me-2" style="color: var(--primary-red);"></i> info@bloodconnect.org</p>
                <p style="margin-bottom: 6px;"><i class="bi bi-phone me-2" style="color: var(--primary-red);"></i> +1 (555) 123-4567</p>
                <p><i class="bi bi-geo-alt me-2" style="color: var(--primary-red);"></i> 123 Health Blvd, Medical City</p>
            </div>
        </div>
        <hr class="footer-divider" />
        <div class="footer-copy">
            &copy; 2025 <strong>BloodConnect</strong>. All rights reserved. Made with <i class="bi bi-heart-fill" style="color: var(--primary-red);"></i> for humanity.
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const nav = document.getElementById('mainNav');
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>

</body>
</html>
