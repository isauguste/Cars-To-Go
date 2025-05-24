<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "sql1.njit.edu";
$username = "iea";
$password = "Qwertyuiop12!@";
$dbname = "iea";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<script>alert('Database connection failed.'); window.location.href='book_rental.html';</script>");
}

$repId = trim($_POST['repId'] ?? '');
$custId = trim($_POST['custId'] ?? '');
$carType = trim($_POST['carType'] ?? '');
$makeModel = trim($_POST['makeModel'] ?? '');
$startDate = trim($_POST['startDate'] ?? '');
$endDate = trim($_POST['endDate'] ?? '');

if (!$repId || !$custId || !$carType || !$makeModel || !$startDate || !$endDate) {
    echo "<script>alert('Missing required information. Please ensure all fields are filled.'); window.location.href='book_rental.html';</script>";
    exit;
}

$carId = rand(1000, 9999);

// Insert car record
$carSql = "INSERT INTO Rental_Car_Record (Car_ID, Customer_ID, Car_Type, `Make/Model`, Representative_ID)
           VALUES (?, ?, ?, ?, ?)";
$carStmt = $conn->prepare($carSql);
$carStmt->bind_param("issss", $carId, $custId, $carType, $makeModel, $repId);
if (!$carStmt->execute()) {
    $error = $conn->error;
    echo "<script>alert('Failed to insert car record: $error'); window.location.href='book_rental.html';</script>";
    exit;
}
$carStmt->close();

// Insert rental dates
$dateSql = "INSERT INTO Rental_Date_Record (Car_ID, Start_Date, End_Date) VALUES (?, ?, ?)";
$dateStmt = $conn->prepare($dateSql);
$dateStmt->bind_param("iss", $carId, $startDate, $endDate);
if (!$dateStmt->execute()) {
    $error = $conn->error;
    echo "<script>alert('Failed to insert date record: $error'); window.location.href='book_rental.html';</script>";
    exit;
}
$dateStmt->close();

$conn->close();

// Store carId and ask about upgrades
echo "
<script>
    alert('Car Rental Booked. Your Car ID is: $carId');
    sessionStorage.setItem('carId', '$carId');
    if (confirm('Would you like to add upgrades to your Rental Car?')) {
        window.location.href = 'request_upgrades.html';
    } else {
        window.location.href = 'book_rental.html';
    }
</script>";
?>

