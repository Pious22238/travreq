<?php
include 'db_connect.php';

$sql = "SELECT lr.id,
               COALESCE(e.full_name, 'Unknown') AS employee_name,
               COALESCE(tr.reason, 'N/A') AS travel_reason,
               lr.expense_description,
               lr.expense_date,
               lr.amount,
               lr.status
        FROM liquidation_reports lr
        LEFT JOIN employees e ON lr.employee_id = e.id
        LEFT JOIN travel_requests tr ON lr.request_id = tr.id
        ORDER BY lr.id DESC";

$stmt = $pdo->query($sql);

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $statusClass = match ($row['status']) {
            'Approved' => 'badge-approved',
            'Rejected' => 'badge-rejected',
            default    => 'badge-pending'
        };

        $buttons = ($row['status'] === 'Pending') ?
            "<button class='btn-approve' data-id='{$row['id']}' data-status='Approved'>Approve</button>
             <button class='btn-reject' data-id='{$row['id']}' data-status='Rejected'>Reject</button>"
            : "<em>N/A</em>";

        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['employee_name']}</td>
                <td>{$row['travel_reason']}</td>
                <td>{$row['expense_description']}</td>
                <td>{$row['expense_date']}</td>
                <td>₱" . number_format($row['amount'], 2) . "</td>
                <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                <td>{$buttons}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No liquidation reports found.</td></tr>";
}
?>
