<?php
session_start();
require 'db_connect.php';
require 'flash.php';
require 'csrf.php';
$csrf = csrf_token();


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate request ID
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($request_id <= 0) {
    flash_set('error', 'Invalid request ID.');
    header("Location: view_requests.php");
    exit;
}

// Fetch request
$stmt = $pdo->prepare("
    SELECT tr.*,
           e.full_name, e.department, e.email,
           d.destination_name, d.country
    FROM travel_requests tr
    JOIN employees e ON tr.employee_id = e.id
    JOIN destinations d ON tr.destination_id = d.id
    WHERE tr.id = ?
");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

// Not found
if (!$request) {
    flash_set('error', 'Travel request not found.');
    header("Location: view_requests.php");
    exit;
}

// Security check
if ($_SESSION['role'] !== 'admin' && $request['employee_id'] != $_SESSION['user_id']) {
    flash_set('error', 'Unauthorized access.');
    header("Location: view_requests.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Request Details</title>

<style>
body {
    margin:0;
    font-family:"Segoe UI",Arial;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    background:#000;
}
canvas#bg {
    position:fixed;
    inset:0;
    z-index:-1;
    background:radial-gradient(circle,#000015,#000);
}

/* Back Button */
.back-btn {
    position:fixed;
    top:20px;
    left:20px;
    padding:12px 24px;
    border-radius:30px;
    text-decoration:none;
    color:#00bfff;
    border:1px solid #00bfff;
    background:rgba(0,191,255,0.15);
    box-shadow:0 0 20px rgba(0,191,255,.4);
    animation:glow 2.5s infinite alternate;
}
@keyframes glow {
    from { box-shadow:0 0 15px rgba(0,191,255,.4); }
    to   { box-shadow:0 0 35px rgba(0,191,255,.9); }
}

.container {
    width:90%;
    max-width:720px;
    background:rgba(20,20,20,.88);
    border-radius:16px;
    padding:35px;
    box-shadow:0 0 35px rgba(0,191,255,.25);
}

h2 {
    text-align:center;
    color:#00bfff;
    margin-bottom:25px;
}

.details p {
    display:flex;
    justify-content:space-between;
    border-bottom:1px solid rgba(255,255,255,.1);
    padding:10px 0;
}

.label { color:#00bfff; font-weight:600; }

.status-pending { color:#ffcc00; }
.status-approved { color:#00ff99; }
.status-rejected { color:#ff6666; }
.status-withdrawn { color:#ff4d4d; }

/* Withdraw Button */
.withdraw-btn {
    margin-top:30px;
    padding:14px 34px;
    border:none;
    border-radius:30px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    color:white;
    background:#ff4d4d;
    box-shadow:0 0 20px rgba(255,77,77,.5);
    transition:.3s;
}
.withdraw-btn:hover {
    transform:scale(1.08);
    box-shadow:0 0 35px rgba(255,77,77,.9);
}
</style>
</head>

<body>

<canvas id="bg"></canvas>
<a href="view_requests.php" class="back-btn">⬅ Back</a>

<?php render_flash(); ?>

<div class="container">
<h2>🚀 Travel Request #<?= $request['id'] ?></h2>

<div class="details">
<p><span class="label">Employee</span><span><?= htmlspecialchars($request['full_name']) ?></span></p>
<p><span class="label">Department</span><span><?= htmlspecialchars($request['department']) ?></span></p>
<p><span class="label">Email</span><span><?= htmlspecialchars($request['email']) ?></span></p>
<p><span class="label">Destination</span><span><?= htmlspecialchars($request['destination_name']) ?>, <?= htmlspecialchars($request['country']) ?></span></p>
<p><span class="label">Purpose</span><span><?= htmlspecialchars($request['reason']) ?></span></p>
<p><span class="label">Departure</span><span><?= $request['departure_date'] ?></span></p>
<p><span class="label">Return</span><span><?= $request['return_date'] ?></span></p>
<p><span class="label">Budget</span><span>₱<?= number_format($request['total_cost'],2) ?></span></p>
<p><span class="label">Status</span>
<span class="status-<?= strtolower($request['status']) ?>">
<?= htmlspecialchars($request['status']) ?>
</span></p>
</div>

<?php if ($request['status'] === 'Pending'): ?>
<form method="POST" action="withdraw_request.php"
      onsubmit="return confirm('Withdraw this request?');"
      style="text-align:center;">
    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" id="csrf_token" value="<?= $csrf ?>">
    <button class="withdraw-btn" onclick="withdrawRequest(<?= $request['id'] ?>)">
    Withdraw Request
</button>

</form>
<?php endif; ?>

</div>

<script>
const canvas = document.getElementById("bg");
const ctx = canvas.getContext("2d");
let stars=[];

function resize(){
    canvas.width=innerWidth;
    canvas.height=innerHeight;
}
resize(); window.onresize=resize;

for(let i=0;i<120;i++){
    stars.push({
        x:Math.random()*canvas.width,
        y:Math.random()*canvas.height,
        r:Math.random()*2+0.5,
        dx:(Math.random()-.5)*.3,
        dy:(Math.random()-.5)*.3,
        a:Math.random()
    });
}

function animate(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    stars.forEach(s=>{
        ctx.beginPath();
        ctx.arc(s.x,s.y,s.r,0,Math.PI*2);
        ctx.fillStyle=`rgba(0,191,255,${s.a})`;
        ctx.fill();
        s.x+=s.dx; s.y+=s.dy;
        if(s.x<0||s.x>canvas.width) s.dx*=-1;
        if(s.y<0||s.y>canvas.height) s.dy*=-1;
    });
    requestAnimationFrame(animate);
}
animate();

function withdrawRequest(requestId) {
    if (!confirm("Are you sure you want to withdraw this request?")) return;

    const csrfToken = document.getElementById('csrf_token').value;

    fetch("withdraw_request.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            request_id: requestId,
            csrf_token: csrfToken
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.msg);
            location.reload(); // refresh to show Withdrawn status
        } else {
            alert(data.msg);
        }
    })
    .catch(() => alert("Network error"));
}
</script>

</body>
</html>
