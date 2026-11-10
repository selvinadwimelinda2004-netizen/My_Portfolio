<?php
session_start();
require_once "../config/database.php";

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, username, password FROM admin WHERE username = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin["password"])) {
        session_regenerate_id(true);

        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Portfolio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #fff5f8;
            font-family: Arial, sans-serif;
        }

        .login-card {
            max-width: 420px;
            width: 100%;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 10px 35px rgba(215,127,165,.15);
        }

        .btn-pink {
            background: #e99abb;
            color: white;
            border: none;
        }

        .btn-pink:hover {
            background: #d77fa5;
            color: white;
        }
    </style>
</head>

<body>
<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="login-card">
        <h3 class="text-center mb-2">Admin Login</h3>
        <p class="text-center text-muted mb-4">
            Silakan masuk ke dashboard portfolio
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password"
                       class="form-control" required>
            </div>

            <button type="submit" class="btn btn-pink w-100">
                Login
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="../index.php" class="text-decoration-none">
                Kembali ke Portfolio
            </a>
        </div>
    </div>

</div>
</body>
</html>