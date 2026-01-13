<?php
require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

$userId = $_SESSION['user_id'];

/* ===== READ FILTER INPUTS ===== */
$fromDate = $_GET['from_date'] ?? '';
$toDate   = $_GET['to_date'] ?? '';
$statusF  = $_GET['status'] ?? '';

/* ===== PAGINATION ===== */
$limit  = 10;
$page   = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
?>

<link rel="stylesheet" href="css/pages.css">

<h1>
    <i class='bx bxs-calendar-check'></i>Leave Status
</h1>

<style>
    .profile-card {
        max-width: 100vw !important;
        margin-top: 0px;
        padding: 2%;
        padding-top: 1%;
    }
</style>

<!-- ================= FILTER BAR ================= -->
<div class="profile-card">
    <form method="GET">
        <div class="attendance-filter">

            <div class="filter-item">
                <label>From</label>
                <input type="date" class="form-control" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
            </div>

            <div class="filter-item">
                <label>To</label>
                <input type="date" class="form-control" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
            </div>

            <div class="filter-item">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="">All</option>
                    <option value="approved" <?= $statusF === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="pending" <?= $statusF === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="rejected" <?= $statusF === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <div class="filter-item btn-item">
                <label>&nbsp;</label>
                <button class="btn btn-primary" type="submit">Search</button>
            </div>

            <div class="filter-item btn-item">
                <label>&nbsp;</label>
                <a href="?" class="btn btn-outline-dark">Reset</a>
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
                    <th>Leave Date</th>
                    <th>Request Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $sql = "SELECT id, start_date, end_date, created_at, status
                    FROM leaves
                    WHERE user_id = ?
                      AND (? = '' OR DATE(start_date) >= ?)
                      AND (? = '' OR DATE(end_date) <= ?)
                      AND (? = '' OR status = ?)
                    ORDER BY id DESC
                    LIMIT ? OFFSET ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "issssssii",
                $userId,
                $fromDate, $fromDate,
                $toDate, $toDate,
                $statusF, $statusF,
                $limit, $offset
            );
            $stmt->execute();
            $result = $stmt->get_result();

            $sr = $offset + 1;
            while ($row = $result->fetch_assoc()) {

                $cls = 'pending';
                if ($row['status'] === 'approved') $cls = 'success';
                if ($row['status'] === 'rejected') $cls = 'failed';

                echo "<tr>
                        <td>{$sr}</td>
                        <td>{$row['start_date']} to {$row['end_date']}</td>
                        <td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>
                        <td><span class='status {$cls}'>" . ucfirst($row['status']) . "</span></td>
                      </tr>";
                $sr++;
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
/* ===== COUNT QUERY ===== */
$countSql = "SELECT COUNT(*) AS total
             FROM leaves
             WHERE user_id = ?
               AND (? = '' OR DATE(start_date) >= ?)
               AND (? = '' OR DATE(end_date) <= ?)
               AND (? = '' OR status = ?)";

$countStmt = $conn->prepare($countSql);
$countStmt->bind_param(
    "issssss",
    $userId,
    $fromDate, $fromDate,
    $toDate, $toDate,
    $statusF, $statusF
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
                       href="?page=<?= $page - 1 ?>&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&status=<?= $statusF ?>">
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
                       href="?page=<?= $page + 1 ?>&from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>&status=<?= $statusF ?>">
                        Next
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
</div>
