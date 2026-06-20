<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- SEO Meta Tags -->
    @if(app()->getLocale() == 'sw')
        <title>Ukoo wa Nyahende - Karibu kwenye Mfumo wa Ukoo</title>
        <meta name="description" content="Karibu kwenye tovuti rasmi ya Ukoo wa Nyahende. Gundua asili yetu, mti wa familia (family tree) na matukio muhimu. Jiunge sasa kuungana na ndugu zako.">
    @else
        <title>Nyahende Clan - Welcome to the Clan System</title>
        <meta name="description" content="Welcome to the official Nyahende Clan portal. Discover our ancestry, family tree, and essential clan updates. Join now to connect with your relatives.">
    @endif
    
    <meta name="keywords" content="Nyahende, Ukoo wa Nyahende, Family Tree, Nyisulya, Mti wa Ukoo, Clan, Tanzania">
    <meta name="robots" content="index, follow">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="alternate" hreflang="sw" href="{{ url('/') }}" />
    <link rel="alternate" hreflang="en" href="{{ url('/') }}?lang=en" />
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}" />
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Ukoo wa Nyahende">
    <meta property="og:description" content="Ungana na wanafamilia wa Ukoo wa Nyahende, gundua asili yako, na ushiriki katika maendeleo ya ukoo wetu.">
    <meta property="og:image" content="{{ asset('images/brand-banner.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="Ukoo wa Nyahende">
    <meta property="twitter:description" content="Ungana na wanafamilia wa Ukoo wa Nyahende, gundua asili yako, na ushiriki katika maendeleo ya ukoo wetu.">
    <meta property="twitter:image" content="{{ asset('images/brand-banner.png') }}">

    <!-- Schema.org for Google -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Organization",
      "name": "Ukoo wa Nyahende",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/brand-banner.png') }}",
      "description": "Ungana na wanafamilia wa Ukoo wa Nyahende, gundua asili yako, na ushiriki katika maendeleo ya ukoo wetu."
    }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Styles -->
    <style>
        :root {
            --primary-grad: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-grad: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --text-dark: #0f172a;
            --text-muted: #475569;
            --bg-light: #f8fafc;
            --card-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 30px 60px -15px rgba(124, 58, 237, 0.2);
            --transition-smooth: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            background-color: #fdfdfd;
            background-image: 
                radial-gradient(at 0% 0%, rgba(124, 58, 237, 0.05) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.05) 0, transparent 50%);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header Style */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            transition: var(--transition-smooth);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.5px;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-grad);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(118, 75, 162, 0.2);
        }

        .brand-icon svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Language Switcher */
        .lang-switch {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 8px;
            gap: 2px;
        }

        .lang-btn {
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: var(--text-muted);
            border-radius: 6px;
            transition: var(--transition-smooth);
        }

        .lang-btn.active {
            background: #ffffff;
            color: var(--text-dark);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition-smooth);
            cursor: pointer;
            border: none;
        }

        .btn-outline {
            border: 2px solid #e2e8f0;
            color: var(--text-dark);
            background: transparent;
        }

        .btn-outline:hover {
            border-color: #764ba2;
            color: #764ba2;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary-grad);
            color: white;
            box-shadow: 0 10px 20px -5px rgba(118, 75, 162, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(118, 75, 162, 0.4);
        }

        /* Hero Section */
        .hero-section {
            padding: 160px 24px 80px 24px;
            position: relative;
            background: radial-gradient(circle at 80% 20%, rgba(102, 126, 234, 0.08) 0%, rgba(255, 255, 255, 0) 50%),
                        radial-gradient(circle at 10% 80%, rgba(118, 75, 162, 0.08) 0%, rgba(255, 255, 255, 0) 50%);
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 48px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(118, 75, 162, 0.08);
            color: #764ba2;
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 24px;
            border: 1px solid rgba(118, 75, 162, 0.15);
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 52px;
            font-weight: 800;
            line-height: 1.15;
            color: #0f172a;
            margin-bottom: 24px;
            letter-spacing: -1px;
        }

        .hero-title span {
            background: var(--primary-grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 18px;
            color: var(--text-muted);
            margin-bottom: 36px;
            max-width: 540px;
        }

        .hero-btns {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        /* Floating Tree Illustration */
        .hero-graphic {
            position: relative;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-graphic::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--primary-grad);
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: pulse-glow 6s infinite alternate;
        }

        @keyframes pulse-glow {
            0% { transform: scale(0.9); opacity: 0.1; }
            100% { transform: scale(1.1); opacity: 0.25; }
        }

        .tree-illustration {
            width: 100%;
            max-width: 420px;
            height: 100%;
            position: relative;
            animation: float-anim 6s ease-in-out infinite;
        }

        @keyframes float-anim {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Statistics Bar */
        .stats-section {
            padding: 40px 24px;
            background: var(--bg-light);
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 32px 24px;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--glass-border);
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--hover-shadow);
            border-color: rgba(124, 58, 237, 0.3);
        }

        .stat-num {
            font-size: 40px;
            font-weight: 800;
            color: #764ba2;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
            background: var(--primary-grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Intro / History Section */
        .intro-section {
            padding: 100px 24px;
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }

        .section-badge {
            display: inline-block;
            background: rgba(246, 211, 101, 0.15);
            color: #fda085;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 24px;
        }

        .intro-text {
            font-size: 18px;
            color: #475569;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto 48px auto;
        }

        /* Features Section */
        .features-section {
            padding: 100px 24px;
            background: #ffffff;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            margin-top: 48px;
        }

        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px 32px;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--card-shadow);
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--hover-shadow);
            border-color: rgba(124, 58, 237, 0.4);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: rgba(102, 126, 234, 0.08);
            color: #667eea;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 24px;
            transition: var(--transition-smooth);
        }

        .feature-card:hover .feature-icon {
            background: var(--primary-grad);
            color: white;
            box-shadow: 0 8px 16px rgba(118, 75, 162, 0.2);
        }

        .feature-name {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .feature-desc {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Call To Action Section */
        .cta-section {
            padding: 80px 24px;
            background: var(--primary-grad);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -100px;
            left: -100px;
        }

        .cta-section::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            bottom: -200px;
            right: -100px;
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }

        .cta-title {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-text {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 36px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-white {
            background: #ffffff;
            color: #764ba2;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .btn-white:hover {
            transform: translateY(-2px);
            background: #f8fafc;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
        }

        /* Footer */
        .site-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 60px 24px 30px 24px;
            border-top: 1px solid #1e293b;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
            padding-bottom: 40px;
            border-bottom: 1px solid #1e293b;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 20px;
        }

        .footer-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
        }

        .footer-info a {
            color: #38bdf8;
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .footer-info a:hover {
            color: #ffffff;
        }

        .footer-copyright {
            max-width: 1200px;
            margin: 30px auto 0 auto;
            text-align: center;
            font-size: 14px;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Responsiveness */
        @media (max-width: 1024px) {
            .hero-title { font-size: 42px; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 40px;
            }
            .hero-subtitle {
                margin-left: auto;
                margin-right: auto;
            }
            .hero-btns {
                justify-content: center;
            }
            .hero-graphic {
                height: 300px;
                order: -1;
            }
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .footer-container {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }
            .footer-info {
                align-items: center;
            }
            .footer-copyright {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .header-container {
                padding: 12px 16px;
                gap: 8px;
            }
            .brand-link {
                font-size: 16px;
                gap: 8px;
            }
            .brand-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
            }
            .brand-icon svg {
                width: 16px;
                height: 16px;
            }
            .nav-actions {
                gap: 8px;
            }
            .lang-switch {
                padding: 2px;
            }
            .lang-btn {
                padding: 4px 8px;
                font-size: 11px;
            }
            .btn {
                padding: 8px 12px;
                font-size: 13px;
                border-radius: 8px;
                gap: 4px;
            }
        }

        @media (max-width: 480px) {
            .brand-link span {
                font-size: 14px;
            }
            .btn span {
                font-size: 12px;
            }
        }

        @media (max-width: 400px) {
            .brand-link span {
                display: none; /* Hide brand text, only show icon */
            }
            .btn span {
                display: none; /* Hide button text, only show icons */
            }
            .btn {
                padding: 8px 12px;
                border-radius: 6px;
            }
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header class="site-header">
        <div class="header-container">
            <a href="{{ url('/') }}" class="brand-link" id="brandLink">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C10.9 2 10 2.9 10 4C10 5.1 10.9 6 12 6C13.1 6 14 5.1 14 4C14 2.9 13.1 2 12 2M12 8C9.8 8 8 9.8 8 12C8 14.2 9.8 16 12 16C14.2 16 16 14.2 16 12C16 9.8 14.2 8 12 8M6 13C4.9 13 4 13.9 4 15C4 16.1 4.9 17 6 17C7.1 17 8 16.1 8 15C8 13.9 7.1 13 6 13M18 13C16.9 13 16 13.9 16 15C16 16.1 16.9 17 18 17C19.1 17 20 16.1 20 15C20 13.9 19.1 13 18 13M6 18C4.3 18 3 19.3 3 21H9C9 19.3 7.7 18 6 18M18 18C16.3 18 15 19.3 15 21H21C21 19.3 19.7 18 18 18Z"/>
                    </svg>
                </div>
                <span>Ukoo wa Nyahende</span>
            </a>

            <div class="nav-actions">
                <!-- Language Selector -->
                <div class="lang-switch">
                    <a href="{{ route('language.switch', 'sw') }}" class="lang-btn {{ app()->getLocale() == 'sw' ? 'active' : '' }}" id="langSw">SW</a>
                    <a href="{{ route('language.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}" id="langEn">EN</a>
                </div>

                <a href="{{ route('login') }}" class="btn btn-outline" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>{{ app()->getLocale() == 'sw' ? 'Ingia' : 'Log In' }}</span>
                </a>
                
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary" id="registerBtn">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ app()->getLocale() == 'sw' ? 'Sajili' : 'Register' }}</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-container">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>{{ app()->getLocale() == 'sw' ? 'Tovuti Rasmi ya Ukoo' : 'Official Clan Portal' }}</span>
                    </div>
                    
                    <h1 class="hero-title" id="mainHeading">
                        @if(app()->getLocale() == 'sw')
                            Karibu Kwenye Mfumo wa <span>Ukoo wa Nyahende</span>
                        @else
                            Welcome to the <span>Nyahende Clan</span> Portal
                        @endif
                    </h1>
                    
                    <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                        @if(app()->getLocale() == 'sw')
                            Kuunganisha familia zetu, kuhifadhi mti wetu wa ukoo, kushirikiana katika matukio, kampeni, na kuendeleza historia na urithi wetu kwa vizazi vya sasa na vijavyo.
                        @else
                            Connecting our families, preserving our lineage, sharing events and campaigns, and passing down our rich heritage to current and future generations.
                        @endif
                    </p>
                    
                    <div class="hero-btns" data-aos="fade-up" data-aos-delay="200">
                        <a href="{{ route('login') }}" class="btn btn-primary" id="heroStartBtn">
                            <i class="fas fa-tree"></i>
                            <span>{{ app()->getLocale() == 'sw' ? 'Fungua Mti wa Ukoo' : 'Explore Family Tree' }}</span>
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline" id="heroRegisterBtn">
                                <i class="fas fa-users"></i>
                                <span>{{ app()->getLocale() == 'sw' ? 'Jiunge Nasi Leo' : 'Register Profile' }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="hero-graphic" data-aos="zoom-in" data-aos-delay="300">
                    <!-- Custom SVG representing connected family members with pulse animations -->
                    <svg class="tree-illustration" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                        <!-- Connecting lines -->
                        <path d="M200,80 L200,200" stroke="#764ba2" stroke-width="3" stroke-dasharray="5,5" />
                        <path d="M200,200 L100,280" stroke="#667eea" stroke-width="3" />
                        <path d="M200,200 L300,280" stroke="#667eea" stroke-width="3" />
                        <path d="M100,280 L50,340" stroke="#fda085" stroke-width="2" />
                        <path d="M100,280 L150,340" stroke="#fda085" stroke-width="2" />
                        
                        <!-- Nodes -->
                        <!-- Founder Node -->
                        <circle cx="200" cy="80" r="24" fill="url(#founderGrad)" />
                        <i class="fa fa-crown" style="color: white;"></i>
                        
                        <!-- Level 1 Nodes -->
                        <circle cx="200" cy="200" r="18" fill="url(#primaryGrad)" />
                        <circle cx="100" cy="280" r="16" fill="url(#primaryGrad)" />
                        <circle cx="300" cy="280" r="16" fill="url(#primaryGrad)" />
                        
                        <!-- Level 2 Nodes -->
                        <circle cx="50" cy="340" r="12" fill="url(#accentGrad)" />
                        <circle cx="150" cy="340" r="12" fill="url(#accentGrad)" />
                        
                        <!-- Glowing pulse effect on top node -->
                        <circle cx="200" cy="80" r="30" fill="none" stroke="#fda085" stroke-width="2">
                            <animate attributeName="r" values="24;38;24" dur="3s" repeatCount="indefinite" />
                            <animate attributeName="opacity" values="0.8;0;0.8" dur="3s" repeatCount="indefinite" />
                        </circle>

                        <circle cx="100" cy="280" r="22" fill="none" stroke="#667eea" stroke-width="1.5">
                            <animate attributeName="r" values="16;28;16" dur="4s" repeatCount="indefinite" />
                            <animate attributeName="opacity" values="0.7;0;0.7" dur="4s" repeatCount="indefinite" />
                        </circle>

                        <circle cx="300" cy="280" r="22" fill="none" stroke="#667eea" stroke-width="1.5">
                            <animate attributeName="r" values="16;28;16" values="16;28;16" dur="4.5s" repeatCount="indefinite" />
                            <animate attributeName="opacity" values="0.7;0;0.7" dur="4.5s" repeatCount="indefinite" />
                        </circle>

                        <!-- Definitions -->
                        <defs>
                            <linearGradient id="founderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#764ba2" />
                                <stop offset="100%" stop-color="#fda085" />
                            </linearGradient>
                            <linearGradient id="primaryGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#667eea" />
                                <stop offset="100%" stop-color="#764ba2" />
                            </linearGradient>
                            <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f6d365" />
                                <stop offset="100%" stop-color="#fda085" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section" id="statsSection">
            <div class="stats-container">
                <div class="stat-card" id="statMembers" data-aos="fade-up" data-aos-delay="0">
                    <div class="stat-num">{{ $stats['members'] }}</div>
                    <div class="stat-label">{{ app()->getLocale() == 'sw' ? 'Wanafamilia' : 'Family Members' }}</div>
                </div>
                <div class="stat-card" id="statFamilies" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-num">{{ $stats['families'] }}</div>
                    <div class="stat-label">{{ app()->getLocale() == 'sw' ? 'Familia' : 'Families' }}</div>
                </div>
                <div class="stat-card" id="statClans" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-num">{{ $stats['clans'] }}</div>
                    <div class="stat-label">{{ app()->getLocale() == 'sw' ? 'Koo' : 'Clans' }}</div>
                </div>
            </div>
        </section>

        <!-- Intro Section -->
        <section class="intro-section" id="aboutSection" data-aos="fade-up">
            <span class="section-badge">{{ app()->getLocale() == 'sw' ? 'Asili na Malengo' : 'Heritage & Vision' }}</span>
            <h2 class="section-title">
                {{ app()->getLocale() == 'sw' ? 'Kuhusu Ukoo wa Nyahende' : 'About the Nyahende Clan' }}
            </h2>
            <p class="intro-text">
                @if(app()->getLocale() == 'sw')
                    Ukoo wa Nyahende una historia ndefu na tajiri ya umoja, upendo na mshikamano. Mfumo huu wa kidijitali umelenga kuwaunganisha wanafamilia wote walioko maeneo mbalimbali duniani ili kudumisha asili yetu, kushirikiana katika shughuli mbalimbali za maendeleo, na kurithisha historia hii ya thamani kwa watoto na vizazi vyetu vyote.
                @else
                    The Nyahende Clan shares a deep-rooted history built on unity, affection, and collaboration. This digital portal is designed to bridge wanafamilia globally, celebrate our rich genealogy, coordinate local development projects, and secure our values and traditions for generations to come.
                @endif
            </p>
        </section>

        <!-- Features Section -->
        <section class="features-section" id="featuresSection">
            <div class="features-container">
                <div style="text-align: center; margin-bottom: 50px;">
                    <span class="section-badge" style="background: rgba(102, 126, 234, 0.15); color: #667eea;">{{ app()->getLocale() == 'sw' ? 'Huduma za Mfumo' : 'Portal Features' }}</span>
                    <h2 class="section-title">{{ app()->getLocale() == 'sw' ? 'Kile Unachoweza Kufanya' : 'What You Can Do' }}</h2>
                </div>

                <div class="features-grid">
                    <!-- Feature 1: Family Tree -->
                    <div class="feature-card" id="featTree" data-aos="fade-up" data-aos-delay="0">
                        <div class="feature-icon"><i class="fas fa-tree"></i></div>
                        <h3 class="feature-name">{{ app()->getLocale() == 'sw' ? 'Mti wa Ukoo' : 'Family Tree' }}</h3>
                        <p class="feature-desc">
                            {{ app()->getLocale() == 'sw' ? 'Chunguza mti wa ukoo mzima, tafuta asili yako na utambue uhusiano wako na kila mwanafamilia kwa urahisi kabisa.' : 'Search the entire ancestry, check details of your cousins and descendants, and visually trace relationships.' }}
                        </p>
                    </div>

                    <!-- Feature 2: Timeline -->
                    <div class="feature-card" id="featTimeline" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-icon"><i class="fas fa-history"></i></div>
                        <h3 class="feature-name">{{ app()->getLocale() == 'sw' ? 'Historia ya Matukio' : 'Family Timeline' }}</h3>
                        <p class="feature-desc">
                            {{ app()->getLocale() == 'sw' ? 'Tazama historia ya matukio muhimu, mafanikio ya ukoo, sherehe za kale, na hadithi zilizotufikisha hapa leo.' : 'Discover our clan timeline, major milestones, stories, and the cultural context of our ancestors.' }}
                        </p>
                    </div>

                    <!-- Feature 3: Contributions -->
                    <div class="feature-card" id="featContributions" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-icon"><i class="fas fa-hand-holding-heart"></i></div>
                        <h3 class="feature-name">{{ app()->getLocale() == 'sw' ? 'Kampeni na Michango' : 'Michango & Campaigns' }}</h3>
                        <p class="feature-desc">
                            {{ app()->getLocale() == 'sw' ? 'Changia shughuli za maendeleo ya ukoo, misaada ya dharura, au sherehe kwa uwazi na usimamizi wa hali ya juu.' : 'Safely participate in collective clan development, welfare funds, and family gatherings with direct online updates.' }}
                        </p>
                    </div>

                    <!-- Feature 4: Calendar -->
                    <div class="feature-card" id="featCalendar" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                        <h3 class="feature-name">{{ app()->getLocale() == 'sw' ? 'Kalenda ya Matukio' : 'Clan Calendar' }}</h3>
                        <p class="feature-desc">
                            {{ app()->getLocale() == 'sw' ? 'Pata taarifa za vikao vya ukoo, matukio ya kijamii, sherehe, na misiba ili ushirikiane na wenzako kwa wakati.' : 'Keep track of scheduled clan meetings, events, weddings, and key dates to stay informed and supportive.' }}
                        </p>
                    </div>

                    <!-- Feature 5: Gallery -->
                    <div class="feature-card" id="featGallery" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-icon"><i class="fas fa-images"></i></div>
                        <h3 class="feature-name">{{ app()->getLocale() == 'sw' ? 'Picha za Matukio' : 'Shared Photo Galleries' }}</h3>
                        <p class="feature-desc">
                            {{ app()->getLocale() == 'sw' ? 'Pakia na utazame albamu za picha za sherehe, mikutano, na kumbukumbu mbalimbali za wanafamilia.' : 'Save and explore albums of family gatherings, historic meetings, and memorable snapshots shared by members.' }}
                        </p>
                    </div>

                    <!-- Feature 6: Map -->
                    <div class="feature-card" id="featMap" data-aos="fade-up" data-aos-delay="500">
                        <div class="feature-icon"><i class="fas fa-map-marked-alt"></i></div>
                        <h3 class="feature-name">{{ app()->getLocale() == 'sw' ? 'Ramani ya Asili' : 'Ancestral Map' }}</h3>
                        <p class="feature-desc">
                            {{ app()->getLocale() == 'sw' ? 'Tazama na ugundue maeneo wanamoishi wanafamilia wa ukoo wetu au asili ya chimbuko letu.' : 'Identify geographic origins and current settlements of the clan.' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section" id="joinSection">
            <div class="cta-container" data-aos="zoom-in">
                <h2 class="cta-title">
                    {{ app()->getLocale() == 'sw' ? 'Je, Wewe ni Mwanafamilia wa Nyahende?' : 'Are You a Nyahende Descendant?' }}
                </h2>
                <p class="cta-text">
                    {{ app()->getLocale() == 'sw' ? 'Jiunge na mfumo wetu rasmi leo. Jiandikishe ili uongezwe kwenye mti wa ukoo na ushiriki kikamilifu katika maendeleo yetu.' : 'Connect with the official system today. Register your profile to be integrated into the family tree and support our projects.' }}
                </p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-white btn-lg" id="ctaRegisterBtn">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ app()->getLocale() == 'sw' ? 'Sajili Wasifu Wako Sasa' : 'Register Your Account Now' }}</span>
                    </a>
                @endif
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <a href="{{ url('/') }}" class="footer-brand">
                <i class="fas fa-shield-alt" style="color:#fda085;"></i>
                <span>Ukoo wa Nyahende</span>
            </a>

            <div class="footer-info">
                <span><i class="fas fa-phone-alt" style="margin-right: 8px;"></i> kwa msaada zaidi au mawasiliano 0787661560</span>
            </div>
        </div>

        <div class="footer-copyright">
            <span>&copy; {{ date('Y') }} <strong>Felician Joseph Nyisulya</strong>. {{ app()->getLocale() == 'sw' ? 'Haki zote zimehifadhiwa.' : 'All rights reserved.' }}</span>
            <span>Made with <i class="fas fa-heart" style="color: #ef4444;"></i> for Ukoo wa Nyahende</span>
        </div>
    </footer>


    <!-- AOS Animation Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });
    </script>
</body>

</html>
