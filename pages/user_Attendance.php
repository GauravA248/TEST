<?php

require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";
?>

<link rel="stylesheet" href="css/pages.css">

<h1>
    <i class='bx bx-log-in'></i>My Attendance History
</h1>

<style>
    .profile-card {
        max-width: 100vw !important;
        margin-top: 0px;
        padding: 2%;
        padding-top: 1%;
    }
    .attendance-filter {
        display: grid;
        grid-template-columns: 1fr 1fr auto auto;
        gap: 12px;
        align-items: end;
    }
    .btn-item button {
        min-width: 110px;
        height: 38px;
        border-radius: 20px;
    }
    @media (max-width: 768px) {
        .attendance-filter {
            grid-template-columns: 1fr;
        }
    }
</style>

<form method ="Get">
<div class="profile-card">
    <div class="attendance-filter">
        <div class="filter-item">
            <label>From</label>
            <input type="date" class="form-control" name="from_date">
        </div>
        <div class="filter-item">
            <label>To</label>
            <input type="date" class="form-control" name="to_date">
        </div>
        <div class="filter-item btn-item">
            <label>&nbsp;</label>
           <button class="btn btn-primary" type="submit">Search</button>
        </div>
        <div class="filter-item btn-item">
            <label>&nbsp;</label>
            <button class="btn btn-outline-dark">Reset</button>
        </div>
    </div>
</div>
</form>

<?php
$userId = $_SESSION['user_id'];

/* ---------- DB PAGINATION (SAFE) ---------- */
$limit  = 10;
$page   = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* ---------- DATA QUERY ---------- */
$sql = "SELECT attendance_date, user_id, punch_in, punch_out, hours_worked
        FROM attendance
        WHERE user_id = ?
        ORDER BY attendance_date DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userId, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

/* ---------- COUNT QUERY ---------- */
$countSql = "SELECT COUNT(*) AS total
FROM attendance
WHERE user_id = ?
  AND (? = '' OR Date(attendance_date) >= ?)
  AND (? = '' OR Date(attendance_date) <= ?)";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param(
    "issss",
    $userId,
    $fromDate, $fromDate,
    $toDate, $toDate
);
$countStmt->execute();
$totalRows  = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>

<div class="table-responsive">
    <table class="custom-table" id="punchTable">
        <thead>
            <tr>
                <th>Sr No</th>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Punch In Time</th>
                <th>Punch Out Time</th>
                <th>Total Hours</th>
            </tr>
        </thead>

        <tbody id="punchBody">
        <?php
        $sr = $offset + 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$sr}</td>
                    <td>{$row['attendance_date']}</td>
                    <td>{$row['user_id']}</td>
                    <td>" . ($row['punch_in'] ? date("h:i A", strtotime($row['punch_in'])) : "--") . "</td>
                    <td>" . ($row['punch_out'] ? date("h:i A", strtotime($row['punch_out'])) : "--") . "</td>
                    <td>{$row['hours_worked']}</td>
                  </tr>";
            $sr++;
        }
        ?>
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-end mt-2" id="pagination">
<?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Prev</a>
                </li>
            <?php endif; ?>

            <li class="page-item active">
                <span class="page-link"><?= $page ?></span>
            </li>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
</div>

<script>
    const allRows = Array.from(document.querySelectorAll("#punchBody tr"));
</script>

<script>
    function applyHourColors() {
        const rows = document.querySelectorAll("#punchBody tr");
        rows.forEach(row => {
            const cell = row.children[5];
            const hours = parseInt(cell.innerText.split("h")[0]);
            if (!isNaN(hours)) {
                cell.style.color = hours < 8 ? "red" : "green";
                cell.style.fontWeight = "600";
            }
        });
    }
    applyHourColors();
</script>
