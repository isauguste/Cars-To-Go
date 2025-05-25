<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("sql1.njit.edu", "iea", "Qwertyuiop12!@", "iea");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$fname = trim($_POST['fname'] ?? '');
$lname = trim($_POST['lname'] ?? '');
$custID = trim($_POST['custID'] ?? '');

if (empty($fname) || empty($lname) || empty($custID)) {
    echo json_encode(["error" => "Missing fields"]);
    exit;
}

$sql = "SELECT * FROM Customer WHERE Customer_FirstName = ? AND Customer_LastName = ? AND Customer_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $fname, $lname, $custID);
$stmt->execute();
$stmt->store_result();

echo json_encode(["found" => $stmt->num_rows > 0]);

$stmt->close();
$conn->close();
?>

