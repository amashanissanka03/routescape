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
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">

    <h3>My Trip Plans</h3>

    
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
            No trips found. Add some attractions first.
        </div>
    <?php else: ?>

        <div class="row mt-3">

<?php while ($row = $result->fetch_assoc()): ?>

    <div class="col-md-4 mb-4">

        <div class="card h-100 shadow-sm">

            <img src="../assets/images/<?php echo $row['image']; ?>" 
                 class="card-img-top"
                 style="height:200px; object-fit:cover;">

            <div class="card-body d-flex flex-column">

                <h5 class="card-title">
                    <?php echo $row['name']; ?>
                </h5>

                <form method="POST" class="mt-auto">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <button class="btn btn-danger w-100">
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