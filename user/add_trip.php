<?php
session_start();
require_once "../config/db.php";

// protect page
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

// when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $attraction_ids = $_POST["attraction_id"];

    foreach ($attraction_ids as $attraction_id) {

        $stmt = $conn->prepare("
    INSERT IGNORE INTO trip_plan (user_id, attraction_id)
    VALUES (?, ?)
");

        $stmt->bind_param("ii", $user_id, $attraction_id);
        $stmt->execute();
    }

    $message = "Trip plan(s) saved successfully!";
}

// get attractions for dropdown
$attractions = $conn->query("SELECT id, name FROM attractions");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Trip Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">

    <h3>Create Trip Plan</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Select Attraction</label>
         <p class="text-muted">
        Hold Ctrl (Windows) or Cmd (Mac) to select multiple attractions
    </p>
        <select name="attraction_id[]" class="form-control mb-3" multiple required>
            <?php while ($row = $attractions->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>">
                    <?= $row['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button class="btn btn-primary">Save Trip Plan</button>

    </form>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>