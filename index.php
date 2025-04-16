<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: " . $_SESSION['level'] . ".php");
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Bank Sampah Perumahan</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>Bank Sampah Perumahan</h1>
        <p>Sistem Monitoring Pengelolaan Sampah Berbasis Komunitas</p>
    </div>

    <?php if (!isset($_SESSION['user'])): ?>
        <nav class="nav-menu">
            <a href="login.php">Login</a>
            <a href="register.php">Registrasi</a>
        </nav>

        <div class="container">
            <div class="card">
                <h2 class="text-center">Selamat Datang</h2>
                <p>Sistem monitoring pengelolaan sampah perumahan dengan kategori:</p>
                <ul>
                    <li>📦 Sampah Organik</li>
                    <li>♻️ Sampah Anorganik</li>
                    <li>⚠️ B3</li>
                    <li>🗑️ Residual</li>
                    <li>🔋 E-Waste</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>