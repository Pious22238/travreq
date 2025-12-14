<?php
require 'auth_check.php';
require 'db_connect.php';

if ($_SESSION['role'] === 'admin') {
    $stmt = $pdo->query("
        SELECT tr.id, e.full_name AS employee_name, e.email, e.department, tr.reason, 
               tr.departure_date, tr.return_date, d.destination_name, d.country, tr.total_cost, tr.status
        FROM travel_requests tr
        JOIN employees e ON tr.employee_id = e.id
        LEFT JOIN destinations d ON tr.destination_id = d.id
        ORDER BY tr.id DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT tr.id, e.full_name AS employee_name, e.email, e.department, tr.reason, 
               tr.departure_date, tr.return_date, d.destination_name, d.country, tr.total_cost, tr.status
        FROM travel_requests tr
        JOIN employees e ON tr.employee_id = e.id
        LEFT JOIN destinations d ON tr.destination_id = d.id
        WHERE tr.employee_id = ?
        ORDER BY tr.id DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
}
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($requests as $r) {
    echo "<tr>
        <td>{$r['id']}</td>
        <td>{$r['employee_name']}</td>
        <td>{$r['email']}</td>
        <td>{$r['department']}</td>
        <td>{$r['reason']}</td>
        <td>{$r['destination_name']}, {$r['country']}</td>
        <td>{$r['departure_date']}</td>
        <td>{$r['return_date']}</td>
        <td>₱" . number_format($r['total_cost'], 2) . "</td>
        <td class='status-" . strtolower($r['status']) . "'>{$r['status']}</td>
        <td>
            <a href='view_request_details.php?id={$r['id']}' class='btn'>🔍 View</a>
            <a href='liquidation.php?id={$r['id']}' class='btn btn-liquidation'>💰 Liquidation</a>";
    if ($_SESSION['role'] === 'admin') {
        echo "<button class='btn approve' data-id='{$r['id']}' data-action='Approved'>✅</button>
              <button class='btn reject' data-id='{$r['id']}' data-action='Rejected'>❌</button>
              <button class='btn pending' data-id='{$r['id']}' data-action='Pending'>⏳</button>
              <button class='btn btn-delete delete' data-id='{$r['id']}' data-action='Delete'>🗑️</button>";
    }
    echo "</td></tr>";
}
?>
