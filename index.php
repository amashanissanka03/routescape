<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>RouteScape Home</title>
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

    
    .glass-card {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        color: white;
        border-radius: 14px;
        padding: 30px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        transition: 0.3s;
    }

    .feature-icon {
        font-size: 32px;
    }

    .welcome-box {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        color: white;
        border-radius: 14px;
        padding: 40px;
    }
</style>
</head>

<body>
    <video autoplay muted loop class="bg-video">
    <source src="assets/videos/bg2.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<?php include("includes/navbar.php"); ?>

<div class="container hero-section">

    <div class="container py-5">

    <div class="welcome-box text-center mb-5">
        <h1 class="mb-3">
            Welcome to <span class="text-primary">RouteScape</span>
        </h1>

        <p class="lead">
            Hello, <?php echo $_SESSION["username"]; ?> 👋
        </p>

        <p>
            Plan your perfect one-day journey by exploring attractions and building your trip.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-4 d-flex">
            <div class="glass-card text-center w-100">

                <div>
                    <div class="feature-icon mb-3">📍</div>
                    <h4>Explore Attractions</h4>
                    <p>
                        Browse attractions, filter by category, and view essential details before planning.
                    </p>
                </div>

                <a href="user/attractions.php" class="btn btn-primary mt-3">
                    View Attractions
                </a>

            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="glass-card text-center w-100">

                <div>
                    <div class="feature-icon mb-3">🗺️</div>
                    <h4>My Trip Map</h4>
                    <p>
                        Visualize your selected attractions on an interactive map.
                    </p>
                </div>

                <a href="user/my_trip_map.php" class="btn btn-success mt-3">
                    Open Map
                </a>

            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="glass-card text-center w-100">

                <div>
                    <div class="feature-icon mb-3">🧳</div>
                    <h4>My Trip Plan</h4>
                    <p>
                        Review and manage your one-day trip plan.
                    </p>
                </div>

                <a href="user/view_trips.php" class="btn btn-dark mt-3">
                    View Trips
                </a>

            </div>
        </div>

    </div>

    <?php if ($_SESSION["role"] == "admin"): ?>
        <div class="text-center mt-5">
            <a href="admin/manage_attractions.php" class="btn btn-warning btn-lg">
                Admin Panel
            </a>
        </div>
    <?php endif; ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>