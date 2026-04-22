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
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-5">

<div class="card p-4">
<h3 class="text-center">Register</h3>

<?php if ($message): ?>
<div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST">
<input class="form-control mb-2" name="username" placeholder="Username" required>
<input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
<input class="form-control mb-2" name="password" type="password" placeholder="Password" required>
<input class="form-control mb-2" name="confirm_password" type="password" placeholder="Confirm Password" required>

<button class="btn btn-success w-100">Register</button>
</form>

<p class="text-center mt-2">
Already have account? <a href="login.php">Login</a>
</p>

</div>

</div>
</div>
</div>

</body>
</html>