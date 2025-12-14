<?php
require 'db_connect.php';

// ✅ Fetch all employees
$stmt = $pdo->query("SELECT id, full_name, email, department, position FROM employees ORDER BY id DESC");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($employees);
?>
