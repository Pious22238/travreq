<?php
require 'db_connect.php';
$employeeId = intval($_GET['employee_id'] ?? 0);
$response = ['success' => false, 'balance' => 0];

if ($employeeId > 0) {
    $stmt = $pdo->prepare("SELECT remaining_balance FROM users WHERE id = :eid");
    $stmt->execute([':eid'=>$employeeId]);
    $balance = $stmt->fetchColumn();
    if ($balance !== false) {
        $response['success'] = true;
        $response['balance'] = floatval($balance);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
