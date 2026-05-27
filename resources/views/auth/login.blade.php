<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RFK Provinsi Maluku Utara</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variabel warna dan font */
        :root {
            --dark-blue: #31326F;
            --teal: #4FB7B3;
            --light-blue: #9DDFD3;
            --light: #FFFFFF;
            --gradient: linear-gradient(135deg, var(--dark-blue), var(--teal));
            --gradient-light: linear-gradient(135deg, var(--teal), var(--light-blue));
            --font-primary: 'Poppins', sans-serif;
        }

        /* Reset dan styling dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            background: var(--gradient);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
            color: var(--light);
        }

        /* Animasi background dengan partikel */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-20px) translateX(20px);
            }
        }

        /* Siluet gunung Gamalama */
        .mountain-silhouette {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 150px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath fill='rgba(0,0,0,0.1)' d='M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            z-index: 1;
        }

        /* Header styling */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            font-weight: 700;
            color: var(--light);
            font-size: 1.2rem;
        }

        .logo-img {
            height: 50px;
            margin-right: 12px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 2rem;
        }

        nav ul li a {
            text-decoration: none;
            color: var(--light);
            font-weight: 500;
            transition: all 0.3s;
            padding: 8px 12px;
            border-radius: 20px;
        }

        nav ul li a:hover {
            color: var(--dark-blue);
            background-color: var(--light);
        }

        /* Konten utama */
        .main-content {
            display: flex;
            flex: 1;
            padding: 2rem 5%;
            position: relative;
            z-index: 2;
        }

        /* Bagian kiri - Hero text */
        .hero-section {
            flex: 1;
            padding-right: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: var(--light);
        }

        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-subtitle {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
            max-width: 90%;
        }

        /* Bagian kanan - Form login */
        .login-section {
            flex: 0 0 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 1.8rem;
            text-align: center;
        }
         .error-title {
            width: 100%;
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border: 1px solid #feb2b2;
            border-radius: 8px;
            color: #742a2a;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--teal);
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e6e6e6;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--teal);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 183, 179, 0.3);
        }

        .input-group input:focus + i {
            color: var(--dark-blue);
            animation: bounce 0.5s;
        }

        /* Tombol show password */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0a0a0;
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.3s;
            z-index: 2;
        }

        .toggle-password:hover {
            color: var(--teal);
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.2); }
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: var(--gradient);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(49, 50, 111, 0.4);
        }

        .login-btn:hover {
            box-shadow: 0 8px 25px rgba(49, 50, 111, 0.6);
            transform: translateY(-2px);
            background: var(--gradient-light);
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--teal);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: var(--dark-blue);
            text-decoration: underline;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--light);
            font-size: 0.9rem;
            position: relative;
            z-index: 2;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        /* Responsivitas */
        @media (max-width: 992px) {
            .main-content {
                flex-direction: column;
            }

            .hero-section {
                padding-right: 0;
                margin-bottom: 3rem;
                text-align: center;
            }

            .hero-subtitle {
                max-width: 100%;
            }

            .login-section {
                flex: 0 0 auto;
                width: 100%;
                max-width: 400px;
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
            }

            .logo {
                margin-bottom: 1rem;
            }

            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }

            nav ul li {
                margin: 0 0.5rem;
            }

            .hero-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            nav ul {
                flex-wrap: wrap;
            }

            nav ul li {
                margin: 0.5rem;
            }

            .logo-img {
                height: 40px;
            }
        }
        /* Modern Card Style Error Messages */
    </style>
</head>
<body>
    <!-- Background particles -->
    <div class="particles" id="particles"></div>

    <!-- Siluet gunung Gamalama -->
    <div class="mountain-silhouette"></div>

    <!-- Header -->
    <header>
        <div class="logo">
            <img src="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png" alt="Logo Pemerintah Provinsi Maluku Utara" class="logo-img">
            <span>Pemerintah Provinsi Maluku Utara</span>
        </div>
        <nav>
            <ul>
                <li><a href="#">Kegiatan</a></li>
                <li><a href="#">Galeri</a></li>
                <li><a href="#">Panduan</a></li>
            </ul>
        </nav>
    </header>

    <!-- Konten utama -->
    <div class="main-content">
        <!-- Bagian kiri - Hero text -->
        <section class="hero-section">
            <h1 class="hero-title">RFK (Realisasi Fisik Dan Keuangan)</h1>
            <p class="hero-subtitle">Digitalisasi Monitoring Realisasi Fisik Dan Keuangan Sebagai Upaya Peningkatan Kualitas Pelaporan Pembangunan .</p>
        </section>

        <!-- Bagian kanan - Form login -->
        <section class="login-section">
            <div class="login-card">
                <h2 class="login-title">Login ke RFK</h2>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    @error('email')
                    <div class="error-title">{{ $message }}
                    </div>
                    @enderror
                    <br>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="Email" name="email" required class="form-control @error('email') is-invailid @enderror" value="{{ old('email') }}">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="Password" name="password" id="password" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit" class="login-btn">Masuk</button>
                    <a href="#" class="forgot-password">Lupa password?</a>
                </form>
            </div>
        </section>
    </div>

    <footer>
        &copy; 2026 Pemerintah Provinsi Maluku Utara. All rights reserved.Versi 1.0
    </footer>

    <script>
        // Script untuk membuat partikel animasi
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                // Ukuran dan posisi acak
                const size = Math.random() * 20 + 5;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const delay = Math.random() * 10;

                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.animationDelay = `${delay}s`;
                particle.style.opacity = Math.random() * 0.5 + 0.1;

                particlesContainer.appendChild(particle);
            }

            // Script untuk toggle show password
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle the eye icon
                const eyeIcon = this.querySelector('i');
                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                }
            });
        });
    </script>
</body>
</html>
