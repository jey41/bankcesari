<?php
session_start();
if(isset($_SESSION['user'])) {
    header("Location: ".$_SESSION['level'].".php");
    exit();
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users = array_map('str_getcsv', file('data/users.csv'));
    
    foreach($users as $user) {
        if($user[0] == $_POST['username'] && $user[1] == $_POST['password']) {
            $_SESSION['user'] = $user[0];
            $_SESSION['level'] = $user[2];
            header("Location: ".$user[2].".php");
            exit();
        }
    }
    $error = "Username atau Password salah!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Login Sistem Monitoring</h1>
    </div>

    <div class="container">
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <?php if($error): ?>
                    <div class="error-message"><?= $error ?></div>
                <?php endif; ?>

                <button type="submit" class="btn-block">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>