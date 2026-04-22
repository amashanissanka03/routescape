<?php
session_start();
require_once "../config/db.php";

// allow only admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$edit_data = null;

// DELETE attraction
if (isset($_POST["delete_id"])) {
    $id = $_POST["delete_id"];

    // delete related trip rows first
    $stmt = $conn->prepare("DELETE FROM trip_plan WHERE attraction_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // then delete attraction
    $stmt = $conn->prepare("DELETE FROM attractions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: manage_attractions.php");
    exit();
}

// LOAD attraction for editing
if (isset($_GET["edit_id"])) {
    $id = $_GET["edit_id"];

    $stmt = $conn->prepare("SELECT * FROM attractions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}

// ADD or UPDATE attraction
if (isset($_POST["name"])) {
    $name = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $description = trim($_POST["description"]);
    $details = trim($_POST["details"]);
    $image = trim($_POST["image"]);
    $latitude = trim($_POST["latitude"]);
    $longitude = trim($_POST["longitude"]);

    if (!empty($_POST["edit_id"])) {
        // UPDATE existing row
        $id = $_POST["edit_id"];

        $stmt = $conn->prepare("
            UPDATE attractions
            SET name = ?, category = ?, description = ?, details = ?, image = ?, latitude = ?, longitude = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sssssssi", $name, $category, $description, $details, $image, $latitude, $longitude, $id);
        $stmt->execute();
    } else {
        // INSERT new row
        $stmt = $conn->prepare("
            INSERT INTO attractions (name, category, description, details, image, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $name, $category, $description, $details, $image, $latitude, $longitude);
        $stmt->execute();
    }

    header("Location: manage_attractions.php");
    exit();
}

// fetch attractions list
$result = $conn->query("SELECT * FROM attractions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Attractions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <h3>Manage Attractions</h3>

    <form method="POST" class="mb-4">

        <?php if ($edit_data): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>

        <input type="text" name="name" class="form-control mb-2"
               placeholder="Name"
               value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" required>

        <input type="text" name="category" class="form-control mb-2"
               placeholder="Category"
               value="<?= htmlspecialchars($edit_data['category'] ?? '') ?>" required>

        <textarea name="description" class="form-control mb-2" placeholder="Short description" required><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>

        <textarea name="details" class="form-control mb-2" placeholder="Full details for popup" rows="6"><?= htmlspecialchars($edit_data['details'] ?? '') ?></textarea>

        <input type="text" name="image" class="form-control mb-2"
               placeholder="Image filename (e.g. beach.jpg)"
               value="<?= htmlspecialchars($edit_data['image'] ?? '') ?>" required>

        <input type="text" name="latitude" class="form-control mb-2"
               placeholder="Latitude"
               value="<?= htmlspecialchars($edit_data['latitude'] ?? '') ?>">

        <input type="text" name="longitude" class="form-control mb-2"
               placeholder="Longitude"
               value="<?= htmlspecialchars($edit_data['longitude'] ?? '') ?>">

        <button class="btn btn-success">
            <?= $edit_data ? "Update Attraction" : "Add Attraction" ?>
        </button>

        <?php if ($edit_data): ?>
            <a href="manage_attractions.php" class="btn btn-secondary">Cancel Edit</a>
        <?php endif; ?>
    </form>

    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Image</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["name"]) ?></td>
                <td><?= htmlspecialchars($row["category"]) ?></td>
                <td><?= htmlspecialchars($row["image"]) ?></td>
                <td>
                    <a href="manage_attractions.php?edit_id=<?= $row["id"] ?>" class="btn btn-warning btn-sm mb-1">
                        Edit
                    </a>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $row["id"] ?>">
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this attraction?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>