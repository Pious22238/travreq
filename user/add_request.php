    <?php
    require 'auth_check.php';
    require 'db_connect.php';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $full_name = $_POST['full_name'];
        $department = $_POST['department'];
        $email = $_POST['email'];
        $destination_name = $_POST['destination_name'];
        $country = $_POST['country'];
        $reason = $_POST['reason'];
        $travel_date = $_POST['travel_date'];
        $departure_date = $_POST['departure_date'];
        $return_date = $_POST['return_date'];
        $total_cost = $_POST['total_cost'];
        
        // 🔑 FIX: Use the authenticated session ID as the employee_id for the request.
        $employee_id = $_SESSION['user_id']; 
        // We will use this single ID for both employee_id and user_id columns in travel_requests.

        // 1. Update Employee details in the 'employees' table with form data 
        // This ensures name/department changes made in the form are saved to their profile.
        $stmt = $pdo->prepare("
            UPDATE employees 
            SET full_name=?, department=?, email=? 
            WHERE id = ?
        ");
        // Assuming the user's ID is the key to their employee record.
        $stmt->execute([$full_name, $department, $email, $employee_id]); 


        // 2. Check and Insert destination
        $stmt = $pdo->prepare("SELECT id FROM destinations WHERE destination_name = ? AND country = ?");
        $stmt->execute([$destination_name, $country]);
        $destination = $stmt->fetch();

        if (!$destination) {
            $stmt = $pdo->prepare("INSERT INTO destinations (destination_name, country) VALUES (?, ?)");
            $stmt->execute([$destination_name, $country]);
            $destination_id = $pdo->lastInsertId();
        } else {
            $destination_id = $destination['id'];
        }

        // 3. Insert travel request using the single, correct $employee_id
        $stmt = $pdo->prepare("
            INSERT INTO travel_requests (employee_id, destination_id, reason, travel_date, departure_date, return_date, total_cost, status, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)
        ");
        $stmt->execute([$employee_id, $destination_id, $reason, $travel_date, $departure_date, $return_date, $total_cost, $employee_id]); 
        // Note: We use $employee_id (which is $_SESSION['user_id']) for both employee_id and user_id columns.

        header("Location: view_requests.php?success=Request+Added");
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Add Travel Request | Travel System</title>
    <style>
    /* 🌌 Futuristic Galaxy Background */
    body {
        margin: 0;
        font-family: "Segoe UI", Arial, sans-serif;
        color: #fff;
        background: #000;
        overflow-x: hidden;
        overflow-y: auto;
    }
    html {
        scroll-behavior: smooth;
    }
    ::-webkit-scrollbar {
        width: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(0,191,255,0.4);
        border-radius: 5px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(0,191,255,0.7);
    }

    /* Canvas Background */
    canvas#bg {
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background: radial-gradient(circle at center, #000010, #000);
    }

    /* 🔵 Back Button - Glowing Responsive Pulse */
    .back-btn {
        position: fixed;
        top: 20px;
        left: 30px;
        background: rgba(0,191,255,0.15);
        border: 1px solid rgba(0,191,255,0.5);
        color: #00bfff;
        padding: 12px 25px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 0 15px rgba(0,191,255,0.4);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        z-index: 10;
        animation: glowPulse 2.5s infinite alternate ease-in-out;
    }
    @keyframes glowPulse {
        from { box-shadow: 0 0 15px rgba(0,191,255,0.4); }
        to { box-shadow: 0 0 35px rgba(0,191,255,0.8); }
    }
    .back-btn:hover {
        background: rgba(0,191,255,0.3);
        color: white;
        transform: scale(1.08);
        box-shadow: 0 0 35px rgba(0,191,255,0.9);
    }
    @media (max-width: 600px) {
        .back-btn {
            top: 15px;
            left: 15px;
            padding: 10px 18px;
            font-size: 14px;
        }
    }

    /* Form Card */
    .container {
        width: 90%;
        max-width: 650px;
        margin: 100px auto 60px auto;
        background: rgba(20,20,20,0.85);
        border-radius: 15px;
        padding: 30px 40px;
        box-shadow: 0 0 30px rgba(0,191,255,0.2);
        backdrop-filter: blur(12px);
        transition: transform 0.3s ease;
    }
    h2 {
        text-align: center;
        color: #00bfff;
        text-shadow: 0 0 12px rgba(0,191,255,0.7);
    }
    h3 {
        color: #00bfff;
        border-left: 4px solid #00bfff;
        padding-left: 8px;
        margin-top: 25px;
    }

    /* Labels & Inputs */
    label {
        display: block;
        margin-top: 12px;
        font-weight: 600;
    }
    input, textarea {
        width: 100%;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(0,191,255,0.3);
        border-radius: 8px;
        color: #fff;
        padding: 10px;
        font-size: 15px;
        margin-top: 5px;
        transition: 0.3s ease;
    }
    input:focus, textarea:focus {
        outline: none;
        border-color: #00bfff;
        box-shadow: 0 0 15px rgba(0,191,255,0.5);
        background: rgba(255,255,255,0.12);
    }

    /* Buttons */
    button, .btn {
        display: inline-block;
        padding: 12px 22px;
        margin-top: 20px;
        border: none;
        border-radius: 30px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0,191,255,0.4);
    }
    .btn-primary, button[type="submit"] {
        background-color: #00bfff;
    }
    .btn-primary:hover {
        background-color: #0099cc;
        box-shadow: 0 0 25px rgba(0,191,255,0.6);
    }
    .btn-secondary {
        background-color: #555;
    }
    .btn-secondary:hover {
        background-color: #777;
        box-shadow: 0 0 20px rgba(255,255,255,0.3);
    }

    /* Glow Hover Depth */
    .container:hover {
        transform: scale(1.01);
    }

    /* Responsive */
    @media (max-width: 600px) {
        .container {
            padding: 20px;
        }
        h2 { font-size: 20px; }
    }

    /* 3D Parallax */
    .container {
        transform-style: preserve-3d;
    }
    </style>
    </head>
    <body>
    <canvas id="bg"></canvas>

    <a href="view_requests.php" class="back-btn">← Back to Dashboard</a>

    <div class="container">
        <h2>🚀 Add Travel Request</h2>
        <form method="POST" action="">
            <h3>Employee Information</h3>
            <label>Full Name:</label>
            <input type="text" name="full_name" required>

            <label>Department:</label>
            <input type="text" name="department" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <h3>Destination Details</h3>
            <label>Destination Name (City/Location):</label>
            <input type="text" name="destination_name" required>

            <label>Country:</label>
            <input type="text" name="country" required>

            <label>Purpose of Travel:</label>
            <textarea name="reason" rows="3" required></textarea>

            <h3>Travel Schedule</h3>
            <label>Departure Date:</label>
            <input type="date" name="departure_date" required>

            <label>Return Date:</label>
            <input type="date" name="return_date" required>

            <label>Travel Date (Request Date):</label>
            <input type="date" name="travel_date" required>

            <label>Approved Budget(₱):</label>
            <input type="number" name="total_cost" step="0.01" min="0" required>

            <button type="submit" class="btn-primary">Submit Request</button>
            <a href="view_requests.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
    // 🌠 Animated Galaxy Background + Soft Drift
    const canvas = document.getElementById("bg");
    const ctx = canvas.getContext("2d");
    let particles = [];

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    for (let i = 0; i < 120; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            r: Math.random() * 2 + 0.5,
            dx: (Math.random() - 0.5) * 0.3,
            dy: (Math.random() - 0.5) * 0.3,
            alpha: Math.random()
        });
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(p => {
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(0,191,255,${p.alpha})`;
            ctx.fill();
        });
    }

    function update() {
        particles.forEach(p => {
            p.x += p.dx;
            p.y += p.dy;
            if (p.x < 0 || p.x > canvas.width) p.dx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.dy *= -1;
            p.alpha += (Math.random() - 0.5) * 0.02;
            p.alpha = Math.min(Math.max(p.alpha, 0.2), 1);
        });
    }

    function animate() {
        draw();
        update();
        requestAnimationFrame(animate);
    }
    animate();

    // 🎮 Parallax Mouse Effect
    const container = document.querySelector('.container');
    document.addEventListener("mousemove", e => {
        const x = (e.clientX / window.innerWidth - 0.5) * 2;
        const y = (e.clientY / window.innerHeight - 0.5) * 2;
        container.style.transform = `rotateY(${x * 4}deg) rotateX(${-y * 4}deg)`;
    });
    document.addEventListener("mouseleave", () => {
        container.style.transform = "";
    });
    </script>
    </body>
    </html>