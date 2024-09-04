<?php

$servername = "localhost";
$dbname = "pm25";
$username = "root";
$password = "";


// Keep this API Key value to be compatible with the ESP32 code provided in the project page. 
// If you change this value, the ESP32 sketch needs to match
$api_key_value = "tPmAT5Ab3j7F9";
$api_key = "";
$sensor = 0;
$fan = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_REQUEST["api_key"]);

    if ($api_key == $api_key_value) {
        $sensor = test_input($_REQUEST["sensor"]);
        $fan = test_input($_REQUEST["fan"]);
        $stat_udara = test_input($_REQUEST["status"]);

        echo $fan;

        $mysqli = new mysqli($servername, $username, $password, $dbname);
        if ($mysqli->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO data_sensor (PM25, FAN, STATUS_UDARA) VALUES ('" . $sensor . "', '" . $fan . "', '" . $stat_udara . "')";
        if ($mysqli->query($sql) == TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }
        $mysqli->close();


    } else {
        echo "Wrong API Key provided.";
    }

} else {
    echo "No data posted with HTTP POST";
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}