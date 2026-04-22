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
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <h3>My Trip Map</h3>
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