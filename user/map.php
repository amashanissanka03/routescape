<?php
session_start();
require_once "../config/db.php";


if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}


$result = $conn->query("SELECT name, latitude, longitude FROM attractions WHERE latitude IS NOT NULL AND longitude IS NOT NULL");
?>

<!DOCTYPE html>
<html>
<head>
    <title>RouteScape Map</title>

   
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #map {
            height: 500px;
            width: 100%;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">

    <h3>Attractions Map</h3>

    <div id="map"></div>

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