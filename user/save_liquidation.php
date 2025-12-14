<?php
session_start();
require 'db_connect.php';
require 'log_liquidation.php';
header('Content-Type: text/plain');
// 🚫 Block save if request is Withdrawn
$stmt = $pdo->prepare("SELECT status FROM travel_requests WHERE id = ?");
$stmt->execute([$_POST['request_id']]);
$status = $stmt->fetchColumn();

if ($status === 'Withdrawn') {
    echo "ERROR|Request withdrawn";
    exit;
}

if (!isset($_SESSION['user_id'])) { http_response_code(401); echo "Unauthorized"; exit; }
if (!isset($_POST['request_id'])) { http_response_code(400); echo "Missing request_id"; exit; }

$request_id = (int) $_POST['request_id'];
$user_id = (int) $_SESSION['user_id'];
$pdf_path = $_POST['pdf_path'] ?? null;

try {
    // fetch request
    $q = $pdo->prepare("SELECT id, employee_id, total_cost, reason FROM travel_requests WHERE id = ?");
    $q->execute([$request_id]);
    $req = $q->fetch();
    if (!$req) throw new Exception("Travel Request not found.");

    // sum expenses from items
    $q2 = $pdo->prepare("SELECT COALESCE(SUM(amount),0) AS total_expense FROM liquidation_items WHERE request_id = ?");
    $q2->execute([$request_id]);
    $total_expense = (float)$q2->fetchColumn();

    $approved_total = (float)$req['total_cost'];
    $remaining = $approved_total - $total_expense;

    // insert summary report
    $ins = $pdo->prepare("INSERT INTO liquidation_reports (employee_id, request_id, total_amount, total_expense, remaining_balance, pdf_path, status, saved_by, remarks) VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?, ?)");
    $ins->execute([$req['employee_id'], $request_id, $approved_total, $total_expense, $remaining, $pdf_path, $user_id, 'Saved via UI']);
    $report_id = $pdo->lastInsertId();

    // update travel_requests liquidation fields
    $pdo->prepare("UPDATE travel_requests SET liquidation_total = ?, liquidation_balance = ?, last_modified_by = ?, last_modified_at = NOW() WHERE id = ?")
        ->execute([$approved_total, $remaining, $user_id, $request_id]);

    // log snapshot
    $payload = [
        'request_id' => $request_id,
        'total_amount' => $approved_total,
        'total_expense' => $total_expense,
        'remaining_balance' => $remaining,
        'pdf_path' => $pdf_path,
        'status' => 'Pending',
        'user_id' => $user_id,
        'employee_name' => null,
        'purpose' => $req['reason'] ?? null,
        'balance' => $remaining
    ];
    logLiquidation($pdo, (int)$report_id, $payload, 'Created');

    echo "OK|report_id:$report_id";
} catch (Exception $e) {
    http_response_code(500);
    echo "ERROR|" . $e->getMessage();
}
