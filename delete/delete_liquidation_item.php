<?php
session_start();
require 'db_connect.php';
$id = $_POST['id'] ?? null;
if (!$id) { echo "Missing id"; exit; }
$pdo->prepare("DELETE FROM liquidation_items WHERE id = ?")->execute([$id]);
echo "OK";
