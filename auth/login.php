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
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-5">

<div class="card p-4">
<h3 class="text-center">Login</h3>

<?php if ($message): ?>
<div class="alert alert-danger"><?= $message ?></div>
<?php endif; ?>

<form method="POST">
<input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
<input class="form-control mb-2" name="password" type="password" placeholder="Password" required>

<button class="btn btn-primary w-100">Login</button>
</form>

<p class="text-center mt-2">
No account? <a href="register.php">Register</a>
</p>

</div>

</div>
</div>
</div>

</body>
</html>