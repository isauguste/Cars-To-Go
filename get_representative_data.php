<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "sql1.njit.edu";
$username = "iea";
$password = "Qwertyuiop12!@";
$dbname = "iea";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$repId = $_POST['repId'] ?? '';
$repName = trim($_POST['repName'] ?? '');

if (empty($repId)) {
    die("Representative ID is required.");
}

$sql = "
SELECT 
    r.Representative_FirstName,
    r.Representative_LastName,
    r.Representative_ID,
    r.Representative_PhoneNumber,
    r.Representative_Email,

    c.Customer_FirstName,
    c.Customer_LastName,
    c.Customer_ID,
    ci.Customer_StreetAddress,
    ci.Customer_City,
    ci.Customer_State,
    ci.Customer_ZipCode,
    ci.Customer_PhoneNumber,

    cr.Car_Type,
    cr.`Make/Model`,
    cr.Car_ID,

    rd.Start_Date,
    rd.End_Date,

    p.Insurance,
    p.Additional_Features,
    p.Additional_Drivers

FROM Representative_Database r
JOIN Rental_Car_Record cr ON r.Representative_ID = cr.Representative_ID
JOIN Customer c ON cr.Customer_ID = c.Customer_ID
JOIN Customer_Personal_Information ci ON c.Customer_ID = ci.Customer_ID
JOIN Rental_Date_Record rd ON cr.Car_ID = rd.Car_ID
JOIN Car_Rental_Perks_Record p ON cr.Car_ID = p.Car_ID
WHERE r.Representative_ID = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("s", $repId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "<p>No records found for this representative.</p>";
    exit;
}

$stmt->bind_result(
    $repFirst, $repLast, $repID, $repPhone, $repEmail,
    $custFirst, $custLast, $custID, $street, $city, $state, $zip, $custPhone,
    $carType, $makeModel, $carID,
    $startDate, $endDate,
    $insurance, $features, $drivers
);

// Output styling
echo "<style>
    body {
        font-family: Arial, sans-serif;
        background: url('https://cimg6.ibsrv.net/gimg/www.flyertalk.com-vbulletin/2000x1124/20230503_200214_0f8b39fae81fb48ad0d0bd05adb1bfa89bbb1399.jpg') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 0;
    }
    .table-container {
        margin: 60px auto 30px auto;
        width: 95%;
        overflow-x: auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
        padding: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        text-align: center;
    }
    th, td {
        padding: 10px;
        border: 1px solid #999;
    }
    th {
        background-color: #004d00;
        color: white;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .back-btn-container {
        text-align: center;
        margin-bottom: 50px;
    }
    .back-btn {
        background-color: darkgreen;
        color: white;
        padding: 12px 24px;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }
    .back-btn:hover {
        background-color: green;
    }
</style>";

// Output the table
echo "<div class='table-container'>";
echo "<table>";
echo "<tr>
    <th>Rep ID</th><th>Rep First</th><th>Rep Last</th><th>Rep Phone</th><th>Rep Email</th>
    <th>Customer First</th><th>Customer Last</th><th>Customer ID</th>
    <th>Street</th><th>City</th><th>State</th><th>Zip</th><th>Customer Phone</th>
    <th>Car Type</th><th>Make/Model</th><th>Car ID</th>
    <th>Start Date</th><th>End Date</th>
    <th>Insurance</th><th>Upgrades</th><th>Additional Driver</th>
</tr>";

while ($stmt->fetch()) {
    echo "<tr>
        <td>$repID</td><td>$repFirst</td><td>$repLast</td><td>$repPhone</td><td>$repEmail</td>
        <td>$custFirst</td><td>$custLast</td><td>$custID</td>
        <td>$street</td><td>$city</td><td>$state</td><td>$zip</td><td>$custPhone</td>
        <td>$carType</td><td>$makeModel</td><td>$carID</td>
        <td>$startDate</td><td>$endDate</td>
        <td>$insurance</td><td>$features</td><td>$drivers</td>
    </tr>";
}
echo "</table></div>";

// Output back button
echo "<div class='back-btn-container'>
        <a href='Project1.html' class='back-btn'>Back to Login</a>
    </div>";

$stmt->close();
$conn->close();
?> 

