<?php
session_start();
require 'db_connect.php';
require 'flash.php';
require 'csrf.php';

header("Content-Type: application/json");

if (!csrf_validate($_POST['csrf_token'] ?? "")) {
    echo json_encode(["success" => false, "message" => "Invalid CSRF token."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$request_id = intval($_POST['id']);

$stmt = $pdo->prepare("SELECT status FROM travel_requests WHERE id = ? AND user_id = ?");
$stmt->execute([$request_id, $user_id]);
$req = $stmt->fetch();

if (!$req) {
    echo json_encode(["success" => false, "message" => "Request not found."]);
    exit;
}

if ($req['status'] !== "Pending") {
    echo json_encode(["success" => false, "message" => "Only pending requests can be withdrawn."]);
    exit;
}

$update = $pdo->prepare("UPDATE travel_requests SET status = 'Withdrawn' WHERE id = ?");
$update->execute([$request_id]);

echo json_encode(["success" => true, "message" => "Request withdrawn successfully."]);
exit;
?>
