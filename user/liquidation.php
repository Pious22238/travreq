<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No request selected.";
    exit;
}

$request_id = $_GET['id'];

// ✅ Fetch request details
$stmt = $pdo->prepare("
    SELECT tr.*, 
           e.full_name AS employee_name, 
           d.destination_name AS destination, 
           tr.total_cost AS approved_budget
    FROM travel_requests tr
    LEFT JOIN employees e ON tr.employee_id = e.id
    LEFT JOIN destinations d ON tr.destination_id = d.id
    WHERE tr.id = ?
");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    echo "Request not found.";
    exit;
}
// 🚫 BLOCK LIQUIDATION IF REQUEST IS WITHDRAWN
if ($request['status'] === 'Withdrawn') {
    echo "
    <div style='
        margin:120px auto;
        max-width:600px;
        padding:30px;
        background:rgba(255,0,0,0.15);
        border:1px solid rgba(255,80,80,0.6);
        color:#ffb3b3;
        text-align:center;
        border-radius:15px;
        box-shadow:0 0 25px rgba(255,80,80,0.6);
        font-size:18px;
        font-family:Poppins,Segoe UI;
    '>
        🚫 <b>Liquidation Disabled</b><br><br>
        This travel request has been <b>WITHDRAWN</b>.<br>
        No liquidation actions are allowed.
        <br><br>
        <a href='view_requests.php' style='
            display:inline-block;
            margin-top:15px;
            padding:10px 22px;
            background:#ff4d4d;
            color:white;
            border-radius:25px;
            text-decoration:none;
            font-weight:600;
            box-shadow:0 0 15px rgba(255,77,77,0.8);
        '>⬅ Return to Dashboard</a>
    </div>";
    exit;
}



// ✅ Fetch existing liquidation items
$stmt = $pdo->prepare("SELECT * FROM liquidation_items WHERE request_id = ?");
$stmt->execute([$request_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Insert default 4 rows if empty and display them right away
if (count($items) === 0) {
    $defaults = [
        ['Transportation', 0],
        ['Accommodation', 0],
        ['Meals', 0],
        ['Miscellaneous', 0]
    ];
    $stmt = $pdo->prepare("INSERT INTO liquidation_items (request_id, description, amount) VALUES (?, ?, ?)");
    foreach ($defaults as $d) {
        $stmt->execute([$request_id, $d[0], $d[1]]);
    }

    // Re-fetch to show defaults immediately
    $stmt = $pdo->prepare("SELECT * FROM liquidation_items WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Liquidation Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ✨ Make table rows clearly visible */
.table tbody tr {
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(0, 191, 255, 0.3);
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

.table tbody tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.08);
}

.table tbody tr:hover {
    background: rgba(0, 191, 255, 0.15);
    box-shadow: 0 0 12px rgba(0, 191, 255, 0.3);
}
/* 💡 Subtle glowing borders around each cell */
.table td, .table th {
    border: 1px solid rgba(0, 191, 255, 0.25);
    box-shadow: 0 0 6px rgba(0, 191, 255, 0.1);
    background-clip: padding-box;
}

/* Slight glow on hover */
.table tbody tr:hover td {
    border-color: rgba(0, 255, 255, 0.5);
    box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
}

/* ✨ Make input text always visible */
.table input[type="text"],
.table input[type="number"] {
    color: #ffffff;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(0, 191, 255, 0.3);
    box-shadow: 0 0 6px rgba(0, 191, 255, 0.2);
}

.table input[type="text"]::placeholder,
.table input[type="number"]::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.table input[type="text"]:hover,
.table input[type="number"]:hover {
    border-color: rgba(0, 255, 255, 0.6);
    box-shadow: 0 0 10px rgba(0, 255, 255, 0.4);
}
/* 🔙 Return to Dashboard button */
.btn-return {
    position: fixed;
    top: 20px;
    left: 20px;
    background: linear-gradient(90deg, #007bff, #00bfff);
    color: #fff;
    padding: 10px 18px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 0 15px rgba(0,191,255,0.6);
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-return:hover {
    box-shadow: 0 0 25px rgba(0,255,255,0.9);
    transform: scale(1.05);
}

body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    color: #fff;
    background: #000;
    overflow-x: hidden;
}
canvas#bg {
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}
.card {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 0 25px rgba(0,128,255,0.3);
    backdrop-filter: blur(12px);
}
.card h2 {
    text-align: center;
    color: #00bfff;
    text-shadow: 0 0 12px rgba(0,191,255,0.7);
}
input[type="text"], input[type="number"] {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 8px;
    color: #fff;
    padding: 8px;
}
input:focus {
    outline: none;
    border-color: #00bfff;
    box-shadow: 0 0 10px rgba(0,191,255,0.6);
}
.table {
    color: #fff;
}
.table thead {
    color: #00bfff;
}
.btn-glow {
    background: linear-gradient(90deg, #007bff, #00bfff);
    border: none;
    color: #fff;
    box-shadow: 0 0 20px #00bfff;
    border-radius: 30px;
    padding: 8px 18px;
    font-weight: 600;
    transition: 0.3s;
}
.btn-glow:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px #00ffff;
}
.container {
    max-width: 800px;
    margin-top: 100px;
}
h5, h6 {
    color: #b3e0ff;
}
h4 {
    color: #00ffff;
}
</style>
</head>
<body>
<canvas id="bg"></canvas>
<a href="view_requests.php" class="btn-return">⬅️ Return to Dashboard</a>



<div class="container">
    <div class="card">
        <h2 class="mb-4">🪙 Liquidation Report</h2>
        <h5>Traveler: <?= htmlspecialchars($request['employee_name']) ?></h5>
        <h6>Destination: <?= htmlspecialchars($request['destination']) ?></h6>
        <h6>Approved Budget: ₱<span id="approved"><?= number_format($request['approved_budget'], 2) ?></span></h6>

        <table class="table table-bordered text-white mt-4">
            <thead>
                <tr>
                    <th>Expense Description</th>
                    <th>Amount (₱)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="expense-body">
                <?php foreach ($items as $item): ?>
                    <tr data-id="<?= $item['id'] ?>">
                        <td><input type="text" class="form-control description" value="<?= htmlspecialchars($item['description']) ?>"></td>
                        <td><input type="number" class="form-control amount" value="<?= $item['amount'] ?>" step="0.01"></td>
                        <td><button class="btn btn-danger btn-sm delete">🗑️</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end">
<?php if ($request['status'] !== 'Withdrawn'): ?>
    <button class="btn-glow me-2" id="add-row">➕ Add Expense</button>
    <button class="btn btn-success" id="print-pdf">🖨️ Print PDF</button>
    <button class="btn btn-primary" id="save-liquidation">💾 Save</button>
<?php else: ?>
    <div class="alert alert-danger">
        🚫 Liquidation disabled — request withdrawn
    </div>
<?php endif; ?>
</div>


        <h4 class="text-end mt-4">
            💰 Total: ₱<span id="total">0.00</span><br>
            <small id="balance-status"></small>
        </h4>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// 🌠 Galaxy Animation
const canvas = document.getElementById("bg");
const ctx = canvas.getContext("2d");
let stars = [];

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
window.addEventListener("resize", resizeCanvas);
resizeCanvas();

for (let i = 0; i < 120; i++) {
    stars.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 2 + 0.5,
        dx: (Math.random() - 0.5) * 0.3,
        dy: (Math.random() - 0.5) * 0.3,
        alpha: Math.random()
    });
}
// SAVE LIQUIDATION BUTTON
$('#save-liquidation').click(function() {

    let total_amount = $('#total').text();
    let balance = $('#balance-status').text();

    $.post('save_liquidation.php', {
        request_id: <?= $request_id ?>,
        total_amount: total_amount,
        balance: balance
    }, function(response) {
        alert(response);

        // After saving, hide button
        $('#save-liquidation').hide();
    });
});
$('#save-data').click(function(){
    $.post('save_liquidation.php', {
        request_id: <?= $request_id ?>,
        total_amount: $('#approved').text().replace(/,/g,''),
        pdf_path: '' // optional
    }, function(response){
        if (!response) { alert('No response'); return; }
        if (response.startsWith('OK|')) {
            alert('Saved successfully');
            $('#save-data').hide();
            location.href = 'liquidation_history.php';
        } else {
            alert('Save failed: ' + response);
        }
    });
});




function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    stars.forEach(s => {
        ctx.beginPath();
        ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(0,191,255,${s.alpha})`;
        ctx.fill();
    });
}
function update() {
    stars.forEach(s => {
        s.x += s.dx;
        s.y += s.dy;
        if (s.x < 0 || s.x > canvas.width) s.dx *= -1;
        if (s.y < 0 || s.y > canvas.height) s.dy *= -1;
        s.alpha += (Math.random() - 0.5) * 0.02;
        s.alpha = Math.min(Math.max(s.alpha, 0.2), 1);
    });
}
function animate() {
    draw();
    update();
    requestAnimationFrame(animate);
}
animate();

// 💰 Auto-updating total + balance
function updateTotal() {
    let total = 0;
    $('.amount').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#total').text(total.toFixed(2));

    // Compare to approved budget
    const approved = parseFloat($('#approved').text().replace(/,/g, ''));
    const balance = approved - total;
    const balanceStatus = $('#balance-status');

    if (balance < 0) {
        balanceStatus.html(`⚠️ Over Budget by ₱${Math.abs(balance).toFixed(2)}`).css('color', '#ff6666');
    } else {
        balanceStatus.html(`✅ Remaining Balance: ₱${balance.toFixed(2)}`).css('color', '#66ff99');
    }
}

$(document).on('input', '.description, .amount', function() {
    const row = $(this).closest('tr');
    const id = row.data('id');
    const description = row.find('.description').val();
    const amount = row.find('.amount').val();

    $.post('update_liquidation_item.php', { id, description, amount });
    updateTotal();
});

$('#add-row').click(function() {
    $.post('add_liquidation_item.php', { request_id: <?= $request_id ?> }, function(response) {
        const res = JSON.parse(response);
        $('#expense-body').append(`
            <tr data-id="${res.id}">
                <td><input type="text" class="form-control description" value="New Expense"></td>
                <td><input type="number" class="form-control amount" value="0.00" step="0.01"></td>
                <td><button class="btn btn-danger btn-sm delete">🗑️</button></td>
            </tr>
        `);
        updateTotal();
    });
});

$(document).on('click', '.delete', function() {
    const row = $(this).closest('tr');
    const id = row.data('id');
    $.post('delete_liquidation_item.php', { id });
    row.remove();
    updateTotal();
});

$('#print-pdf').click(function() {
    window.print();
});

$(document).ready(updateTotal);
</script>
</body>
</html>