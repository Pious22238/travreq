<?php
session_start();
require 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO users (username, password, role)
                VALUES (?, ?, 'user')
            ");
            $stmt->execute([
                $username,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $user_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("
                INSERT INTO employees (id, full_name, email, department)
                VALUES (?, '', '', '')
            ");
            $stmt->execute([$user_id]);

            $pdo->commit();

            // 🔑 AUTO LOGIN
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';

            $success = "Registration successful! Redirecting…";

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Registration failed.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | Travel Request System</title>

<style>
/* 🌌 Futuristic Background */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(120deg, #0a0f1f, #001a33, #00264d);
    background-size: 400% 400%;
    animation: gradientShift 20s ease infinite;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: #e0e6f1;
    margin: 0;
}
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* ✨ Register Box */
.register-box {
    background: rgba(15, 25, 45, 0.8);
    border-radius: 16px;
    padding: 40px;
    width: 380px;
    box-shadow: 0 0 25px rgba(0, 200, 255, 0.15);
    backdrop-filter: blur(8px);
    text-align: center;
}

/* 🔁 Step Indicator */
.step-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 25px;
    gap: 12px;
}
.step {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    border: 2px solid rgba(0,200,255,0.3);
    color: #a3cfff;
    font-weight: bold;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    transition: all 0.3s ease;
}
.step span { font-size: 11px; }
.step.active {
    background: rgba(0,234,255,0.2);
    border-color: #00eaff;
    color: #00eaff;
    box-shadow: 0 0 15px #00eaff80;
}
.arrow {
    font-size: 26px;
    color: rgba(0,234,255,0.4);
    animation: arrowPulse 1.8s infinite alternate;
}
@keyframes arrowPulse {
    from { opacity: 0.4; transform: translateX(0); }
    to { opacity: 1; transform: translateX(6px); }
}

/* Inputs */
label {
    display: block;
    text-align: left;
    margin-top: 10px;
    font-weight: bold;
}
input {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 8px;
    border: 1px solid rgba(0,200,255,0.2);
    background: rgba(255,255,255,0.05);
    color: #fff;
}

/* Button */
button {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background: linear-gradient(90deg, #00eaff, #007bff);
    border: none;
    border-radius: 50px;
    font-weight: bold;
    cursor: pointer;
    color: #fff;
}

/* Messages */
.error {
    color: #ff4b4b;
    margin-top: 12px;
}
.success {
    color: #00ffb3;
    margin-top: 12px;
}
footer {
    margin-top: 20px;
    font-size: 13px;
}
footer a {
    color: #00ffe0;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="register-box">

    <!-- STEP INDICATOR -->
    <div class="step-indicator">
        <div class="step active">1<br><span>Register</span></div>
        <div class="arrow">➜</div>
        <div class="step">2<br><span>Add Request</span></div>
    </div>

    <h2>🚀 Create Your Account</h2>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Register</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>

    <?php elseif ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>

        <script>
            // Activate Add Request step
            document.querySelectorAll('.step')[1].classList.add('active');

            // Redirect to Add Request
            setTimeout(() => {
                window.location.href = "add_request.php";
            }, 1500);
        </script>
    <?php endif; ?>
    <footer>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </footer>

</div>

</body>
</html>
