<?php
session_start();
require 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = "No user found with that username.";
        } elseif (!password_verify($password, $user['password'])) {
            $error = "Incorrect password.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: view_requests.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Travel Request System</title>
  <style>
/* 🌌 Futuristic Galaxy Theme */
body {
  margin: 0;
  padding: 0;
  font-family: "Segoe UI", sans-serif;
  color: #fff;
  background: #000;
  overflow: hidden;
  height: 100vh;
}

/* Canvas for stars */
canvas#bg {
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

/* Glassmorphic Login Box */
.login-box {
  background: rgba(20, 20, 30, 0.6);
  border: 1px solid rgba(0,191,255,0.4);
  border-radius: 20px;
  box-shadow: 0 0 25px rgba(0,191,255,0.2);
  backdrop-filter: blur(12px);
  padding: 40px 35px;
  width: 380px;
  max-width: 90%;
  margin: 0 auto;
  position: relative;
  top: 50%;
  transform: translateY(-50%);
  text-align: center;
  animation: fadeIn 1s ease;
}

h2 {
  margin-bottom: 20px;
  color: #00bfff;
  text-shadow: 0 0 15px rgba(0,191,255,0.8);
}

/* Inputs */
input[type="text"], input[type="password"] {
  width: 100%;
  padding: 12px;
  margin-top: 8px;
  margin-bottom: 20px;
  border-radius: 10px;
  border: 1px solid rgba(0,191,255,0.4);
  background: rgba(255,255,255,0.08);
  color: white;
  font-size: 15px;
  transition: all 0.3s ease;
}
input:focus {
  outline: none;
  border-color: #00bfff;
  box-shadow: 0 0 15px rgba(0,191,255,0.6);
  background: rgba(255,255,255,0.15);
}

/* Glowing Button */
button {
  width: 100%;
  padding: 12px;
  background: #00bfff;
  color: white;
  border: none;
  border-radius: 30px;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s ease;
  box-shadow: 0 0 20px rgba(0,191,255,0.5);
}
button:hover {
  background: #0099cc;
  box-shadow: 0 0 35px rgba(0,191,255,0.8);
  transform: scale(1.05);
}

/* Error Text */
.error {
  color: #ff4c4c;
  margin-top: 10px;
  font-weight: bold;
  text-shadow: 0 0 5px rgba(255,0,0,0.6);
}

/* Footer */
footer {
  margin-top: 20px;
  font-size: 14px;
  color: #bbb;
}
footer a {
  color: #00bfff;
  text-decoration: none;
}
footer a:hover {
  text-shadow: 0 0 10px rgba(0,191,255,0.8);
}

/* Fade In */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-60%); }
  to { opacity: 1; transform: translateY(-50%); }
}

/* Responsive */
@media (max-width: 480px) {
  .login-box {
    padding: 25px 20px;
  }
  h2 { font-size: 22px; }
}

/* Parallax container */
.login-box {
  transform-style: preserve-3d;
}
  </style>
</head>
<body>
  <canvas id="bg"></canvas>
  <div class="login-box">
    <h2>🚀 Login</h2>
    <form method="POST">
      <label>Username:</label>
      <input type="text" name="username" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
    </form>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <footer>
      <p>Don’t have an account? <a href="register.php">Register here</a></p>
    </footer>
  </div>

  <script>
/* 🌠 Galaxy Background Animation */
const canvas = document.getElementById("bg");
const ctx = canvas.getContext("2d");
let stars = [];

function resize() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
window.addEventListener("resize", resize);
resize();

for (let i = 0; i < 150; i++) {
  stars.push({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height,
    r: Math.random() * 2,
    dx: (Math.random() - 0.5) * 0.2,
    dy: (Math.random() - 0.5) * 0.2,
    alpha: Math.random()
  });
}

function drawStars() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  stars.forEach(s => {
    ctx.beginPath();
    ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
    ctx.fillStyle = `rgba(0,191,255,${s.alpha})`;
    ctx.fill();
  });
}

function updateStars() {
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
  drawStars();
  updateStars();
  requestAnimationFrame(animate);
}
animate();

/* 🎮 Subtle Parallax Mouse Movement */
const loginBox = document.querySelector('.login-box');
document.addEventListener("mousemove", e => {
  const x = (e.clientX / window.innerWidth - 0.5) * 2;
  const y = (e.clientY / window.innerHeight - 0.5) * 2;
  loginBox.style.transform = `translateY(-50%) rotateY(${x * 5}deg) rotateX(${-y * 5}deg)`;
});
document.addEventListener("mouseleave", () => {
  loginBox.style.transform = "translateY(-50%)";
});
  </script>
</body>
</html>
