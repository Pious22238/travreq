<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$stmt = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'");
echo json_encode(['count' => (int)$stmt->fetchColumn()]);
