<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Perpustakaan - Modern CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet"> <!-- Icon CDN -->
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        /* Inline CSS untuk modern look (bisa pindah ke style.css) */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .navbar-brand {
            font-weight: bold;
            color: #007bff !important;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(45deg, #fff, #f0f8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInUp 1s ease;
        }
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.3s both;
        }
        .stats-card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .stats-card .card-body {
            padding: 2rem;
            text-align: center;
        }
        .stats-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0;
            animation: fadeInUp 1s ease 0.5s both;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            opacity: 0;
            animation: countUp 1.5s ease forwards;
        }
        .anggota-card .stats-icon { color: #007bff; }
        .anggota-card .stats-number { color: #007bff; }
        .buku-card .stats-icon { color: #28a745; }
        .buku-card .stats-number { color: #28a745; }
        .peminjaman-card .stats-icon { color: #fd7e14; }
        .peminjaman-card .stats-number { color: #fd7e14; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes countUp {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .btn-modern {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            /* IMPROVE TOMBOL: Warna Teks Putih */
            color: white !important; /* Pastikan teks putih konstan */
        }
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
            /* IMPROVE TOMBOL: Teks tetap putih saat hover */
            color: white !important;
        }
    </style>
</head>
<body>
    <!-- Navbar Modern -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="bi bi-book-half me-2"></i>Perpustakaan Digital</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="anggota.php"><i class="bi bi-people me-1"></i>Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="buku.php"><i class="bi bi-book me-1"></i>Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php"><i class="bi bi-arrow-repeat me-1"></i>Peminjaman</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section mt-5 pt-5"> <!-- mt-5 biar navbar gak nutupin -->
        <div class="container">
            <h1 class="hero-title">Selamat Datang di Dashboard Perpustakaan</h1>
            <p class="hero-subtitle">Kelola data anggota, buku, dan peminjaman dengan mudah dan efisien.</p>
        </div>
    </section>

    <!-- Stats Cards Section -->
    <section class="container my-5 mb-5"> <!-- Tambah mb-5 biar spacing bawah -->
        <div class="row g-4">
            <!-- Total Anggota -->
            <div class="col-md-4">
                <div class="card stats-card anggota-card">
                    <div class="card-body">
                        <i class="bi bi-people-fill stats-icon"></i>
                        <h5 class="card-title">Total Anggota</h5>
                        <div class="stats-number" data-target="<?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM Anggota");
                            echo $stmt->fetchColumn();
                        ?>">0</div>
                        <a href="anggota.php" class="btn btn-modern mt-3">Kelola Anggota</a>
                    </div>
                </div>
            </div>

            <!-- Total Buku -->
            <div class="col-md-4">
                <div class="card stats-card buku-card">
                    <div class="card-body">
                        <i class="bi bi-book-fill stats-icon"></i>
                        <h5 class="card-title">Total Buku</h5>
                        <div class="stats-number" data-target="<?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM Buku");
                            echo $stmt->fetchColumn();
                        ?>">0</div>
                        <a href="buku.php" class="btn btn-modern mt-3">Kelola Buku</a>
                    </div>
                </div>
            </div>

            <!-- Total Peminjaman -->
            <div class="col-md-4">
                <div class="card stats-card peminjaman-card">
                    <div class="card-body">
                        <i class="bi bi-arrow-clockwise stats-icon"></i>
                        <h5 class="card-title">Total Peminjaman</h5>
                        <div class="stats-number" data-target="<?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM Peminjaman");
                            echo $stmt->fetchColumn();
                        ?>">0</div>
                        <a href="peminjaman.php" class="btn btn-modern mt-3">Kelola Peminjaman</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi Counter untuk angka total (modern touch)
        function animateCounters() {
            const counters = document.querySelectorAll('.stats-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const increment = target / 100; // Smooth increment
                let current = 0;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 20);
            });
        }

        // Jalankan animasi saat load
        window.addEventListener('load', animateCounters);

        // Smooth scroll untuk navbar (opsional)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
