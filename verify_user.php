<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

$servername = "sql1.njit.edu";
$username = "iea";
$password = "Qwertyuiop12!@";
$dbname = "iea";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "DB connection failed: " . $conn->connect_error
    ]);
    exit;
}

// Get form data
$firstName = $_POST['fname'] ?? '';
$lastName = $_POST['Lname'] ?? '';
$repPassword = $_POST['pass'] ?? '';
$repID = $_POST['id'] ?? '';
$emailRequired = ($_POST['request'] ?? '') === 'on';
$emailInput = $_POST['email'] ?? '';

// SQL
$sql = "SELECT Representative_ID, Representative_FirstName, Representative_LastName 
        FROM Representative_Database 
        WHERE Representative_FirstName = ?
        AND Representative_LastName = ?
        AND Representative_Password = ?
        AND Representative_ID = ?";

if ($emailRequired) {
    $sql .= " AND Representative_Email = ?";
}

// Prepare and bind
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error" => "Statement preparation failed: " . $conn->error
    ]);
    exit;
}

if ($emailRequired) {
    $stmt->bind_param("sssss", $firstName, $lastName, $repPassword, $repID, $emailInput);
} else {
    $stmt->bind_param("ssss", $firstName, $lastName, $repPassword, $repID);
}

// Execute and bind results
if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "error" => "Query execution failed: " . $stmt->error
    ]);
    exit;
}

$stmt->store_result(); 
$stmt->bind_result($repId, $repFirst, $repLast);

if ($stmt->num_rows === 1 && $stmt->fetch()) {
    echo json_encode([
        "success" => true,
        "repId" => $repId,
        "repName" => $repFirst . " " . $repLast
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Representative not found or credentials are incorrect."
    ]);
}

$stmt->close();
$conn->close();
?>

