<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= NOMBRE_HOSTAL ?? 'HOSTAL el DULCE DESCANSO' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/style.css">
    
    <?php
    $req = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($req, '/cliente/') !== false) {
        echo '<link rel="stylesheet" href="' . URL_BASE . 'assets/css/cliente.css">';
        echo '<script>window.URL_BASE = "' . URL_BASE . '";</script>';
        echo '<script src="' . URL_BASE . 'assets/js/cliente-cart.js"></script>';
    }
    ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #ec4899;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Header Glassmorphism */
        header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.5s ease-out;
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-shrink: 0;
        }

        .logo i {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            white-space: nowrap;
        }

        /* Navigation */
        nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        nav a {
            color: var(--dark);
            text-decoration: none;
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        nav a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        nav a:hover::before {
            width: 80%;
        }

        nav a:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }

        /* Auth Links */
        .auth-links {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .auth-links a {
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 2px solid transparent;
        }

        .btn-login {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-login:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.4);
        }

        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .user-name {
            color: var(--primary);
            font-weight: 600;
            padding: 0 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-logout {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-logout::before {
            display: none;
        }

        /* Panel Links */
        .btn-panel {
            background: rgba(139, 92, 246, 0.1);
            color: var(--secondary);
            border: 2px solid var(--secondary);
        }

        .btn-panel:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        /* Cart Badge */
        .cart-badge {
            position: fixed;
            right: 2rem;
            top: 2rem;
            z-index: 999;
            animation: fadeInRight 0.5s ease-out;
        }

        @keyframes fadeInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .cart-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .cart-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            border-color: var(--primary);
        }

        .cart-icon {
            font-size: 1.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .cart-count {
            background: linear-gradient(135deg, var(--accent), #f472b6);
            color: white;
            padding: 0.3rem 0.7rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            min-width: 2rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(236, 72, 153, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Main Container */
        main.container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .header-content {
                padding: 1.2rem 1.5rem;
                gap: 1.5rem;
            }

            nav {
                gap: 0.3rem;
            }

            nav a {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-wrap: wrap;
                padding: 1rem 1.5rem;
                gap: 1rem;
            }

            .logo h1 {
                font-size: 1.5rem;
            }

            nav {
                width: 100%;
                justify-content: center;
                gap: 0.4rem;
            }

            nav a {
                flex: 0 1 auto;
                padding: 0.6rem 0.9rem;
                font-size: 0.85rem;
            }

            .auth-links {
                width: 100%;
                gap: 0.5rem;
            }

            .auth-links a {
                flex: 1;
                justify-content: center;
                min-width: 110px;
            }

            .user-menu {
                width: 100%;
                justify-content: center;
            }

            .user-name {
                font-size: 0.9rem;
            }

            .cart-badge {
                right: 1.5rem;
                top: 1.5rem;
            }

            .cart-link {
                padding: 0.7rem 1.2rem;
            }

            main.container {
                padding: 0 1rem;
                margin: 1rem auto;
            }
        }

        @media (max-width: 480px) {
            .header-content {
                padding: 1rem;
                gap: 0.8rem;
            }

            .logo h1 {
                font-size: 1.3rem;
            }

            nav a {
                padding: 0.5rem 0.7rem;
                font-size: 0.8rem;
            }

            .auth-links a {
                padding: 0.6rem 0.8rem;
                font-size: 0.8rem;
            }

            .cart-badge {
                right: 1rem;
                top: 1rem;
            }

            .cart-link {
                padding: 0.6rem 1rem;
            }

            .cart-icon {
                font-size: 1.2rem;
            }

            main.container {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-content">
        <div class="logo">
            <i class="fas fa-hotel"></i>
            <h1><?= NOMBRE_HOSTAL ?? 'Hostal Estrella' ?></h1>
        </div>

        <nav>
            <a href="<?= URL_BASE ?>index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="<?= URL_BASE ?>nosotros.php"><i class="fas fa-info-circle"></i> Nosotros</a>
            <a href="<?= URL_BASE ?>contacto.php"><i class="fas fa-envelope"></i> Contacto</a>

            <?php if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['id_usuario'])): ?>
                <?php 
                    $rol = strtolower($_SESSION['usuario']['rol'] ?? '');
                    $nombre = $_SESSION['usuario']['nombres'] ?? 'Usuario';
                ?>
                
                <?php if ($rol === 'cliente'): ?>
                    <a href="<?= URL_BASE ?>cliente/index.php" class="btn-panel"><i class="fas fa-user"></i> Mi Cuenta</a>
                <?php elseif ($rol === 'recepcionista'): ?>
                    <a href="<?= URL_BASE ?>recepcionista/index.php" class="btn-panel"><i class="fas fa-clipboard"></i> Recepción</a>
                <?php elseif ($rol === 'administrador' || $rol === 'admin'): ?>
                    <a href="<?= URL_BASE ?>admin/index.php" class="btn-panel"><i class="fas fa-chart-line"></i> Admin</a>
                <?php endif; ?>

                <a href="<?= URL_BASE ?>logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
            <?php else: ?>
                <div class="auth-links">
                    <a href="<?= URL_BASE ?>login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                    <a href="<?= URL_BASE ?>registro.php" class="btn-register"><i class="fas fa-user-plus"></i> Registrarse</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if (isset($_SESSION['usuario']) && strtolower($_SESSION['usuario']['rol'] ?? '') === 'cliente'): ?>
<?php endif; ?>

<main class="container">