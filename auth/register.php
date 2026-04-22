<?php
require_once "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {

        // check duplicate
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Username or Email already exists!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, role)
                VALUES (?, ?, ?, 'user')
            ");

            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "Registration successful! You can login now.";
            } else {
                $message = "Error occurred.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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

        .register-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        }

        .form-control {
            background: rgba(255,255,255,0.88);
            border: none;
        }

        .form-control:focus {
            box-shadow: none;
            border: 1px solid rgba(255,255,255,0.6);
        }

        a {
            text-decoration: none;
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
        <div class="col-md-5 col-lg-4">

            <div class="register-card text-center">
                <h2 class="mb-2">Create Account</h2>
                <p class="mb-4">Join RouteScape and start planning your journey</p>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input class="form-control mb-3" name="username" placeholder="Username" required>
                    <input class="form-control mb-3" name="email" type="email" placeholder="Email" required>
                    <input class="form-control mb-3" name="password" type="password" placeholder="Password" required>
                    <input class="form-control mb-3" name="confirm_password" type="password" placeholder="Confirm Password" required>

                    <button class="btn btn-success w-100">Register</button>
                </form>

                <p class="mt-3 mb-0">
                    Already have an account? <a href="login.php" class="text-blue fw-semibold">Login</a>
                </p>
            </div>

        </div>
    </div>
</div>

</body>
</html>