<?php
session_start();
require_once "../config/db.php";

// protect page
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// handle add to trip
if (isset($_POST["attraction_id"])) {

    $attraction_id = $_POST["attraction_id"];

    $stmt = $conn->prepare("
        INSERT IGNORE INTO trip_plan (user_id, attraction_id)
        VALUES (?, ?)
    ");

    $stmt->bind_param("ii", $user_id, $attraction_id);
    $stmt->execute();

    $_SESSION["message"] = "Attraction added to your trip!";

    // redirect to same page (prevents resubmission)
    header("Location: attractions.php");
    exit();
}
// get all attractions
$category = $_GET["category"] ?? "";

if ($category != "") {
    $stmt = $conn->prepare("SELECT * FROM attractions WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM attractions");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attractions</title>
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

        .filter-box {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            color: white;
        }

        select.form-control {
            background: rgba(255,255,255,0.85);
        }

        .modal-content {
            border-radius: 14px;
        }
    </style>
</head>
<body>

<video autoplay muted loop class="bg-video">
    <source src="../assets/videos/bg3.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">

    <div class="text-center text-white mb-4">
        <h2>Explore <span class="text-primary">Attractions</span></h2>
    </div>

    <div class="filter-box mb-4">
        <form method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>

                        <option value="Adventure and Outdoor Activities" <?= ($category == "Adventure and Outdoor Activities") ? "selected" : "" ?>>
                            Adventure and Outdoor Activities
                        </option>

                        <option value="Nature and Wildlife" <?= ($category == "Nature and Wildlife") ? "selected" : "" ?>>
                            Nature and Wildlife
                        </option>

                        <option value="Cultural and Lifestyle Experiences" <?= ($category == "Cultural and Lifestyle Experiences") ? "selected" : "" ?>>
                            Cultural and Lifestyle Experiences
                        </option>

                        <option value="Dining and Leisure" <?= ($category == "Dining and Leisure") ? "selected" : "" ?>>
                            Dining and Leisure
                        </option>

                        <option value="Religious and Heritage Sites" <?= ($category == "Religious and Heritage Sites") ? "selected" : "" ?>>
                            Religious and Heritage Sites
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <?php if (isset($_SESSION["message"])): ?>
        <div class="alert alert-success">
            <?php
                echo $_SESSION["message"];
                unset($_SESSION["message"]);
            ?>
        </div>
    <?php endif; ?>

    <div class="row">

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

                        <p class="card-text text-light mb-4">
                            <?php echo $row['description']; ?>
                        </p>

                        <div class="mt-auto">
                            <button class="btn btn-info w-100 mb-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal<?= $row['id'] ?>">
                                View Details
                            </button>

                            <form method="POST">
                                <input type="hidden" name="attraction_id" value="<?php echo $row['id']; ?>">
                                <button class="btn btn-primary w-100">
                                    Add to Trip
                                </button>
                            </form>
                        </div>

                    </div>

                </div>

            </div>

            <div class="modal fade" id="modal<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content bg-dark text-white">

                        <div class="modal-header">
                            <h5 class="modal-title"><?= $row['name'] ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <?= nl2br($row['details']) ?>
                        </div>

                    </div>
                </div>
            </div>

        <?php endwhile; ?>

    </div>

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