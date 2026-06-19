<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sajili - Ukoo wa Nyahende</title>
    @if(config('services.google.analytics_id'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{ config('services.google.analytics_id') }}');
        </script>
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
        }

        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-100px) translateX(50px); }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .logo-icon i {
            font-size: 28px;
            color: white;
        }

        h1 {
            font-size: 26px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #718096;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            width: 20px;
            text-align: center;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            padding: 0;
            margin: 0;
            cursor: pointer;
            color: #a0aec0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle i {
            font-size: 16px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 48px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .invalid-feedback {
            color: #e53e3e;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .login-link-section {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .login-link-section p {
            color: #718096;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .btn-to-login {
            display: inline-block;
            padding: 12px 24px;
            background: #f7fafc;
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: 2px solid #667eea;
            transition: all 0.3s ease;
        }

        .btn-to-login:hover {
            background: #667eea;
            color: white;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .alert-danger {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #fc8181;
        }

        .alert-info {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            line-height: 1.4;
        }

        .alert-info i {
            margin-top: 2px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 12px;
            }
            .login-card {
                padding: 32px 20px;
                border-radius: 20px;
            }
            h1 {
                font-size: 22px;
            }
            .logo-icon {
                width: 56px;
                height: 56px;
                margin-bottom: 12px;
            }
            .logo-icon i {
                font-size: 24px;
            }
            input[type="text"],
            input[type="password"] {
                padding: 12px 42px 12px 42px;
                font-size: 15px;
            }
            .input-icon {
                left: 12px;
            }
            .password-toggle {
                right: 12px;
            }
            .btn-register {
                padding: 14px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle" style="width: 80px; height: 80px; left: 10%; top: 20%; animation-delay: 0s;"></div>
        <div class="particle" style="width: 60px; height: 60px; left: 80%; top: 30%; animation-delay: 2s;"></div>
        <div class="particle" style="width: 100px; height: 100px; left: 50%; top: 60%; animation-delay: 4s;"></div>
        <div class="particle" style="width: 40px; height: 40px; left: 20%; top: 70%; animation-delay: 1s;"></div>
        <div class="particle" style="width: 70px; height: 70px; left: 70%; top: 80%; animation-delay: 3s;"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Sajili Akaunti</h1>
                <p class="subtitle">Fungua akaunti ili kujiunga na mti wa ukoo</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Tafadhali weka taarifa sahihi.
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name field --}}
                <div class="form-group">
                    <label for="name">Jina la Mtumiaji (Jina kamili)</label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-user"></i>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus
                            placeholder="ingiza jina lako kamili"
                        >
                    </div>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password field --}}
                <div class="form-group">
                    <label for="password">Nenosiri (Password)</label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Ingiza nenosiri lako"
                        >
                        <button type="button" class="password-toggle" data-target="password" aria-label="Onyesha nenosiri">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password confirmation field --}}
                <div class="form-group">
                    <label for="password_confirmation">Thibitisha Nenosiri</label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            placeholder="Rudia nenosiri lako"
                        >
                        <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Onyesha nenosiri">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Info message --}}
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <div>
                        <small>Jina lako litatumika kama jina la kuingilia kwenye mfumo. Unaweza kusasisha maelezo ya wasifu wako baada ya usajili.</small>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Sajili Akaunti
                </button>
            </form>

            <div class="login-link-section">
                <p>Tayari una akaunti?</p>
                <a href="{{ route('login') }}" class="btn-to-login">
                    Ingia hapa
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.password-toggle').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = button.getAttribute('data-target');
                    var input = document.getElementById(targetId);
                    if (!input) return;
                    
                    var icon = button.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        button.setAttribute('aria-label', 'Ficha nenosiri');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        button.setAttribute('aria-label', 'Onyesha nenosiri');
                    }
                });
            });
        });
    </script>
</body>
</html>