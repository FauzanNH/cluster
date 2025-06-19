<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bukit Asri Cluster</title>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <!-- Animated Background Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Bukit Asri" class="logo" onerror="this.src='https://via.placeholder.com/120x120?text=Bukit+Asri';this.onerror='';">
                <h1>Masuk ke Akun</h1>
                <p>Selamat datang di Bukit Asri Cluster</p>
            </div>
            
            @if ($errors->any())
                <div class="server-error" style="display: none;">{{ $errors->first() }}</div>
            @endif
            
            <form id="loginForm" action="{{ route('login.attempt', ['response_type' => request('response_type')]) }}" method="post" novalidate>
                @csrf
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                        <i id="togglePassword" class="fas fa-eye-slash" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #aaa;"></i>
                    </div>
                </div>
                
                <!-- reCAPTCHA -->
                <div class="g-recaptcha" data-sitekey="{{ $recaptcha_site_key }}"></div>
                <div id="recaptcha-error" class="error-message" style="display:none;"></div>
                
                @if ($errors->has('captcha'))
                    <div class="server-error" style="display: none;">{{ $errors->first('captcha') }}</div>
                @endif
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
            
            <div class="links">
                <p><a href="{{ route('welcome') }}"><i class="fas fa-home"></i> Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>
    
    <!-- Wave Decoration -->
    <div class="wave-container">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="0.2" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,202.7C672,203,768,181,864,181.3C960,181,1056,203,1152,197.3C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
    
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>