<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}
include("includes/navbar.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>RouteScape</title>
</head>

<body>
    <h1>RouteScape Home</h1>
    <p>Welcome, <?php echo $_SESSION["username"]; ?>!</p>

    <a href="auth/logout.php">Logout</a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>