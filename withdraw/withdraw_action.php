<?php
session_start();
require 'db_connect.php';

$id = $_POST['id'];
$status = $_POST['status'];

$stmt = $pdo->prepare("UPDATE withdrawal_requests SET status=?, admin_remarks=? WHERE id=?");
$stmt->execute([$status, "$status by admin", $id]);

echo "OK";
