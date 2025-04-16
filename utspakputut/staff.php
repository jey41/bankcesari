<?php
session_start();
if ($_SESSION['level'] != 'staff') {
    header("Location: index.php");
    exit();
}

// CRUD Logic
$users = array_map('str_getcsv', file('data/users.csv'));
$sampah = file_exists('data/sampah.csv') ? array_map('str_getcsv', file('data/sampah.csv')) : [];

if (isset($_POST['tambah'])) {
    $newData = [
        $_POST['pemilik'],
        $_POST['pemilik'],
        $_POST['jenis'],
        $_POST['jumlah'],
        $_POST['tanggal']
    ];
    file_put_contents('data/sampah.csv', implode(",", $newData) . "\n", FILE_APPEND);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="dashboard-header">
        <h2>Panel Staff - <?= $_SESSION['user'] ?></h2>
        <a href="logout.php" class="logout-btn">Keluar</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>Tambah Data Sampah</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Pemilik</label>
                    <select name="pemilik" required>
                        <?php foreach ($users as $user): ?>
                            <?php if ($user[2] == 'user'): ?>
                                <option value="<?= $user[0] ?>"><?= $user[0] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis Sampah</label>
                    <select name="jenis" required>
                        <option value="Organik">Organik</option>
                        <option value="Anorganik">Anorganik</option>
                        <option value="B3">B3</option>
                        <option value="Residual">Residual</option>
                        <option value="E-Waste">E-Waste</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah (kg)</label>
                    <input type="number" step="0.1" name="jumlah" required>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" required>
                </div>

                <button type="submit" name="tambah">Simpan Data</button>
            </form>
        </div>

        <div class="card">
            <h2>Data Sampah Terkini</h2>
            <table class="data-table">
                <!-- Tabel data sama seperti admin -->
            </table>
        </div>
    </div>
</body>

</html>