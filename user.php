<?php
session_start();
if ($_SESSION['level'] !== 'user') {
    header("Location: index.php");
    exit();
}

// Ambil data sampah
$sampah = file_exists('data/sampah.csv') ? array_map('str_getcsv', file('data/sampah.csv')) : [];

// Filter data berdasarkan username yang login
$username = $_SESSION['user'];
$data_user = array_filter($sampah, function ($entry) use ($username) {
    return $entry[0] === $username; // Kolom 0: pemilik
});

// Fungsi untuk mengubah format tanggal
function formatTanggal($date)
{
    return date('d M Y', strtotime($date));
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard User</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="dashboard-header">
        <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['user']) ?></h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <div class="card">
            <h3 class="card-title">📊 Data Sampah Anda</h3>

            <?php if (empty($data_user)): ?>
                <div class="empty-state">
                    <img src="empty-data.png" alt="No data" class="empty-icon">
                    <p>Belum ada data sampah yang tercatat</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Jenis Sampah</th>
                                <th>Jumlah (kg)</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($data_user as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span class="badge 
                                        <?= strtolower($row[2]) === 'organik'    ? 'badge-green' : '' ?>
                                        <?= strtolower($row[2]) === 'anorganik'  ? 'badge-blue'  : '' ?>
                                        <?= strtolower($row[2]) === 'b3'         ? 'badge-red'   : '' ?>
                                        <?= strtolower($row[2]) === 'residual'   ? 'badge-brown' : '' ?>
                                        <?= strtolower($row[2]) === 'e-waste'    ? 'badge-olive' : '' ?>
                                    ">
                                            <?= htmlspecialchars($row[2]) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($row[3], 1) ?></td>
                                    <td><?= formatTanggal($row[4]) ?></td>
                                    <td>
                                        <span class="status verified">
                                            ✅ Tercatat
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="card summary-card">
            <h3 class="card-title">📈 Ringkasan</h3>
            <div class="summary-grid">
                <?php
                $total = [
                    'organik' => 0,
                    'anorganik' => 0,
                    'b3' => 0,
                    'residual' => 0,
                    'e-waste' => 0
                ];

                foreach ($data_user as $row) {
                    $jenis = strtolower($row[2]);
                    if (isset($total[$jenis])) {
                        $total[$jenis] += (float)$row[3];
                    }
                }
                ?>

                <div class="summary-item">
                    <div class="summary-label organik">Organik</div>
                    <div class="summary-value"><?= number_format($total['organik'], 1) ?> kg</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label anorganik">Anorganik</div>
                    <div class="summary-value"><?= number_format($total['anorganik'], 1) ?> kg</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label b3">B3</div>
                    <div class="summary-value"><?= number_format($total['b3'], 1) ?> kg</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label residual">Residual</div>
                    <div class="summary-value"><?= number_format($total['residual'], 1) ?> kg</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label ewaste">E-Waste</div>
                    <div class="summary-value"><?= number_format($total['e-waste'], 1) ?> kg</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>