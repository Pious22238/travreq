<?php
require 'auth_check.php';
require 'db_connect.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Missing request ID.");
}

$request_id = $_GET['id'];

// Fetch request
$stmt = $pdo->prepare("
    SELECT tr.id, d.destination_name, d.country, tr.status
    FROM travel_requests tr
    JOIN destinations d ON tr.destination_id = d.id
    WHERE tr.id = ? AND tr.user_id = ?
");
$stmt->execute([$request_id, $user_id]);
$request = $stmt->fetch();

if (!$request) {
    die("Request not found.");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Withdraw Travel Request</title>
</head>
<body>

<h2>Withdraw Travel Request</h2>

<p><strong>Destination:</strong> <?= htmlspecialchars($request['destination_name']) ?>, <?= htmlspecialchars($request['country']) ?></p>
<p><strong>Current Status:</strong> <?= htmlspecialchars($request['status']) ?></p>

<form method="POST" action="submit_withdraw_request.php">
    <input type="hidden" name="request_id" value="<?= $request_id ?>">

    <label>Reason for Withdrawal:</label><br>
    <textarea name="withdrawal_reason" required rows="4" cols="40"></textarea><br><br>

    <button type="submit">Submit Withdrawal</button>
</form>

</body>
</html>
