<?php
// fetch_balances.php
require 'db_connect.php';

header('Content-Type: application/json');

// Fetch latest balances linked to employees
$stmt = $pdo->query("
    SELECT 
        e.id AS employee_id,
        e.full_name,
        COALESCE(u.remaining_balance, 0) AS remaining_balance
    FROM employees e
    LEFT JOIN users u ON e.id = u.employee_id
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
