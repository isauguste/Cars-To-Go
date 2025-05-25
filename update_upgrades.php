<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "sql1.njit.edu";
$username = "iea";
$password = "Qwertyuiop12!@";
$dbname = "iea";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$carId = trim($_POST['carId'] ?? '');
$insurance = trim($_POST['insurance'] ?? '');
$features = trim($_POST['features'] ?? '');
$drivers = trim($_POST['drivers'] ?? '');

if (!$carId) {
    echo "<script>alert('Car ID is required.'); window.history.back();</script>";
    exit;
}

$checkSql = "SELECT 1 FROM Car_Rental_Perks_Record WHERE Car_ID = ?";
$checkStmt = $conn->prepare($checkSql);
if (!$checkStmt) {
    die("Prepare failed on SELECT: " . $conn->error);
}
$checkStmt->bind_param("i", $carId);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    echo "<script>alert('The Data Entered for Car ID is incorrect or Upgrades were never requested. Please check the Car ID for correctness.'); window.history.back();</script>";
    $checkStmt->close();
    $conn->close();
    exit;
}

$checkStmt->close();

$updates = [];
$params = [];
$types = "";

if (!empty($insurance)) {
    $updates[] = "Insurance = ?";
    $params[] = $insurance;
    $types .= "s";
}
if (!empty($features)) {
    $updates[] = "Additional_Features = ?";
    $params[] = $features;
    $types .= "s";
}
if (!empty($drivers)) {
    $updates[] = "Additional_Drivers = ?";
    $params[] = $drivers;
    $types .= "s";
}

if (empty($updates)) {
    echo "<script>alert('No fields provided to update.'); window.history.back();</script>";
    $conn->close();
    exit;
}

$setClause = implode(", ", $updates);
$updateSql = "UPDATE Car_Rental_Perks_Record SET $setClause WHERE Car_ID = ?";
$params[] = $carId;
$types .= "i";

$updateStmt = $conn->prepare($updateSql);
if (!$updateStmt) {
    die("Prepare failed on UPDATE: " . $conn->error);
}

$updateStmt->bind_param($types, ...$params);

if ($updateStmt->execute()) {
    $updatedFields = [];
    if (!empty($insurance)) $updatedFields[] = "Insurance";
    if (!empty($features)) $updatedFields[] = "Upgrade Features";
    if (!empty($drivers)) $updatedFields[] = "Additional Driver";

    $updatesDone = implode(", ", $updatedFields);
    echo "<script>
        alert('Customer $updatesDone Updated.');
        window.location.href = 'update_upgrades.html';
    </script>";
} else {
    echo "<script>alert('Failed to update record.'); window.history.back();</script>";
}

$updateStmt->close();
$conn->close();
?>

