<?php
session_start();
require 'db_connect.php';
require 'flash.php';
require 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'msg'=>'Unauthorized']);
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success'=>false,'msg'=>'Invalid CSRF token']);
    exit;
}

$request_id = (int)($_POST['request_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Check ownership + status
$stmt = $pdo->prepare("
    SELECT status FROM travel_requests
    WHERE id=? AND employee_id=?
");
$stmt->execute([$request_id, $user_id]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode(['success'=>false,'msg'=>'Request not found']);
    exit;
}

if ($row['status'] !== 'Pending') {
    echo json_encode(['success'=>false,'msg'=>'Only Pending requests can be withdrawn']);
    exit;
}

// Withdraw
$stmt = $pdo->prepare("

    UPDATE travel_requests
    SET status='Withdrawn',
        last_modified_by=?,
        last_modified_at=NOW()
    WHERE id=?
    
");
$stmt->execute([$user_id, $request_id]);

echo json_encode(['success'=>true,'msg'=>'Request withdrawn successfully']);
