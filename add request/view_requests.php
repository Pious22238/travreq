    <?php
    require 'auth_check.php';
    require 'db_connect.php';
    // Admin actions
if (isset($_GET['action'], $_GET['id']) && $_SESSION['role'] === 'admin') {
    $stmt = $pdo->prepare("UPDATE travel_requests SET status=? WHERE id=?");
    $stmt->execute([$_GET['action'], (int)$_GET['id']]);
    header("Location: view_requests.php");
    exit;
}

// Fetch requests
if ($_SESSION['role'] === 'admin') {
    $stmt = $pdo->query("
        SELECT tr.*, e.full_name AS employee_name, e.email, e.department,
               d.destination_name, d.country
        FROM travel_requests tr
        JOIN employees e ON tr.employee_id = e.id
        LEFT JOIN destinations d ON tr.destination_id = d.id
        ORDER BY tr.id DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT tr.*, e.full_name AS employee_name, e.email, e.department,
               d.destination_name, d.country
        FROM travel_requests tr
        JOIN employees e ON tr.employee_id = e.id
        LEFT JOIN destinations d ON tr.destination_id = d.id
        WHERE tr.employee_id = ?
        ORDER BY tr.id DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
}

$requests = $stmt->fetchAll();

    // ✅ Handle Approve / Reject / Pending / Delete actions
    if (isset($_GET['action'], $_GET['id']) && $_SESSION['role'] === 'admin') {
        $id = (int) $_GET['id'];
        $action = $_GET['action'];

        if ($action === 'Delete') {
            $stmt = $pdo->prepare("DELETE FROM travel_requests WHERE id = ?");
                $stmt->execute([$id]);
            header("Location: view_requests.php");
            exit;
        }

        $allowed = ['Approved', 'Rejected', 'Pending'];
        if (in_array($action, $allowed)) {
            $stmt = $pdo->prepare("UPDATE travel_requests SET status = ? WHERE id = ?");
            $stmt->execute([$action, $id]);
            header("Location: view_requests.php");
            exit;
        }
    }

    // 🧠 Fetch travel requests
    if ($_SESSION['role'] === 'admin') {
        $stmt = $pdo->query("
            SELECT 
                tr.id,
                e.full_name AS employee_name,
                e.email,    
                e.department,
                tr.reason,
                tr.departure_date,
                tr.return_date,
                d.destination_name,
                d.country,
                tr.total_cost,
                tr.status
            FROM travel_requests tr
            JOIN employees e ON tr.employee_id = e.id
            LEFT JOIN destinations d ON tr.destination_id = d.id
            ORDER BY tr.id DESC
        "); 
    } else {
        $stmt = $pdo->prepare("
            SELECT 
                tr.id,
                e.full_name AS employee_name,
                e.email,
                e.department,
                tr.reason,
                tr.departure_date,
                tr.return_date,
                d.destination_name,
                d.country,
                tr.total_cost,
                tr.status
            FROM travel_requests tr
            JOIN employees e ON tr.employee_id = e.id
            LEFT JOIN destinations d ON tr.destination_id = d.id
            WHERE tr.employee_id = ?
            ORDER BY tr.id DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }
    $requests = $stmt->fetchAll();
    
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Travel Requests Dashboard</title>
    <style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: radial-gradient(circle at top left, #030b33, #000);
        color: #fff;
        overflow-x: hidden;
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 75px;
        height: 100%;
        background: rgba(10, 20, 50, 0.95);
        backdrop-filter: blur(10px);
        border-right: 2px solid rgba(255, 255, 255, 0.1);
        transition: width 0.3s ease;
        z-index: 1000;
        overflow: hidden;
    }
    .sidebar:hover { width: 230px; }
    .sidebar .logo {
        text-align: center;
        padding: 20px 0;
        font-weight: bold;
        font-size: 18px;
        letter-spacing: 1px;
        color: #4db8ff;
    }
    .sidebar ul { list-style: none; padding: 0; margin: 0; }
    .sidebar ul li {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .sidebar ul li:hover { background: rgba(77, 184, 255, 0.15); }
    .sidebar ul li i { font-size: 20px; color: #4db8ff; min-width: 25px; text-align: center; }
    .sidebar ul li span { opacity: 0; transition: opacity 0.3s ease; }
    .sidebar:hover ul li span { opacity: 1; }

    /* Main */
    .main-content {
        margin-left: 75px;
        transition: margin-left 0.3s ease;
        flex: 1;
        padding: 40px;
    }
    .sidebar:hover ~ .main-content { margin-left: 230px; }

    .table-container {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 0 25px rgba(77, 184, 255, 0.3);
        backdrop-filter: blur(10px);
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td { padding: 12px; text-align: left; white-space: nowrap; }
    th { background: rgba(77, 184, 255, 0.25); color: #fff; }
    tr:nth-child(even) { background: rgba(255, 255, 255, 0.05); }

    /* Buttons */
    a.btn {
        background: #4db8ff;
        color: white;
        padding: 6px 10px;
        text-decoration: none;
        border-radius: 6px;
        transition: 0.3s;
        margin-right: 5px;
    }
    a.btn:hover { background: #007bff; }
    a.btn.btn-delete { background: #e60000; }
    a.btn.btn-delete:hover { background: #cc0000; }
    a.btn.btn-liquidation { background: #00cc99; }
    a.btn.btn-liquidation:hover { background: #00ffb3; }

    /* Status colors */
    .status-approved { color: #00ff99; font-weight: bold; }
    .status-rejected { color: #ff6666; font-weight: bold; }
    .status-pending { color: #ffcc00; font-weight: bold; }

    /* Topbar */
    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .topbar h2 { color: #4db8ff; letter-spacing: 1px; }
    .topbar a {
        text-decoration: none;
        background: #ff4d4d;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
        transition: 0.3s;
    }
    .topbar a:hover { background: #e60000; }

    /* Floating Help Button */
    #helpBtn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: linear-gradient(90deg,#00eaff,#007bff);
        color: white;
        font-size: 26px;
        border: none;
        cursor: pointer;
        box-shadow: 0 0 20px rgba(0,238,255,0.4);
        transition: 0.3s;
        z-index: 999;
    }
    #helpBtn:hover {
        background: linear-gradient(90deg,#00ffe0,#00b8ff);
        transform: scale(1.1);
    }

    /* Modal Styles */
    #helpModal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 1001;
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        background: rgba(15,25,45,0.95);
        border: 1px solid rgba(0,238,255,0.2);
        border-radius: 15px;
        width: 80%;
        max-width: 800px;
        padding: 30px;
        color: #cfd8e3;
        position: relative;
        overflow-y: auto;
        max-height: 80vh;
    }
    .close-btn {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 25px;
        cursor: pointer;
        color: #00eaff;
    }
    .close-btn:hover { color: #00b8ff; }
    h3 { color: #00eaff; border-left: 4px solid #00eaff; padding-left: 10px; }
    /* Container for the main content */
.main-content {
    height: 100vh;         /* Fill the viewport height */
    overflow-y: auto;      /* Enable vertical scrolling */
    padding: 20px;
}

/* Optional: table wrapper for better scrolling */
.table-wrapper {
    max-height: 80vh;      /* Prevent table from overflowing too much */
    overflow-y: auto;
}

    </style>
    </head>
    <body>

    <div class="sidebar">
        <div class="logo">🌌 TravelSys</div>
        <ul>
            <li onclick="window.location='view_requests.php'"><i>📋</i><span>View Requests</span></li>
            <li onclick="window.location='add_request.php'"><i>➕</i><span>Add Request</span></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <li onclick="window.location='reports.php'"><i>📊</i><span>Reports</span></li>
            <?php endif; ?>
            <li onclick="window.location='logout.php'"><i>🚪</i><span>Logout</span></li>
                </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>✈️ Travel Requests</h2>
            <a href="logout.php">Logout</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Purpose</th>
                        <th>Destination</th>
                        <th>Departure</th>
                        <th>Return</th>
                        <th>Approved Budget</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($requests): ?>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td><?= htmlspecialchars($r['employee_name']) ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td><?= htmlspecialchars($r['department']) ?></td>
                            <td><?= htmlspecialchars($r['reason']) ?></td>
                            <td><?= htmlspecialchars($r['destination_name']) ?>, <?= htmlspecialchars($r['country']) ?></td>
                            <td><?= htmlspecialchars($r['departure_date']) ?></td>
                            <td><?= htmlspecialchars($r['return_date']) ?></td>
                            <td>₱<?= number_format($r['total_cost'], 2) ?></td>
                            <td class="status-<?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></td>
                            <td>
                                <a href="view_request_details.php?id=<?= $r['id'] ?>" class="btn">🔍 View</a>
                                <a href="liquidation.php?id=<?= $r['id'] ?>" class="btn btn-liquidation">💰 Liquidation</a>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="?action=Approved&id=<?= $r['id'] ?>" class="btn">✅ Approve</a>
                                    <a href="?action=Rejected&id=<?= $r['id'] ?>" class="btn">❌ Reject</a>
                                    <a href="?action=Pending&id=<?= $r['id'] ?>" class="btn">⏳ Pending</a>
                                    <a href="?action=Delete&id=<?= $r['id'] ?>" class="btn btn-delete">🗑️ Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11" style="text-align:center;">No travel requests found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Help Button -->
    <button id="helpBtn">❓</button>

    <!-- Help Modal -->
    <div id="helpModal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>📞 Help & Customer Service</h2>
        <p>Welcome to the <strong>Travel Request System</strong>. Here’s a quick guide to using your dashboard.</p>

        <h3>🔧 General Help</h3>
        <ul>
        <li>Check your credentials if login fails.</li>
        <li>If pages show “Not Found,” ensure files are in <code>htdocs/travel_system</code>.</li>
        <li>Use this ❓ button for instant help anytime.</li>
        </ul>

        <h3>📝 Employee Guide</h3>
        <ul>
        <li>Submit requests via “Add Request.”</li>
        <li>Track your request status here.</li>
        <li>Contact admin to modify requests.</li>
        </ul>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <h3>👑 Admin Guide</h3>
        <ul>
        <li>Approve, Reject, Pending, or Delete requests.</li>
        <li>Keep employee–destination data linked correctly.</li>
        <li>Use the 💰 Liquidation button to manage travel expenses.</li>
        </ul>
        <?php endif; ?>

        <h3>📬 Contact Support</h3>
        <p><strong>📧 Email:</strong> Jacintonico@yahoo.com / hz202306127@wmsu.edu.ph<br>
        <strong>☎️ Phone:</strong> 0912-103-0107<br>
        <strong>🕒 Hours:</strong> Mon–Fri, 9:00 AM – 5:00 PM
        </p>
    </div>
    </div>
    <div class="table-wrapper">
    <table>
        <!-- your table rows -->
    </table>
</div>



    <script>
    const helpBtn = document.getElementById("helpBtn");
    const helpModal = document.getElementById("helpModal");
    const closeBtn = document.querySelector(".close-btn");

    helpBtn.addEventListener("click", () => helpModal.style.display = "flex");
    closeBtn.addEventListener("click", () => helpModal.style.display = "none");
    window.addEventListener("click", (e) => {
    if (e.target === helpModal) helpModal.style.display = "none";
    });
    </script>

    </body>
    </html>
