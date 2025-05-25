<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "sql1.njit.edu";
$username   = "iea";
$password   = "Qwertyuiop12!@";
$dbname     = "iea";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<script>alert('Database connection failed.'); window.location.href='create_customer_info.html';</script>");
}

// Collect and sanitize POST data
$custId  = trim($_POST['custId'] ?? '');
$street  = trim($_POST['street'] ?? '');
$city    = trim($_POST['city'] ?? '');
$state   = strtoupper(trim($_POST['state'] ?? ''));
$zip     = trim($_POST['zip'] ?? '');
$phone   = trim($_POST['phone'] ?? '');

// Basic validation
if (!$custId || !$street || !$city || !$state || !$zip || !$phone) {
    echo "<script>alert('Missing required fields.'); window.location.href='create_customer_info.html';</script>";
    exit;
}

if (!preg_match('/^\d{5}$/', $zip)) {
    echo "<script>alert('Zip code must be 5 digits.'); window.location.href='create_customer_info.html';</script>";
    exit;
}

if (!preg_match('/^\d{3}[-.\s]?\d{3}[-.\s]?\d{4}$/', $phone)) {
    echo "<script>alert('Phone number must be a valid 10-digit number.'); window.location.href='create_customer_info.html';</script>";
    exit;
}

if (!preg_match('/^[A-Z]{2}$/', $state)) {
    echo "<script>alert('State must be 2 letters.'); window.location.href='create_customer_info.html';</script>";
    exit;
}

// Insert into the Customer_Personal_Information table
$sql = "INSERT INTO Customer_Personal_Information (
            Customer_ID, Customer_StreetAddress, Customer_City, 
            Customer_State, Customer_ZipCode, Customer_PhoneNumber
        ) VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<script>alert('Failed to prepare SQL.'); window.location.href='create_customer_info.html';</script>");
}

$stmt->bind_param("isssss", $custId, $street, $city, $state, $zip, $phone);

if ($stmt->execute()) {
    echo "<script>
        alert('Customer Information has been successfully saved.');
        window.location.href = 'create_customer_info.html';
    </script>";
} else {
    echo "<script>
        alert('Insert failed: " . $stmt->error . "');
        window.location.href = 'create_customer_info.html';
    </script>";
}

$stmt->close();
$conn->close();
?>

