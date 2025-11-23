<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - Hostal Estrella</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --accent: #ec4899;
    --dark: #0f172a;
    --light: #f8fafc;
    --gray: #64748b;
}

body, html {
    height: 100%;
    scroll-behavior: smooth;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow-x: hidden;
}

/* Animated Background */
.bg-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
}

.bg-animation span {
    position: absolute;
    display: block;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.1);
    animation: float 15s infinite;
    border-radius: 50%;
}

.bg-animation span:nth-child(1) { left: 10%; animation-delay: 0s; }
.bg-animation span:nth-child(2) { left: 20%; animation-delay: 2s; width: 30px; height: 30px; }
.bg-animation span:nth-child(3) { left: 30%; animation-delay: 4s; }
.bg-animation span:nth-child(4) { left: 40%; animation-delay: 6s; width: 25px; height: 25px; }
.bg-animation span:nth-child(5) { left: 50%; animation-delay: 8s; }
.bg-animation span:nth-child(6) { left: 60%; animation-delay: 10s; width: 35px; height: 35px; }
.bg-animation span:nth-child(7) { left: 70%; animation-delay: 12s; }
.bg-animation span:nth-child(8) { left: 80%; animation-delay: 14s; width: 28px; height: 28px; }

@keyframes float {
    0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
}

/* Header Moderno */
header {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.header-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.logo i {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.logo h1 {
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

nav {
    display: flex;
    gap: 0.5rem;
}

nav a {
    color: var(--dark);
    text-decoration: none;
    padding: 0.7rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

nav a::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--primary);
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

/* Main Content */
main {
    position: relative;
    z-index: 10;
    max-width: 800px;
    margin: 3rem auto;
    padding: 0 2rem;
}

@keyframes fadeInUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Card Glassmorphism */
.card-glass {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    padding: 3rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.5);
    animation: fadeInUp 0.8s ease-out;
}

.card-glass h2 {
    font-size: 2.2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.subtitle {
    color: var(--gray);
    margin-bottom: 2rem;
    font-size: 1rem;
}

/* Form Styles */
.registration-form {
    display: grid;
    gap: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    color: var(--dark);
    font-weight: 600;
    margin-bottom: 0.8rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group label i {
    color: var(--primary);
    font-size: 1rem;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i.input-icon {
    position: absolute;
    left: 1.2rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 1.1rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid #e2e8f0;
    border-radius: 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    font-family: 'Poppins', sans-serif;
}

.form-group select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

/* Password strength indicator */
.password-strength {
    margin-top: 0.5rem;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
    display: none;
}

.password-strength-bar {
    height: 100%;
    transition: all 0.3s ease;
    width: 0%;
}

.password-strength.weak .password-strength-bar {
    width: 33%;
    background: #ef4444;
}

.password-strength.medium .password-strength-bar {
    width: 66%;
    background: #f59e0b;
}

.password-strength.strong .password-strength-bar {
    width: 100%;
    background: #10b981;
}

/* Info box */
.info-box {
    display: flex;
    gap: 1rem;
    padding: 1.2rem;
    background: rgba(99, 102, 241, 0.05);
    border-left: 4px solid var(--primary);
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.info-box i {
    color: var(--primary);
    font-size: 1.5rem;
    flex-shrink: 0;
}

.info-box p {
    color: var(--gray);
    font-size: 0.9rem;
    line-height: 1.6;
}

/* Submit Button */
.btn-submit {
    width: 100%;
    padding: 1.2rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
    margin-top: 1rem;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
}

.btn-submit:active {
    transform: translateY(-1px);
}

/* Login Link */
.login-link {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid #e2e8f0;
    color: var(--gray);
}

.login-link a {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.login-link a:hover {
    color: var(--secondary);
    text-decoration: underline;
}

/* Footer */
footer {
    background: var(--dark);
    color: white;
    text-align: center;
    padding: 2rem;
    margin-top: 4rem;
    position: relative;
    z-index: 10;
}

footer p {
    opacity: 0.9;
    font-size: 0.95rem;
}

/* Responsive */
@media (max-width: 968px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    main {
        padding: 0 1rem;
    }
    
    .card-glass {
        padding: 2rem 1.5rem;
    }
    
    .card-glass h2 {
        font-size: 1.8rem;
    }
}
/* Fondo de pantalla */
body {
    background: url('assets/img/fondo.png') no-repeat center center fixed;
    background-size: cover;
    position: relative;
}
</style>
</head>
<body>

<div class="bg-animation">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
</div>

<header>
    <div class="header-content">
        <div class="logo">
            <i class="fas fa-hotel"></i>
            <h1>Hostal Estrella</h1>
        </div>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="nosotros.php"><i class="fas fa-info-circle"></i> Nosotros</a>
            <a href="contacto.php"><i class="fas fa-envelope"></i> Contacto</a>
            <a href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a>
        </nav>
    </div>
</header>

<main>
    <div class="card-glass">
        <h2>
            <i class="fas fa-user-plus"></i>
            Crear Cuenta
        </h2>
        <p class="subtitle">Completa el formulario para registrarte como nuevo cliente</p>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>Al registrarte, podrás realizar reservas online, ver tu historial de estadías y acceder a promociones exclusivas.</p>
        </div>

        <form method="POST" class="registration-form" id="registrationForm">
            <!-- Nombres y Apellidos -->
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nombres</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="nombres" placeholder="Ingresa tus nombres" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-user"></i> Apellidos</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="apellidos" placeholder="Ingresa tus apellidos" required>
                    </div>
                </div>
            </div>

            <!-- Tipo de Documento y Número -->
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> Tipo de Documento</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-card input-icon"></i>
                        <select name="tipo_doc" required>
                            <option value="">Selecciona...</option>
                            <option value="DNI">DNI</option>
                            <option value="CE">Carné de Extranjería</option>
                            <option value="pasaporte">Pasaporte</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> Número de Documento</label>
                    <div class="input-wrapper">
                        <i class="fas fa-hashtag input-icon"></i>
                        <input type="text" name="num_doc" placeholder="Ej: 12345678" required>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" placeholder="tu@email.com" required>
                </div>
            </div>

            <!-- Usuario -->
            <div class="form-group">
                <label><i class="fas fa-at"></i> Nombre de Usuario</label>
                <div class="input-wrapper">
                    <i class="fas fa-at input-icon"></i>
                    <input type="text" name="username" placeholder="Elige un nombre de usuario" required>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" placeholder="Crea una contraseña segura" required>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="password-strength-bar"></div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i>
                Crear Cuenta
            </button>
        </form>

        <div class="login-link">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 Hostal Estrella. Todos los derechos reservados.</p>
</footer>

<script>
// Password strength indicator
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthIndicator = document.getElementById('passwordStrength');
    
    if (password.length === 0) {
        strengthIndicator.style.display = 'none';
        return;
    }
    
    strengthIndicator.style.display = 'block';
    strengthIndicator.className = 'password-strength';
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    if (strength <= 2) {
        strengthIndicator.classList.add('weak');
    } else if (strength === 3) {
        strengthIndicator.classList.add('medium');
    } else {
        strengthIndicator.classList.add('strong');
    }
});

// Form validation
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    
    if (password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
});
</script>

</body>
</html>