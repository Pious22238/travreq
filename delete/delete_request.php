<?php
require 'auth_check.php';
require 'db_connect.php';

// Admin-only action
if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = (int) $_GET['id'];
    try {
        // Admin delete does NOT check for user_id, it deletes any request
        $stmt = $pdo->prepare("DELETE FROM travel_requests WHERE id = ?");
        $stmt->execute([$request_id]);
        header('Location: view_requests.php?message=deleted');
        exit();
    } catch (PDOException $e) {
        die("Database error: Could not delete request. " . $e->getMessage());
    }
} else {
    header('Location: view_requests.php?error=no_id');
    exit();
}
?>
