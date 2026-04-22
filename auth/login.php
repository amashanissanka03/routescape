<?php
session_start();
require_once "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("
        SELECT id, username, password, role 
        FROM users 
        WHERE email = ?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row["password"])) {

            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];

            header("Location: ../index.php");
            exit();

        } else {
            $message = "Wrong password!";
        }

    } else {
        $message = "User not found!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        
        .bg-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
            z-index: -2;
        }

        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 10px;
            padding: 30px;
        }

        .form-control {
            background: rgba(255,255,255,0.8);
        }
    </style>
</head>

<body>


<video autoplay muted loop class="bg-video">
    <source src="../assets/videos/bg.mp4" type="video/mp4">
</video>


<div class="overlay"></div>

<div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">

        <div class="col-md-4">

            <div class="login-card text-center">

                <h2 class="mb-3">RouteScape</h2>
                <p class="mb-4">Plan your perfect trip</p>

                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">

                    <input class="form-control mb-3" name="email" type="email" placeholder="Email" required>

                    <input class="form-control mb-3" name="password" type="password" placeholder="Password" required>

                    <button class="btn btn-primary w-100">Login</button>

                </form>

                <p class="mt-3">
                    No account? <a href="register.php" class="text-white">Register</a>
                </p>

            </div>

        </div>

    </div>
</div>

</body>
</html>