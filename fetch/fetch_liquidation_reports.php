<?php
require 'db_connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

// Detect if accessed directly (not via AJAX)
$access_direct = !empty($_SERVER['REQUEST_METHOD']) 
                 && basename($_SERVER['PHP_SELF']) === 'fetch_liquidations.php';

// If direct access → print table header
if ($access_direct) {
    echo "<table border='1' cellpadding='8' cellspacing='0'>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Created At</th>
                <th>PDF</th>
                <th>Actions</th>
            </tr>";
}

$stmt = $pdo->prepare("
    SELECT 
        lr.id AS liquidation_id,
        e.full_name AS employee_name,
        tr.reason,
        lr.status,
        lr.pdf_path,
        lr.created_at
    FROM liquidation_reports lr
    JOIN travel_requests tr ON lr.request_id = tr.id
    JOIN employees e ON tr.employee_id = e.id
    WHERE lr.saved_by = ?
    ORDER BY lr.id DESC
");
$stmt->execute([$user_id]);

while ($report = $stmt->fetch(PDO::FETCH_ASSOC)) {

    echo "<tr>
        <td>{$report['liquidation_id']}</td>
        <td>{$report['employee_name']}</td>
        <td>{$report['reason']}</td>
        <td>";

    if ($report['status'] === 'Approved') echo "<span style='color:green;'>Approved</span>";
    elseif ($report['status'] === 'Rejected') echo "<span style='color:red;'>Rejected</span>";
    else echo "<span style='color:orange;'>Pending</span>";

    echo "</td>
        <td>{$report['created_at']}</td>
        <td>";

    if (!empty($report['pdf_path']))
        echo "<a href='{$report['pdf_path']}' target='_blank'>View PDF</a>";
    else
        echo "<em>No PDF</em>";

    echo "</td>
        <td>";

    if ($report['status'] == 'Pending')
        echo "<button onclick='updateStatus({$report['liquidation_id']},\"Approved\")'>Approve</button>
              <button onclick='updateStatus({$report['liquidation_id']},\"Rejected\")'>Reject</button>";
    else
        echo "N/A";

    echo "</td></tr>";
}

// Close table if direct access
if ($access_direct) {
    echo "</table>";
}
?>
