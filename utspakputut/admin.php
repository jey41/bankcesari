<?php
session_start();
if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fungsi untuk membersihkan data CSV
function clean_csv($value)
{
    return '"' . str_replace('"', '""', $value) . '"';
}

// Baca data CSV
$csvFile = 'data/sampah.csv';
$sampah = [];
$header = "pemilik,alamat,jenis,jumlah,tanggal\n";

if (file_exists($csvFile)) {
    $csvData = file($csvFile);
    if (!empty($csvData)) {
        $header = array_shift($csvData);
        foreach ($csvData as $line) {
            // Pastikan setiap baris memiliki 5 kolom
            $row = array_pad(str_getcsv($line), 5, '');
            $sampah[] = $row;
        }
    }
}

// Handle Delete
if (isset($_POST['delete'])) {
    $id = (int)$_POST['id'];
    if (isset($sampah[$id])) {
        unset($sampah[$id]);
        $sampah = array_values($sampah);

        // Tulis ulang CSV dengan header
        $content = [$header];
        foreach ($sampah as $row) {
            $content[] = implode(',', array_map('clean_csv', $row));
        }
        file_put_contents($csvFile, implode("\n", $content));
    }
    header("Location: admin.php");
    exit();
}

// Handle Update
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    if (isset($sampah[$id])) {
        // Sanitasi input dengan default value
        $data = [
            'pemilik' => trim($_POST['pemilik'] ?? ''),
            'alamat' => trim($_POST['alamat'] ?? ''),
            'jenis' => trim($_POST['jenis'] ?? ''),
            'jumlah' => (float)($_POST['jumlah'] ?? 0),
            'tanggal' => trim($_POST['tanggal'] ?? date('Y-m-d'))
        ];

        // Validasi jenis sampah
        $allowedTypes = ['Organik', 'Anorganik', 'B3', 'Residual', 'E-Waste'];
        if (!in_array($data['jenis'], $allowedTypes)) {
            die("<div class='error-msg'>Jenis sampah tidak valid!</div>");
        }

        // Update data
        $sampah[$id] = array_values($data);

        // Tulis ulang CSV
        $content = [$header];
        foreach ($sampah as $row) {
            $content[] = implode(',', array_map('clean_csv', $row));
        }
        file_put_contents($csvFile, implode("\n", $content));
    }
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="dashboard-header">
        <h2>Panel Admin</h2>
        <a href="logout.php" class="logout-btn">Keluar</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>Manajemen Data Sampah</h2>

            <!-- Edit Form -->
            <?php if (isset($_GET['edit'])): ?>
                <?php
                $editId = (int)$_GET['edit'];
                $editData = $sampah[$editId] ?? null;
                ?>
                <?php if ($editData): ?>
                    <div class="edit-form">
                        <h3>✏️ Edit Data Sampah</h3>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $editId ?>">

                            <div class="form-group">
                                <label>Pemilik:</label>
                                <input type="text" name="pemilik"
                                    value="<?= htmlspecialchars($editData[0] ?? '') ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Alamat:</label>
                                <input type="text" name="alamat"
                                    value="<?= htmlspecialchars($editData[1] ?? '') ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Jenis Sampah:</label>
                                <select name="jenis" required>
                                    <?php foreach (['Organik', 'Anorganik', 'B3', 'Residual', 'E-Waste'] as $type): ?>
                                        <option value="<?= $type ?>"
                                            <?= strtolower(trim($editData[2] ?? '')) === strtolower($type) ? 'selected' : '' ?>>
                                            <?= $type ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Jumlah (kg):</label>
                                <input type="number" step="0.1" name="jumlah"
                                    value="<?= htmlspecialchars($editData[3] ?? 0) ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Tanggal:</label>
                                <input type="date" name="tanggal"
                                    value="<?= htmlspecialchars($editData[4] ?? date('Y-m-d')) ?>"
                                    required>
                            </div>

                            <button type="submit" name="update" class="btn-update">Simpan Perubahan</button>
                            <a href="admin.php" class="cancel-btn">Batal</a>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Data Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pemilik</th>
                        <th>Alamat</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sampah as $id => $row):
                        // Default value untuk setiap kolom
                        $row = array_pad($row, 5, '');
                        $pemilik = htmlspecialchars($row[0] ?? 'N/A');
                        $alamat = htmlspecialchars($row[1] ?? 'Alamat tidak tersedia');
                        $jenis = htmlspecialchars($row[2] ?? 'Belum dikategorikan');
                        $jumlah = is_numeric($row[3] ?? 0) ? (float)$row[3] : 0.0;
                        $rawDate = $row[4] ?? '';
                        $tanggal = (!empty($rawDate) && strtotime($rawDate))
                            ? date('d M Y', strtotime($rawDate))
                            : 'Tanggal invalid';
                    ?>
                        <tr>
                            <td><?= $pemilik ?></td>
                            <td><?= $alamat ?></td>
                            <td>
                                <span class="badge 
                                    <?= match (strtolower(trim($jenis))) {
                                        'organik'    => 'badge-green',
                                        'anorganik'  => 'badge-blue',
                                        'b3'         => 'badge-red',
                                        'residual'   => 'badge-brown',
                                        'e-waste'    => 'badge-olive',
                                        default      => ''
                                    } ?>">
                                    <?= $jenis ?>
                                </span>
                            </td>
                            <td><?= number_format($jumlah, 1) ?> kg</td>
                            <td><?= $tanggal ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="admin.php?edit=<?= $id ?>" class="edit-btn">Edit</a>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <button type="submit" name="delete"
                                            class="btn-danger"
                                            onclick="return confirm('Hapus data ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>