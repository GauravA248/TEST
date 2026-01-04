<?php
require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

/* ===== FORM SUBMIT ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {    
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("Unauthorized access attempt");
        }

        $user_id = $_SESSION['user_id'];
        $paymentType = $_POST['paymentType'] ?? '';
        $amount = $_POST['amount'] ?? '';
        $date = $_POST['date'] ?? '';
        $reason = $_POST['reason'] ?? '';

        $upi = null;
        $account = null;
        $ifsc = null;

        if ($paymentType === 'UPI') {
            $upi = $_POST['upi_id'] ?? null;
        }

        if ($paymentType === 'Bank Transfer') {
            $account = $_POST['account_number'] ?? null;
            $ifsc = $_POST['ifsc_code'] ?? null;
        }

        $stmt = $conn->prepare("
            INSERT INTO advance_salary 
            (user_id, payment_type, upi_id, account_number, ifsc_code, amount, request_date, reason)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "issssdss",
            $user_id,
            $paymentType,
            $upi,
            $account,
            $ifsc,
            $amount,
            $date,
            $reason
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        echo "<script>alert('Payment Applied Successfully');</script>";

        $stmt->close();
        

    } catch (Throwable $e) {

        // ✅ PHP native logging
        error_log(
            "[Advance Salary Error] " .
            "UserID: " . ($_SESSION['user_id'] ?? 'NA') .
            " | Message: " . $e->getMessage()
        );     
    }

}
?>


<link rel="stylesheet" href="/TEST/css/pages.css">
<div id="main-content">

<h1 class="d-flex justify-content-center my-0 py-0">
    <i class='bx bxs-dollar-circle'></i>Payment Application
</h1>

<style>
#upiField,#accountField,#ifscField{display:none;}
</style>

<div class="d-flex justify-content-center">
<div class="profile-card col-md-8 col-sm-12 mt-2">

<form method="POST" id="profileForm" action ="pages/payment.php">

    <div class="mb-3">
        <label class="form-label">Select Payment Type</label>
        <select name="paymentType" id="paymentType" class="form-select" required>
            <option value="">Select</option>
            <option value="Cash">Cash</option>
            <option value="UPI">UPI</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
    </div>

    <div class="mb-3" id="upiField">
        <label class="form-label">Enter UPI ID</label>
        <input type="text" name="upi_id" class="form-control">
    </div>

    <div class="mb-3" id="accountField">
        <label class="form-label">Enter Account Number</label>
        <input type="number" name="account_number" class="form-control">
    </div>

    <div class="mb-3" id="ifscField">
        <label class="form-label">Enter IFSC Code</label>
        <input type="text" name="ifsc_code" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Enter Amount</label>
        <input type="number" name="amount" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Select Date</label>
        <input type="date" name="date" class="form-control" required>
    </div>
    <div class="mb-3">
            <label class="form-label">Reason</label>
            <textarea name="reason" class="form-control" rows="2" required></textarea>
    </div>
    <div class="d-flex justify-content-center gap-3 mt-4">
        <button type="reset" class="btn btn-cancel">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>

    

</form>
</div>
</div>
</div>
<script src="/TEST/js/sidebar-loader.js"></script>


<script>
const paymentType = document.getElementById("paymentType");
const upi = document.getElementById("upiField");
const acc = document.getElementById("accountField");
const ifsc = document.getElementById("ifscField");

paymentType.addEventListener("change", () => {
    upi.style.display = "none";
    acc.style.display = "none";
    ifsc.style.display = "none";

    if (paymentType.value === "UPI") upi.style.display = "block";
    if (paymentType.value === "Bank Transfer") {
        acc.style.display = "block";
        ifsc.style.display = "block";
    }
});
</script>
