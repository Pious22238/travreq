<?php
session_start();
require 'db_connect.php';
header('Content-Type: application/json');

// Check if request_id is provided
if (!isset($_POST['request_id'])) {
    echo json_encode(['error' => 'missing request_id']);
    exit;
}

$request_id = (int) $_POST['request_id'];

// 🚫 Block add if request is Withdrawn
$stmt = $pdo->prepare("SELECT status FROM travel_requests WHERE id = ?");
$stmt->execute([$request_id]);
$status = $stmt->fetchColumn();

if ($status === 'Withdrawn') {
    http_response_code(403);
    echo json_encode(['error' => 'Request withdrawn, cannot add expense']);
    exit;
}

// ✅ Insert new liquidation item
$stmt = $pdo->prepare("INSERT INTO liquidation_items (request_id, description, amount) VALUES (?, 'New Expense', 0.00)");
$stmt->execute([$request_id]);
$id = $pdo->lastInsertId();

// ✅ Return JSON with new item ID
echo json_encode(['id' => $id]);
