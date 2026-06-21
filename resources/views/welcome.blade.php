<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Smart Coffee') }}</title>
        <meta name="description" content="Platform manajemen untuk kedai kopi dengan algoritma KNN untuk personalisasi pelanggan dan program loyalitas otomatis.">
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito+Sans:ital,opsz,wght@0,6..12,400;0,6..12,500;0,6..12,600;0,6..12,700;0,6..12,800;1,6..12,400&display=swap" rel="stylesheet">
        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            :root {
                --espresso: #3C1518;
                --cream: #FFF8E7;
                --caramel: #E8A849;
                --berry: #D64571;
                --mocha: #69432B;
                --mint: #00E5A0;
                --pink-y2k: #FF6B9D;
                --blue-y2k: #4ECDC4;
                --yellow-y2k: #FFE66D;
                --purple-y2k: #C77DFF;
            }
            body {
                font-family: 'Nunito Sans', system-ui, sans-serif;
                background: var(--cream);
                color: var(--espresso);
                min-height: 100vh;
                overflow-x: hidden;
            }

            /* TICKER */
            .ticker {
                background: var(--espresso);
                color: var(--cream);
                padding: 10px 0;
                border-bottom: 3px solid #000;
                overflow: hidden;
                white-space: nowrap;
            }
            .ticker-text {
                display: inline-block;
                animation: marquee 20s linear infinite;
                font-weight: 800;
                font-size: 12px;
                letter-spacing: 0.15em;
                text-transform: uppercase;
            }
            @keyframes marquee { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

            /* NAV */
            .nav {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 40px;
                border-bottom: 3px solid #000;
            }
            .nav-logo {
                font-family: 'Fredoka', 'Nunito Sans', system-ui, sans-serif;
                font-size: 1.4rem;
                font-weight: 600;
                letter-spacing: -0.02em;
            }
            .nav-links { display: flex; gap: 12px; }
            .nb-link {
                display: inline-block;
                padding: 8px 20px;
                border: 2px solid #000;
                border-radius: 8px;
                box-shadow: 3px 3px 0px #000;
                font-weight: 600;
                font-size: 13px;
                font-family: 'Fredoka', 'Nunito Sans', system-ui, sans-serif;
                text-decoration: none;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                transition: all 0.1s ease;
                cursor: pointer;
            }
            .nb-link:hover { transform: translate(-1px,-1px); box-shadow: 4px 4px 0px #000; }
            .nb-link:active { transform: translate(2px,2px); box-shadow: 1px 1px 0px #000; }
            .nb-link-primary { background: var(--caramel); color: var(--espresso); }
            .nb-link-secondary { background: var(--cream); color: var(--espresso); }

            /* HERO */
            .hero {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 80vh;
                padding: 60px 40px;
                position: relative;
                background:
                    radial-gradient(circle at 15% 30%, rgba(232,168,73,0.15) 0%, transparent 60%),
                    radial-gradient(circle at 85% 60%, rgba(214,69,113,0.08) 0%, transparent 50%),
                    radial-gradient(circle at 50% 90%, rgba(199,125,255,0.06) 0%, transparent 40%);
            }
            .hero-content { max-width: 900px; text-align: center; }
            .hero-badge {
                display: inline-block;
                padding: 5px 16px;
                border: 2px solid #000;
                border-radius: 999px;
                box-shadow: 2px 2px 0px #000;
                font-weight: 700;
                font-size: 11px;
                font-family: 'Fredoka', 'Nunito Sans', system-ui, sans-serif;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                background: var(--mint);
                color: var(--espresso);
                margin-bottom: 24px;
            }
            .hero-title {
                font-family: 'Fredoka', 'Nunito Sans', system-ui, sans-serif;
                font-size: clamp(2.5rem, 6vw, 4.5rem);
                font-weight: 600;
                line-height: 1.1;
                letter-spacing: -0.02em;
                margin-bottom: 20px;
            }
            .hero-title span { color: var(--berry); }
            .hero-subtitle {
                font-size: 1.1rem;
                color: var(--mocha);
                max-width: 640px;
                margin: 0 auto 40px;
                line-height: 1.7;
                font-weight: 500;
            }
            .hero-cta { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
            .hero-cta .nb-link { font-size: 15px; padding: 12px 32px; }
            .nb-link-hero { background: var(--espresso); color: var(--cream); }

            /* FLOATING EMOJIS */
            .floating { position: absolute; font-size: 3rem; opacity: 0.12; }
            .f1 { top: 10%; left: 8%; animation: floatA 6s ease-in-out infinite; }
            .f2 { top: 25%; right: 10%; animation: floatA 5s ease-in-out infinite 1s; }
            .f3 { bottom: 15%; left: 15%; animation: floatA 7s ease-in-out infinite 0.5s; }
            .f4 { bottom: 25%; right: 8%; animation: floatA 5.5s ease-in-out infinite 2s; }
            .f5 { top: 50%; left: 4%; animation: floatA 6.5s ease-in-out infinite 1.5s; }
            @keyframes floatA {
                0%, 100% { transform: translateY(0) rotate(0deg); }
                25% { transform: translateY(-12px) rotate(5deg); }
                75% { transform: translateY(6px) rotate(-3deg); }
            }

            /* FEATURES */
            .features {
                padding: 80px 40px;
                border-top: 3px solid #000;
                background: #fff;
            }
            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 24px;
                max-width: 1100px;
                margin: 0 auto;
            }
            .feature-card {
                border: 3px solid #000;
                border-radius: 12px;
                box-shadow: 5px 5px 0px #000;
                padding: 28px 24px;
                transition: all 0.15s ease;
            }
            .feature-card:hover { transform: translate(-2px,-2px); box-shadow: 7px 7px 0px #000; }
            .feature-card:nth-child(1) { background: var(--yellow-y2k); }
            .feature-card:nth-child(2) { background: var(--blue-y2k); }
            .feature-card:nth-child(3) { background: var(--pink-y2k); }
            .feature-card:nth-child(4) { background: var(--purple-y2k); }
            .feature-icon { font-size: 2.5rem; margin-bottom: 12px; }
            .feature-title { font-family: 'Fredoka', 'Nunito Sans', system-ui, sans-serif; font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.01em; }
            .feature-desc { font-size: 0.85rem; line-height: 1.7; color: var(--espresso); font-weight: 500; opacity: 0.85; }

            .section-title {
                text-align: center;
                font-family: 'Fredoka', 'Nunito Sans', system-ui, sans-serif;
                font-size: 2rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: -0.02em;
                margin-bottom: 48px;
            }

            /* FOOTER */
            .footer {
                border-top: 3px solid #000;
                background: var(--espresso);
                color: var(--cream);
                text-align: center;
                padding: 32px 20px;
                font-size: 13px;
                font-weight: 600;
            }

            @media (max-width: 640px) {
                .nav { padding: 12px 16px; }
                .hero { padding: 40px 16px; min-height: 70vh; }
                .features { padding: 48px 16px; }
                .floating { display: none; }
            }
        </style>
    </head>
    <body>
        <!-- TICKER -->
        <div class="ticker">
            <span class="ticker-text">☕ Smart Coffee — PLATFORM LOYALITAS PELANGGAN BERBASIS DATA — POWERED BY KNN MACHINE LEARNING — OPERASIONAL & ANALITIK TERPADU — ☕</span>
        </div>

        <!-- NAV -->
        <nav class="nav">
            <div class="nav-logo">☕ Smart Coffee</div>
            <div class="nav-links">
                @if(Route::has('login'))
                    @auth
                        @php
                            if (auth()->user()->role === 'admin') {
                                $dashUrl = route('admin.dashboard');
                            } elseif (auth()->user()->role === 'kasir') {
                                $dashUrl = route('kasir.cashier');
                            } else {
                                $dashUrl = route('member.dashboard');
                            }
                        @endphp
                        <a href="{{ $dashUrl }}" class="nb-link nb-link-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nb-link nb-link-secondary">Masuk</a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="nb-link nb-link-primary">Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- HERO -->
        <section class="hero">
            <span class="floating f1">☕</span>
            <span class="floating f2">🧠</span>
            <span class="floating f3">🎯</span>
            <span class="floating f4">💎</span>
            <span class="floating f5">🏆</span>
            <div class="hero-content">
                <div class="hero-badge">🧠 Powered by KNN Algorithm</div>
                <h1 class="hero-title">Kelola Pelanggan Kopi Anda dengan <span>Kecerdasan Data</span></h1>
                <p class="hero-subtitle">Platform manajemen lengkap untuk kedai kopi — loyalty engine otomatis, segmentasi pelanggan K-Nearest Neighbors, kasir POS terintegrasi, dan churn prevention cerdas.</p>
                <div class="hero-cta">
                    @if(Route::has('login'))
                        @guest
                            <a href="{{ route('login') }}" class="nb-link nb-link-hero">Masuk Sekarang ☕</a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="nb-link nb-link-primary">Daftar Gratis</a>
                            @endif
                        @else
                            @php
                                if (auth()->user()->role === 'admin') {
                                    $dashUrl = route('admin.dashboard');
                                } elseif (auth()->user()->role === 'kasir') {
                                    $dashUrl = route('kasir.cashier');
                                } else {
                                    $dashUrl = route('member.dashboard');
                                }
                            @endphp
                            <a href="{{ $dashUrl }}" class="nb-link nb-link-hero">Buka Dashboard ☕</a>
                        @endguest
                    @endif
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section class="features">
            <h2 class="section-title">☕ Fitur Utama Platform</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🧠</div>
                    <h3 class="feature-title">KNN Segmentation</h3>
                    <p class="feature-desc">Algoritma K-Nearest Neighbors mengelompokkan pelanggan berdasarkan preferensi rasa, rasio kopi, dan rata-rata belanja secara otomatis.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏆</div>
                    <h3 class="feature-title">Loyalty Engine</h3>
                    <p class="feature-desc">Sistem tier Bronze → Silver → Gold otomatis. Poin belanja terakumulasi dengan multiplier berdasarkan tier keanggotaan.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚨</div>
                    <h3 class="feature-title">Churn Prevention</h3>
                    <p class="feature-desc">Deteksi pelanggan tidak aktif > 30 hari secara otomatis dan kirim voucher re-engagement MISSYOU20 melalui simulasi WA.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Dashboard Analitik</h3>
                    <p class="feature-desc">Visualisasi scatter plot klaster pelanggan, confusion matrix KNN, dan metrik operasional coffee shop real-time.</p>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="footer">
            <p>☕ {{ date('Y') }} Smart Coffee — Tugas Mata Kuliah Pemrograman Web — Powered by Laravel & KNN Algorithm</p>
        </footer>
    </body>
</html>
