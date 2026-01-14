<?php
require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

$userId = $_SESSION['user_id'];

/* ===== READ FILTER INPUTS ===== */
$fromDate    = $_GET['from_date'] ?? '';
$toDate      = $_GET['to_date'] ?? '';
$paymentType = $_GET['payment_type'] ?? '';

/* ===== PAGINATION SETTINGS ===== */
$limit  = 10;
$page   = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
?>

<link rel="stylesheet" href="css/pages.css">

<h1>
    <i class='bx bxs-dollar-circle'></i>Transaction Status
</h1>

<style>
    .profile-card {
        max-width: 100vw !important;
        margin-top: 0px;
        padding: 2%;
        padding-top: 1%;
    }

    /* FILTER BAR STYLE */
.filter-bar {
  display: flex;
  align-items: flex-end;
  gap: 20px;
  background: #fff;
  padding: 18px 24px;
  border-radius: 16px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.filter-group label {
  font-size: 13px;
  font-weight: 500;
  color: #444;
}

.filter-group input,
.filter-group select {
  min-width: 190px;
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid #ddd;
  font-size: 14px;
  outline: none;
}

.filter-group input:focus,
.filter-group select:focus {
  border-color: #4a90e2;
}

/* ACTION BUTTONS */
.filter-actions {
  display: flex;
  gap: 12px;
  margin-left: auto;
}

.btn-search {
  padding: 10px 26px;
  border-radius: 20px;
  background: #4a90e2;
  border: none;
  color: #fff;
  font-weight: 500;
  cursor: pointer;
}

.btn-search:hover {
  background: #357bd8;
}

.btn-reset {
  padding: 10px 24px;
  border-radius: 20px;
  border: 2px solid #333;
  color: #333;
  text-decoration: none;
  font-weight: 500;
}

.btn-reset:hover {
  background: #333;
  color: #fff;
}

/* MOBILE */
@media (max-width: 768px) {
  .filter-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-actions {
    margin-left: 0;
    justify-content: flex-end;
  }
}

</style>

<!-- ================= FILTER UI ================= -->
<div class="profile-card">
  <form method="GET">
    <div class="filter-bar">

      <div class="filter-group">
        <label>From</label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
      </div>

      <div class="filter-group">
        <label>To</label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
      </div>

      <div class="filter-group">
        <label>Payment Type</label>
        <select name="payment_type">
          <option value="">All</option>
          <option value="Cash" <?= $paymentType === 'Cash' ? 'selected' : '' ?>>Cash</option>
          <option value="UPI" <?= $paymentType === 'UPI' ? 'selected' : '' ?>>UPI</option>
          <option value="Bank" <?= $paymentType === 'Bank' ? 'selected' : '' ?>>Bank</option>
        </select>
      </div>

      <div class="filter-actions">
        <button type="submit" class="btn-search">Search</button>
        <a href="?" class="btn-reset">Reset</a>
      </div>

    </div>
  </form>
</div>

<!-- ================= TABLE ================= -->
<div class="profile-card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Request Date</th>
                    <th>Amount</th>
                    <th>Payment Type</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $sql = "SELECT request_date, amount, payment_type, reason, status
                    FROM advance_salary
                    WHERE user_id = ?
                      AND (? = '' OR DATE(request_date) >= ?)
                      AND (? = '' OR DATE(request_date) <= ?)
                      AND (? = '' OR payment_type = ?)
                    ORDER BY request_date DESC
                    LIMIT ? OFFSET ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "issssssii",
                $userId,
                $fromDate, $fromDate,
                $toDate, $toDate,
                $paymentType, $paymentType,
                $limit, $offset
            );
            $stmt->execute();
            $result = $stmt->get_result();

            $sr = $offset + 1;
            while ($row = $result->fetch_assoc()) {

                $statusClass = 'pending';
                if (strtolower($row['status']) === 'completed') $statusClass = 'success';
                if (strtolower($row['status']) === 'failed')    $statusClass = 'failed';

                echo "<tr>
                        <td>{$sr}</td>
                        <td>{$row['request_date']}</td>
                        <td>₹" . number_format($row['amount'], 2) . "</td>
                        <td>{$row['payment_type']}</td>
                        <td>{$row['reason']}</td>
                        <td>
                            <span class='status {$statusClass}'>" . ucfirst($row['status']) . "</span>
                        </td>
                      </tr>";
                $sr++;
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
/* ===== COUNT QUERY (FILTER AWARE) ===== */
$countSql = "SELECT COUNT(*) AS total
             FROM advance_salary
             WHERE user_id = ?
               AND (? = '' OR DATE(request_date) >= ?)
               AND (? = '' OR DATE(request_date) <= ?)
               AND (? = '' OR payment_type = ?)";

$countStmt = $conn->prepare($countSql);
$countStmt->bind_param(
    "issssss",
    $userId,
    $fromDate, $fromDate,
    $toDate, $toDate,
    $paymentType, $paymentType
);

$countStmt->execute();
$totalRows  = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>

<!-- ================= PAGINATION ================= -->
<div class="d-flex justify-content-end mt-2" id="pagination">
<?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link"
                       href="?page=<?= $page - 1 ?>&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&payment_type=<?= $paymentType ?>">
                        Prev
                    </a>
                </li>
            <?php endif; ?>

            <li class="page-item active">
                <span class="page-link"><?= $page ?></span>
            </li>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link"
                       href="?page=<?= $page + 1 ?>&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&payment_type=<?= $paymentType ?>">
                        Next
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
</div>
