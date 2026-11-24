<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline | CV. Netafarm Indolestari</title>
    <link rel="icon" type="image/png" href="{{ asset('images/netafarm-logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0284c7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Figtree', system-ui, -apple-system, sans-serif;
            background: #020617;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 1.5rem;
            max-width: 480px;
            width: 100%;
            padding: 2.25rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(2, 6, 23, 0.8), 0 0 40px rgba(56, 189, 248, 0.08);
            backdrop-filter: blur(12px);
        }
        .logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            margin-bottom: 1.5rem;
        }
        .logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 9999px;
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.75rem;
            line-height: 1.25;
        }
        p {
            font-size: 0.875rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: #0284c7;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.85rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
        }
        .btn-primary:hover {
            background: #0369a1;
            transform: translateY(-1px);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background: #334155;
            color: #ffffff;
        }
        .footer-note {
            margin-top: 1.75rem;
            font-size: 0.75rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-wrap">
            <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm Logo">
        </div>

        <div>
            <span class="badge">
                <span class="pulse-dot"></span>
                Mode Lapangan / Offline
            </span>
        </div>

        <h1>Koneksi Internet Terputus</h1>
        <p>
            Perangkat Anda tidak terhubung ke jaringan internet atau sinyal seluler di lokasi outlet sedang lemah.
            Data yang telah dimuat sebelumnya tetap aman tersimpan di cache aplikasi Anda.
        </p>

        <div class="btn-group">
            <button onclick="window.location.reload()" class="btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Coba Hubungkan Kembali</span>
            </button>
            <a href="/" class="btn-secondary">
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <p class="footer-note">
            Aplikasi otomatis tersinkronisasi saat sinyal terdeteksi kembali.
        </p>
    </div>

    <script>
        // Auto-reload when connection is restored
        window.addEventListener('online', () => {
            window.location.reload();
        });
    </script>
</body>
</html>
