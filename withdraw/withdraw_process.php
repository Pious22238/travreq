<?php
session_start();
require 'db_connect.php';

if (!isset($_POST['travel_request_id'], $_POST['reason'])) {
    die("Invalid submission.");
}

$travel_id = $_POST['travel_request_id'];
$user_id = $_SESSION['user_id'];
$reason = $_POST['reason'];

// Prevent duplicate pending
$check = $pdo->prepare("SELECT * FROM withdrawal_requests WHERE travel_request_id=? AND user_id=? AND status='Pending'");
$check->execute([$travel_id, $user_id]);

if ($check->rowCount() > 0) {
    die("You already have a pending withdrawal request.");
}

$stmt = $pdo->prepare("INSERT INTO withdrawal_requests (travel_request_id, user_id, reason) VALUES (?,?,?)");
$stmt->execute([$travel_id, $user_id, $reason]);

header("Location: user_withdrawals.php?success=1");
exit;
