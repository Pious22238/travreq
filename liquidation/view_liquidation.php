<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Fetch all requests and related liquidation info
$stmt = $pdo->prepare("
    SELECT tr.id AS request_id,
           e.full_name AS employee_name,
           d.destination_name AS destination,
           tr.total_cost AS approved_budget,
           COALESCE(l.total_amount, 0) AS total_spent,
           COALESCE(l.balance, 0) AS balance,
           l.updated_at
    FROM travel_requests tr
    LEFT JOIN employees e ON tr.employee_id = e.id
    LEFT JOIN destinations d ON tr.destination_id = d.id
    LEFT JOIN liquidations l ON tr.id = l.request_id
    ORDER BY tr.id DESC
");
$stmt->execute();
$liquidations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Liquidation Overview</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #000;
    color: #fff;
    font-family: 'Poppins', sans-serif;
}
.table {
    background: rgba(255,255,255,0.05);
}
.btn-view {
    background: linear-gradient(90deg, #007bff, #00bfff);
    border: none;
    color: #fff;
    border-radius: 20px;
    padding: 5px 12px;
}
.btn-view:hover {
    box-shadow: 0 0 15px #00bfff;
}
</style>
</head>
<body class="p-5">
<h2 class="text-center mb-4">📊 Liquidation Overview</h2>

<table class="table table-bordered text-white">
    <thead>
        <tr>
            <th>Traveler</th>
            <th>Destination</th>
            <th>Approved Budget</th>
            <th>Total Spent</th>
            <th>Balance</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($liquidations as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['employee_name']) ?></td>
            <td><?= htmlspecialchars($row['destination']) ?></td>
            <td>₱<?= number_format($row['approved_budget'], 2) ?></td>
            <td>₱<?= number_format($row['total_spent'], 2) ?></td>
            <td><?= $row['balance'] < 0 ? '<span style="color:#ff6666;">₱'.number_format($row['balance'],2).'</span>' : '₱'.number_format($row['balance'],2) ?></td>
            <td><?= $row['updated_at'] ?: '-' ?></td>
            <td><a href="liquidation.php?id=<?= $row['request_id'] ?>" class="btn-view btn btn-sm">View</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<a href="view_requests.php" class="btn btn-secondary mt-4">⬅️ Back to Dashboard</a>
</body>
</html>
