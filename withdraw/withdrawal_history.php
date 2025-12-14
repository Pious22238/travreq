<?php
// withdrawal_history.php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; }

$page = max(1, (int)($_GET['page'] ?? 1));
$per = 20;
$offset = ($page-1)*$per;
$q = trim($_GET['q'] ?? '');

// basic filter on travel_request_id or withdrawal_id or actor_id
$where = "1=1";
$params = [];
if ($q !== '') {
  $where .= " AND (wh.withdrawal_id = :exact OR wh.travel_request_id = :exact OR wh.actor_id = :exact OR wh.action LIKE :like)";
  $params['exact'] = (int)$q;
  $params['like'] = "%$q%";
}

$total = $pdo->prepare("SELECT COUNT(*) FROM withdrawal_history wh WHERE $where");
$total->execute($params);
$totalN = (int)$total->fetchColumn();

$stmt = $pdo->prepare("SELECT wh.*, u.email AS actor_email, e.full_name AS actor_name FROM withdrawal_history wh LEFT JOIN users u ON wh.actor_id = u.id LEFT JOIN employees e ON e.user_id = u.id WHERE $where ORDER BY wh.created_at DESC LIMIT :l OFFSET :o");
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->bindValue('l', (int)$per, PDO::PARAM_INT);
$stmt->bindValue('o', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Withdrawal Audit Trail</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header_sidebar.php'; ?>

<div class="container mt-4">
  <h3>Withdrawal Audit Trail</h3>

  <form class="mb-2">
    <div class="input-group">
      <input name="q" class="form-control" value="<?= htmlspecialchars($q) ?>" placeholder="Search id, travel id, actor id or action">
      <button class="btn btn-primary">Search</button>
    </div>
  </form>

  <table class="table table-sm">
    <thead><tr><th>ID</th><th>Withdraw ID</th><th>Travel ID</th><th>Action</th><th>Actor</th><th>Details</th><th>IP</th><th>Created</th></tr></thead>
    <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= $r['withdrawal_id'] ?></td>
          <td><?= $r['travel_request_id'] ?></td>
          <td><?= htmlspecialchars($r['action']) ?></td>
          <td><?= htmlspecialchars($r['actor_role'] . ' / ' . ($r['actor_name'] ?? $r['actor_email'] ?? $r['actor_id'])) ?></td>
          <td><?= nl2br(htmlspecialchars($r['details'])) ?></td>
          <td><?= htmlspecialchars($r['ip_address']) ?></td>
          <td><?= $r['created_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- pagination -->
  <?php $pages = max(1, ceil($totalN / $per)); ?>
  <nav><ul class="pagination">
    <?php for($p=1;$p<=$pages;$p++): ?>
      <li class="page-item <?= $p==$page ? 'active':'' ?>"><a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($q) ?>"><?= $p ?></a></li>
    <?php endfor; ?>
  </ul></nav>
</div>
</body>
</html>
