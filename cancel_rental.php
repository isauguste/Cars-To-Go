<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "sql1.njit.edu";
$username = "iea";
$password = "Qwertyuiop12!@";
$dbname = "iea";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<script>alert('Database connection failed.'); window.location.href='cancel_rental.html';</script>");
}

// If user confirmed cancellation
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes' && isset($_GET['carId'])) {
    $carId = intval($_GET['carId']);

    // Get customer and rep before delete
    $lookup = $conn->prepare("SELECT Customer_ID, Representative_ID FROM Rental_Car_Record WHERE Car_ID = ?");
    $lookup->bind_param("i", $carId);
    $lookup->execute();
    $lookup->bind_result($custId, $repId);
    $lookup->fetch();
    $lookup->close();

    // Delete from perks
    $conn->query("DELETE FROM Car_Rental_Perks_Record WHERE Car_ID = $carId");
    // Delete from date record
    $conn->query("DELETE FROM Rental_Date_Record WHERE Car_ID = $carId");
    // Delete rental record
    $conn->query("DELETE FROM Rental_Car_Record WHERE Car_ID = $carId");

    echo "<script>
        alert('Car Rental and Upgrades are Cancelled for Customer ID: $custId whose Representative is: $repId and whose CarID is: $carId');
        window.location.href = 'cancel_rental.html';
    </script>";
    exit;
}

// Form submission to validate Car ID
$carId = intval($_POST['carId'] ?? '');
if (!$carId) {
    echo "<script>alert('Invalid Car ID. Please enter a valid number.'); window.location.href='cancel_rental.html';</script>";
    exit;
}

$sql = "SELECT Customer_ID, Representative_ID FROM Rental_Car_Record WHERE Car_ID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "<script>alert('Query preparation failed.'); window.location.href='cancel_rental.html';</script>";
    exit;
}

$stmt->bind_param("i", $carId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "<script>alert('Car ID Does not Exist for the Customer. Please Check and Re-enter Car ID.'); window.location.href='cancel_rental.html';</script>";
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->bind_result($custId, $repId);
$stmt->fetch();
$stmt->close();

// JS confirmation prompt
echo "<script>
    if (confirm('You are about to CANCEL this car rental booking. Cancelling this car rental will also cancel any upgrades associated with the car rental. Are you sure you want to do so?')) {
        window.location.href = 'cancel_rental.php?confirm=yes&carId=$carId';
    } else {
        window.location.href = 'cancel_rental.html';
    }
</script>";

$conn->close();
?>

