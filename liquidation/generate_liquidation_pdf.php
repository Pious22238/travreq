<?php
require 'db_connect.php';
require_once __DIR__ . '/vendor/autoload.php'; // ✅ make sure you have dompdf installed

use Dompdf\Dompdf;

if (!isset($_GET['id'])) {
    die('No liquidation report ID provided');
}

$id = $_GET['id'];

// ✅ Fetch liquidation report and details
$stmt = $pdo->prepare("
    SELECT lr.*, e.full_name, tr.id AS request_id, d.destination_name, tr.total_cost
    FROM liquidation_reports lr
    JOIN employees e ON lr.employee_id = e.id
    JOIN travel_requests tr ON lr.request_id = tr.id
    LEFT JOIN destinations d ON tr.destination_id = d.id
    WHERE lr.id = ?
");
$stmt->execute([$id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) die('Report not found.');

// ✅ Fetch detailed items
$stmt = $pdo->prepare("SELECT * FROM liquidation_items WHERE request_id = ?");
$stmt->execute([$report['request_id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<style>
body { font-family: DejaVu Sans, sans-serif; }
h2 { color: #007bff; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #999; padding: 8px; text-align: left; }
th { background: #007bff; color: white; }
</style>
<h2>Liquidation Report</h2>
<p><strong>Employee:</strong> '.$report['full_name'].'</p>
<p><strong>Destination:</strong> '.$report['destination_name'].'</p>
<p><strong>Approved Budget:</strong> ₱'.number_format($report['total_cost'],2).'</p>
<p><strong>Total Amount:</strong> ₱'.number_format($report['total_amount'],2).'</p>
<p><strong>Status:</strong> '.$report['status'].'</p>

<table>
<tr><th>Description</th><th>Amount (₱)</th></tr>';

foreach ($items as $i) {
    $html .= '<tr><td>'.htmlspecialchars($i['description']).'</td><td>'.number_format($i['amount'],2).'</td></tr>';
}
$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('liquidation_report_'.$id.'.pdf', ["Attachment" => false]);
?>
