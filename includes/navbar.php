<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    
    <a class="navbar-brand" href="/routescape/index.php">RouteScape</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

        <!-- LEFT SIDE LINKS -->
        <?php if (isset($_SESSION["user_id"])): ?>
            <ul class="navbar-nav me-auto">
                <?php if ($_SESSION["role"] == "admin"): ?>
    <li class="nav-item">
        <a class="nav-link text-warning" href="/routescape/admin/manage_attractions.php">
            Admin Panel
        </a>
    </li>
<?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="/routescape/user/attractions.php">Attractions</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/routescape/user/view_trips.php">My Trips</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/routescape/user/my_trip_map.php">My Map</a>
                </li>

            </ul>
        <?php endif; ?>

        <!-- RIGHT SIDE USER INFO -->
        <div class="ms-auto d-flex align-items-center">

            <?php if (isset($_SESSION["user_id"])): ?>

                <span class="text-white me-3">
                    👤 <?php echo $_SESSION["username"]; ?>
                    (<?php echo $_SESSION["role"]; ?>)
                </span>

                <a href="/routescape/auth/logout.php" class="btn btn-danger btn-sm">
                    Logout
                </a>

            <?php else: ?>

                <a href="/routescape/auth/login.php" class="btn btn-primary btn-sm me-2">Login</a>
                <a href="/routescape/auth/register.php" class="btn btn-success btn-sm">Register</a>

            <?php endif; ?>

        </div>

    </div>
</nav>