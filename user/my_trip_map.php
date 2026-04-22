<?php
session_start();
require_once "../config/db.php";

// protect page
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// get ONLY user's trip locations
$stmt = $conn->prepare("
    SELECT a.name, a.latitude, a.longitude
    FROM trip_plan tp
    JOIN attractions a ON tp.attraction_id = a.id
    WHERE tp.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Trip Map</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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

        .page-header {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 14px;
            padding: 30px;
            margin-bottom: 25px;
            text-align: center;
        }

        .map-wrapper {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 20px;
        }

        #map {
            height: 550px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>
<body>

<video autoplay muted loop class="bg-video">
    <source src="../assets/videos/bg1.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">

    <div class="page-header">
        <h2>My Trip <span class="text-primary">Map</span></h2>
        <p class="mb-0">View the attractions in your trip plan on an interactive map.</p>
    </div>

    <div class="map-wrapper">
        <div id="map"></div>
    </div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    var map = L.map('map').setView([7.8731, 80.7718], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
</script>

<?php while ($row = $result->fetch_assoc()): ?>
<script>
    L.marker([<?= $row['latitude'] ?>, <?= $row['longitude'] ?>])
        .addTo(map)
        .bindPopup("<?= $row['name'] ?>");
</script>
<?php endwhile; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>