<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

// Connect to the database (adjust the credentials accordingly)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pm25";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    echo "data: {\"error\": \"Connection failed: " . $conn->connect_error . "\"}\n\n";
    exit();
}

// Query to fetch the latest data from the sensor_data table
$sql = "SELECT WAKTU, PM25, STATUS_UDARA, FAN FROM DATA_SENSOR ORDER BY WAKTU DESC LIMIT 20";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "data: {\"error\": \"No data found\"}\n\n";
    exit();
}

// Send the data
echo "data: " . json_encode($data) . "\n\n";

// Close the connection
$conn->close();

flush();
?>