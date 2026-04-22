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
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">

    <h3 class="mb-4">Explore Attractions</h3>
    <form method="GET" class="mb-4">

    <div class="row">

        <div class="col-md-4">
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

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </div>

</form>
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

    <div class="col-md-4 mb-4">

        <div class="card h-100 shadow-sm border-0">

            <img src="../assets/images/<?php echo $row['image']; ?>" 
                 class="card-img-top" 
                 style="height:200px; object-fit:cover;">

            <div class="card-body d-flex flex-column">

                <h5 class="card-title">
                    <?php echo $row['name']; ?>
                </h5>

                <p class="card-text">
                    <?php echo $row['description']; ?>
                </p>

                <!-- ✅ Button BELOW description -->
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

    <!-- ✅ MODAL MUST BE INSIDE LOOP -->
    <div class="modal fade" id="modal<?= $row['id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title"><?= $row['name'] ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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