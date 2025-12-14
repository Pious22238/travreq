<?php
session_start();
require 'db_connect.php';

if (!isset($_GET['file'])) {
    echo "No PDF file specified.";
    exit;
}

$file = basename($_GET['file']);
$filePath = "pdfs/" . $file;

if (!file_exists($filePath)) {
    echo "File not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Liquidation Report PDF</title>
<style>
body {
    background: #0b1220;
    color: #e6e9ef;
    font-family: 'Poppins', sans-serif;
    text-align: center;
    padding: 20px;
}
iframe {
    width: 90%;
    height: 90vh;
    border: 2px solid #4db8ff;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(77,184,255,0.2);
}
</style>
</head>
<body>
    <h2>📄 Liquidation Report</h2>
    <iframe src="<?= htmlspecialchars($filePath) ?>"></iframe>
</body>
</html>
