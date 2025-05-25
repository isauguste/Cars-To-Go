<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB Credentials
$servername = "sql1.njit.edu";
$username = "iea";
$password = "Qwertyuiop12!@";
$dbname = "iea";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "<script>alert('Database connection failed.'); window.history.back();</script>";
    exit;
}

// Collect POST inputs
$carId = trim($_POST['carId'] ?? '');
$insurance = trim($_POST['insurance'] ?? '');
$features = trim($_POST['features'] ?? '');
$drivers = trim($_POST['drivers'] ?? '');

// Validate input
if (!$carId || !$insurance || !$features || !$drivers) {
    echo "<script>alert('All fields are required. Please complete the form.'); window.history.back();</script>";
    exit;
}

// Check if rental exists
$checkRental = $conn->prepare("SELECT Car_ID FROM Rental_Car_Record WHERE Car_ID = ?");
$checkRental->bind_param("i", $carId);
$checkRental->execute();
$checkRental->store_result();

if ($checkRental->num_rows === 0) {
    echo "<script>
        alert('RENTAL INFORMATION CANNOT BE FOUND. RECHECK DATA ENTERED OTHERWISE YOU NEED TO MAKE SURE THE CUSTOMER HAS RENTED A CAR.');
        window.location.href = 'request_upgrades.html';
    </script>";
    $checkRental->close();
    $conn->close();
    exit;
}
$checkRental->close();

// Check if upgrade already exists
$checkUpgrade = $conn->prepare("SELECT Car_ID FROM Car_Rental_Perks_Record WHERE Car_ID = ?");
$checkUpgrade->bind_param("i", $carId);
$checkUpgrade->execute();
$checkUpgrade->store_result();

if ($checkUpgrade->num_rows > 0) {
    // Update existing upgrade
    $update = $conn->prepare("UPDATE Car_Rental_Perks_Record SET Insurance = ?, Additional_Features = ?, Additional_Drivers = ? WHERE Car_ID = ?");
    $update->bind_param("sssi", $insurance, $features, $drivers, $carId);
    if ($update->execute()) {
        echo "<script>
            alert('UpGrades added to your Rental.');
            window.location.href = 'book_rental.html';
        </script>";
    } else {
        echo "<script>
            alert('Failed to update existing upgrade record.');
            window.history.back();
        </script>";
    }
    $update->close();
} else {
    // Insert new upgrade
    $insert = $conn->prepare("INSERT INTO Car_Rental_Perks_Record (Car_ID, Insurance, Additional_Features, Additional_Drivers) VALUES (?, ?, ?, ?)");
    $insert->bind_param("isss", $carId, $insurance, $features, $drivers);
    if ($insert->execute()) {
        echo "<script>
            alert('UpGrades added to your Rental.');
            window.location.href = 'book_rental.html';
        </script>";
    } else {
        echo "<script>
            alert('Failed to insert upgrade: " . $conn->error . "');
            window.history.back();
        </script>";
    }
    $insert->close();
}

$checkUpgrade->close();
$conn->close();
?>

