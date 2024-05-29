<?php
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No ID specified']);
    exit();
}

$id = intval($_GET['id']);

$mysqli = new mysqli("localhost", "root", "", "webhook");

if ($mysqli->connect_errno) {
    echo json_encode(['error' => 'Failed to connect to MySQL: ' . $mysqli->connect_error]);
    exit();
}

$stmt = $mysqli->prepare("UPDATE form_data SET status=1 WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to remove notification: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
