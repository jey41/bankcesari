<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: " . $_SESSION['level'] . ".php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $alamat = trim($_POST['alamat']);

    // Baca data pengguna
    $users = file_exists('data/users.csv') ? array_map('str_getcsv', file('data/users.csv')) : [];

    // Cek username unik
    foreach ($users as $user) {
        if ($user[0] === $username) {
            $error = 'Username sudah terdaftar!';
            break;
        }
    }

    if (empty($error)) {
        // Simpan data baru
        $fp = fopen('data/users.csv', 'a');
        fputcsv($fp, [$username, $password, 'user', $alamat]);
        fclose($fp);

        $_SESSION['user'] = $username;
        $_SESSION['level'] = 'user';
        header("Location: user.php");
        exit();
    }
}

// Style sama dengan index.php
// include 'style.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Registrasi Pengguna</title>
    <link rel="stylesheet" href="style.css">
    <?php echo $style; ?>
</head>

<body>
    <div class="header">
        <h2>Registrasi Pengguna Baru</h2>
    </div>

    <div class="container">
        <div class="card">
            <form method="post">
                <label>Username:</label>
                <input type="text" name="username" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <label>Alamat Rumah:</label>
                <input type="text" name="alamat" required>

                <button type="submit" style="background: <?= $color_palette['teal'] ?>; color: white;">Daftar</button>
                <?php if ($error): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>

</html>