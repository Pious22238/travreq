<?php
require 'db_connect.php';
require 'fpdf/fpdf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $total_balance = $_POST['total_balance'];
    $status = 'Pending';
    $created_at = date('Y-m-d H:i:s');

    // Save to DB
    $stmt = $pdo->prepare("
        INSERT INTO liquidation_reports (request_id, total_balance, status, created_at)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$request_id, $total_balance, $status, $created_at]);

    $liquidation_id = $pdo->lastInsertId();

    // Fetch employee info
    $q = $pdo->prepare("
        SELECT e.full_name, e.department, tr.reason
        FROM travel_requests tr
        JOIN employees e ON tr.employee_id = e.id
        WHERE tr.id = ?
    ");
    $q->execute([$request_id]);
    $info = $q->fetch(PDO::FETCH_ASSOC);

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Liquidation Report',0,1,'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(50,10,'Employee: '.$info['full_name'],0,1);
    $pdf->Cell(50,10,'Department: '.$info['department'],0,1);
    $pdf->Cell(50,10,'Reason: '.$info['reason'],0,1);
    $pdf->Cell(50,10,'Total Balance: ₱'.number_format($total_balance,2),0,1);
    $pdf->Cell(50,10,'Date: '.$created_at,0,1);

    $pdf_path = "pdfs/liquidation_{$liquidation_id}.pdf";
    if (!file_exists('pdfs')) mkdir('pdfs');
    $pdf->Output('F', $pdf_path);

    // Save PDF path
    $pdo->prepare("UPDATE liquidation_reports SET pdf_path=? WHERE id=?")
        ->execute([$pdf_path, $liquidation_id]);

    echo "<script>alert('Liquidation saved successfully!'); window.location.href='withdrawal_requests.php';</script>";
}
?>
