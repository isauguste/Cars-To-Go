<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
$servername = "sql1.njit.edu";
$username = "iea";
$password = "Qwertyuiop12!@";
$dbname = "iea";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Collect and trim form inputs
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$customerId = trim($_POST['customerId'] ?? '');

// Input validation
if (empty($firstName) || empty($lastName) || empty($customerId)) {
    echo "<script>
        alert('All fields are required. Please fill in all data.');
        window.location.href = 'create_customer.html';
    </script>";
    exit();
}

// Check if customer already exists
$sqlCheck = "SELECT Customer_ID FROM Customer WHERE Customer_ID = ?";
$stmtCheck = $conn->prepare($sqlCheck);

if (!$stmtCheck) {
    die("Prepare failed on SELECT: " . $conn->error);
}

$stmtCheck->bind_param("i", $customerId);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    echo "<script>
        alert('Customer Already Has an Account');
        window.location.href = 'create_customer.html';
    </script>";
    $stmtCheck->close();
    exit();
}
$stmtCheck->close();

// Insert into Customer_Account (correct column names)
$sqlInsert = "INSERT INTO Customer (Customer_ID, Customer_FirstName, Customer_LastName) VALUES (?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);

if (!$stmtInsert) {
    die("Prepare failed on INSERT: " . $conn->error);
}

$stmtInsert->bind_param("iss", $customerId, $firstName, $lastName);

if ($stmtInsert->execute()) {
    echo "<script>
        // Set sessionStorage on frontend after redirect
        alert('Customer Account Created. You will be redirected to a form to enter the Personal Information for the Customer.');
        sessionStorage.setItem('custId', '$customerId');
        window.location.href = 'create_customer_info.html';
    </script>";
} else {
    echo "<script>
        alert('Error creating account: " . $stmtInsert->error . "');
        window.location.href = 'create_customer.html';
    </script>";
}

$stmtInsert->close();
$conn->close();
?>

