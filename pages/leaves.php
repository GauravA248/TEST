<?php
// =======================
// AUTH + DB
// =======================
require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

// =======================
// ALERT HOLDER
// =======================
$alert = null;

// =======================
// HANDLE FORM SUBMIT
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id    = $_SESSION['user_id'];
    $leave_type = trim($_POST['leave_type'] ?? '');
    $from_date  = $_POST['from_date'] ?? '';
    $to_date    = $_POST['to_date'] ?? '';
    $reason     = trim($_POST['reason'] ?? '');

    // -----------------------
    // 1️⃣ REQUIRED VALIDATION
    // -----------------------
    if ($leave_type === '' || $from_date === '' || $to_date === '' || $reason === '') {

        $alert = [
            'type'  => 'warning',
            'title' => 'Validation Error',
            'text'  => 'All fields are required'
        ];

    }
    // -----------------------
    // 2️⃣ DATE VALIDATION
    // -----------------------
    elseif ($from_date > $to_date) {

        $alert = [
            'type'  => 'warning',
            'title' => 'Invalid Dates',
            'text'  => 'From date cannot be greater than To date'
        ];

    }
    else {

        // -----------------------
        // 3️⃣ MAX 3 LEAVES / MONTH
        // -----------------------
        $month = date('m', strtotime($from_date));
        $year  = date('Y', strtotime($from_date));

        $countSql = "
            SELECT COUNT(*) AS total
            FROM leaves
            WHERE user_id = ?
              AND MONTH(start_date) = ?
              AND YEAR(start_date) = ?
        ";

        $stmt = $conn->prepare($countSql);
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        $countResult = $stmt->get_result()->fetch_assoc();

        if ($countResult['total'] >= 3) {

            $alert = [
                'type'  => 'error',
                'title' => 'Limit Exceeded',
                'text'  => 'You can apply only 3 leaves in a month'
            ];

        } else {

            // -----------------------
            // 4️⃣ INSERT LEAVE
            // -----------------------
            $insertSql = "
                INSERT INTO leaves
                (user_id, leave_type, start_date, end_date, reason, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ";

            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param(
                "issss",
                $user_id,
                $leave_type,
                $from_date,
                $to_date,
                $reason,
            );

            if ($stmt->execute()) {

                // PRG pattern (prevent duplicate submit)
                $_SESSION['leave_alert'] = [
                    'type'  => 'success',
                    'title' => 'Success',
                    'text'  => 'Leave applied successfully'
                ];

                header("Location: leaves.php");
                exit;

            } else {

                $alert = [
                    'type'  => 'error',
                    'title' => 'Error',
                    'text'  => 'Something went wrong. Please try again'
                ];
            }
        }
    }
}

// =======================
// SHOW ALERT AFTER REDIRECT
// =======================
if (isset($_SESSION['leave_alert'])) {
    $alert = $_SESSION['leave_alert'];
    unset($_SESSION['leave_alert']);
}
?>

<!-- =======================
     PAGE UI
======================= -->

<link rel="stylesheet" href="/TEST/css/pages.css">
<div id="main-content">
<h1><i class='bx bxs-plane'></i> Apply Leave</h1>

<div class="profile-card">
    <form method="POST" id="profileForm" action="pages/leaves.php">

        <div class="mb-3">
            <label class="form-label">Leave Type</label>
            <select name="leave_type" class="form-select" required>
                <option value="">Select</option>
                <option value="Casual">Casual Leave</option>
                <option value="Sick">Sick Leave</option>
                <option value="Earned">Earned Leave</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Reason</label>
            <textarea name="reason" class="form-control" rows="2" required></textarea>
        </div>

        <div class="btn-position mt-4">
            <button type="submit" class="btn btn-primary">
                Submit Leave
            </button>
        </div>

    </form>
</div>
</div>
<script src="/TEST/js/sidebar-loader.js"></script>

<!-- =======================
     SWEETALERT POPUP
======================= -->
<?php if ($alert): ?>
<script>
Swal.fire({
    icon: "<?= $alert['type'] ?>",
    title: "<?= $alert['title'] ?>",
    text: "<?= $alert['text'] ?>",
    confirmButtonColor: "#3085d6"
});
</script>
<?php endif; ?>
