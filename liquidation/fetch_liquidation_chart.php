<?php
// fetch_liquidation_chart.php
// Returns JSON for Chart.js (period = day | week | month; from, to optional)
header('Content-Type: application/json; charset=utf-8');
require 'db_connect.php';
session_start();

// Validate period
$allowed = ['day','week','month'];
$period = strtolower($_GET['period'] ?? 'week');
if (!in_array($period, $allowed)) $period = 'week';

// Parse dates or default to last 8 periods
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

$tz = new DateTimeZone('UTC');
$now = new DateTime('now', $tz);

if (!$to) $to = $now->format('Y-m-d');
if (!$from) {
    // default: last 8 *periods*
    $tmp = clone $now;
    if ($period === 'day')      $tmp->modify('-14 days');   // last 14 days
    elseif ($period === 'week') $tmp->modify('-7 weeks');   // last 7 weeks
    else                        $tmp->modify('-8 months');  // last 8 months
    $from = $tmp->format('Y-m-d');
}

// Normalize times
$from_dt = new DateTime($from . ' 00:00:00', $tz);
$to_dt   = new DateTime($to   . ' 23:59:59', $tz);

// Prepare SQL grouping depending on period
if ($period === 'day') {
    // label = date
    $sql = "
      SELECT DATE(created_at) AS label,
             COALESCE(SUM(total_expense),0) AS sum_expense,
             COALESCE(SUM(total_amount),0) AS sum_amount
      FROM liquidation_history
      WHERE created_at BETWEEN ? AND ?
      GROUP BY DATE(created_at)
      ORDER BY DATE(created_at) ASC
    ";
} elseif ($period === 'month') {
    $sql = "
      SELECT DATE_FORMAT(created_at, '%Y-%m-01') AS label,
             COALESCE(SUM(total_expense),0) AS sum_expense,
             COALESCE(SUM(total_amount),0) AS sum_amount
      FROM liquidation_history
      WHERE created_at BETWEEN ? AND ?
      GROUP BY YEAR(created_at), MONTH(created_at)
      ORDER BY YEAR(created_at), MONTH(created_at) ASC
    ";
} else { // week
    // use ISO week (YEAR + WEEK with mode 1)
    $sql = "
      SELECT CONCAT(YEAR(created_at), '-W', LPAD(WEEK(created_at,1),2,'0')) AS label,
             YEAR(created_at) AS yr,
             WEEK(created_at,1) AS wk,
             COALESCE(SUM(total_expense),0) AS sum_expense,
             COALESCE(SUM(total_amount),0) AS sum_amount,
             MIN(created_at) AS period_start
      FROM liquidation_history
      WHERE created_at BETWEEN ? AND ?
      GROUP BY yr, wk
      ORDER BY yr, wk ASC
    ";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$from_dt->format('Y-m-d H:i:s'), $to_dt->format('Y-m-d H:i:s')]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build a full list of labels spanning from->to with period step and fill zeros if missing
$labels = [];
$exp_map = []; // label => sum_expense
$amt_map = []; // label => sum_amount

foreach ($rows as $r) {
    $lbl = $r['label'];
    $exp_map[$lbl] = (float) $r['sum_expense'];
    $amt_map[$lbl] = (float) $r['sum_amount'];
}

// Generate contiguous labels
$current = clone $from_dt;
$end = clone $to_dt;

if ($period === 'day') {
    while ($current <= $end) {
        $lbl = $current->format('Y-m-d');
        $labels[] = $lbl;
        if (!isset($exp_map[$lbl])) $exp_map[$lbl] = 0.0;
        if (!isset($amt_map[$lbl])) $amt_map[$lbl] = 0.0;
        $current->modify('+1 day');
    }
} elseif ($period === 'month') {
    // normalize to first day of month
    $current->modify('first day of this month')->setTime(0,0,0);
    $end->modify('first day of this month')->setTime(0,0,0);
    while ($current <= $end) {
        $lbl = $current->format('Y-m-01');
        $labels[] = $lbl;
        if (!isset($exp_map[$lbl])) $exp_map[$lbl] = 0.0;
        if (!isset($amt_map[$lbl])) $amt_map[$lbl] = 0.0;
        $current->modify('+1 month');
    }
} else { // week
    // step by ISO week: set to monday of that week for label readability
    $current->modify('monday this week')->setTime(0,0,0);
    $end->modify('monday this week')->setTime(0,0,0);
    while ($current <= $end) {
        $lbl = $current->format('o') . '-W' . $current->format('W'); // ISO year-week
        $labels[] = $lbl;
        // SQL label for week was CONCAT(YEAR(created_at), '-W', LPAD(WEEK(created_at,1),2,'0'))
        if (!isset($exp_map[$lbl])) $exp_map[$lbl] = 0.0;
        if (!isset($amt_map[$lbl])) $amt_map[$lbl] = 0.0;
        $current->modify('+1 week');
    }
}

// Build data arrays corresponding to labels
$expense_data = [];
$amount_data = [];
foreach ($labels as $lbl) {
    $expense_data[] = round($exp_map[$lbl] ?? 0.0, 2);
    $amount_data[]  = round($amt_map[$lbl]  ?? 0.0, 2);
}

// Return JSON
echo json_encode([
    'labels' => $labels,
    'expense' => $expense_data,
    'amount' => $amount_data,
    'period' => $period,
    'from' => $from_dt->format('Y-m-d'),
    'to' => $to_dt->format('Y-m-d')
], JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
exit;
