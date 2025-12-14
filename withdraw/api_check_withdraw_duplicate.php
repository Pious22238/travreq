<?php
// api_check_withdraw_duplicate.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error'=>'not_logged_in']);
    exit;
}
$uid = (int)$_SESSION['user_id'];
$tid = isset($_GET['travel_request_id']) ? (int)$_GET['travel_request_id'] : 0;
if ($tid <= 0) { echo json_encode(['exists'=>false]); exit; }

$stmt = $pdo->prepare("SELECT COUNT(*) FROM withdrawal_requests WHERE travel_request_id = ? AND user_id = ? AND status = 'Pending'");
$stmt->execute([$tid, $uid]);
$exists = $stmt->fetchColumn() > 0;
echo json_encode(['exists' => (bool)$exists]);
