<?php
session_start();
require_once "../config/db.php";

// protect page
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];



if (isset($_POST["delete_id"])) {

    $delete_id = $_POST["delete_id"];

    $stmt = $conn->prepare("
        DELETE FROM trip_plan 
        WHERE id = ? AND user_id = ?
    ");

    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();

    $_SESSION["message"] = "Attraction removed from your trip!";

    header("Location: view_trips.php");
    exit();
}


// get user trips with attraction details
$stmt = $conn->prepare("
    SELECT tp.id, a.name, a.image
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
    <title>My Trips</title>
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

        .glass-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 14px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .glass-card .card-body {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .glass-card img {
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }
    </style>
</head>
<body>

<video autoplay muted loop class="bg-video">
    <source src="../assets/videos/bg5.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">

    <div class="page-header">
        <h2>My <span class="text-primary">Trip Plans</span></h2>
        <p class="mb-0">Review and manage the attractions you added to your trip.</p>
    </div>

    <?php if (isset($_SESSION["message"])): ?>
        <div class="alert alert-danger mt-3">
            <?php 
                echo $_SESSION["message"]; 
                unset($_SESSION["message"]); 
            ?>
        </div>
    <?php endif; ?>

    <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-warning mt-3">
            No trips yet. Go to Attractions and start planning your journey!
        </div>
    <?php else: ?>

        <div class="row mt-3">

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="col-md-6 mb-4">

                    <div class="glass-card">

                        <img src="../assets/images/<?php echo $row['image']; ?>" 
                             class="card-img-top"
                             style="height:260px; object-fit:cover;">

                        <div class="card-body">

                            <h5 class="card-title">
                                <?php echo $row['name']; ?>
                            </h5>

                            <form method="POST" class="mt-auto">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button class="btn btn-danger w-100" onclick="return confirm('Remove this attraction from your trip?')">
                                    Remove from Trip
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    setTimeout(function() {
        let alert = document.querySelector(".alert");
        if (alert) {
            alert.style.transition = "0.5s";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000);
</script>

</body>
</html>