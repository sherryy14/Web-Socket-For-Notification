<?php
header('Content-Type: application/json');

// Connect to MySQL database
$mysqli = new mysqli("localhost", "root", "", "webhook");

if ($mysqli->connect_errno) {
    echo json_encode(["error" => "Failed to connect to MySQL: " . $mysqli->connect_error]);
    exit();
}

// Fetch data from database
$result = $mysqli->query("SELECT * FROM form_data WHERE status=0");

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $result->free();
}

// Close MySQL connection
$mysqli->close();

// Return data as JSON
echo json_encode($data);