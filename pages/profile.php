<?php
require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";
// ================= FETCH USER PROFILE FOR AUTO-FILL =================
$profile = [];

$stmt = $conn->prepare("
    SELECT *
    FROM user_profiles
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $profile = $res->fetch_assoc();
}
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        if (!isset($_SESSION['user_id'])) {
            throw new Exception("Unauthorized access");
        }

        $user_id = $_SESSION['user_id'];

        // Collect POST data (NO UI CHANGE REQUIRED)
        $title       = $_POST['title'] ?? null;
        $full_name   = $_POST['full_name'] ?? null;
        $guardian    = $_POST['guardian_name'] ?? null;
        $aadhaar     = $_POST['aadhaar'] ?? null;
        $pan         = $_POST['pan'] ?? null;
        $email       = $_POST['email'] ?? null;
        $phone       = $_POST['phone'] ?? null;

        $perm_addr   = $_POST['permanent_address'] ?? null;
        $corr_addr   = $_POST['correspondence_address'] ?? null;

        $job_role    = $_POST['job_role'] ?? null;
       $doj = !empty($_POST['date_of_joining'])
                ? date('Y-m-d', strtotime($_POST['date_of_joining']))
                : null;

        $bank_name   = $_POST['bank_name'] ?? null;
        $account_no  = $_POST['account_number'] ?? null;
        $ifsc_code   = $_POST['ifsc_code'] ?? null;

        // 🔍 Check if profile already exists
        $check = $conn->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $check->store_result();
        $exists = $check->num_rows > 0;
        $check->close();

        if ($exists) {

            // ✅ UPDATE (like re-apply payment logic)
            $stmt = $conn->prepare("
                UPDATE user_profiles SET
                    title = ?,
                    full_name = ?,
                    guardian_name = ?,
                    aadhaar = ?,
                    pan = ?,
                    email = ?,
                    phone = ?,
                    permanent_address = ?,
                    correspondence_address = ?,
                    job_role = ?,
                    date_of_joining = ?,
                    bank_name = ?,
                    account_number = ?,
                    ifsc_code = ?
                WHERE user_id = ?
            ");

            $stmt->bind_param(
                "ssssssssssssssi",
                $title, $full_name, $guardian, $aadhaar, $pan,
                $email, $phone,
                $perm_addr, $corr_addr,
                $job_role, $doj,
                $bank_name, $account_no, $ifsc_code,
                $user_id
            );

        } else {

            // ✅ INSERT (first time profile save)
            $stmt = $conn->prepare("
                INSERT INTO user_profiles
                (user_id, title, full_name, guardian_name, aadhaar, pan, email, phone,
                 permanent_address, correspondence_address,
                 job_role, date_of_joining,
                 bank_name, account_number, ifsc_code)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "issssssssssssss",
                $user_id,
                $title, $full_name, $guardian, $aadhaar, $pan,
                $email, $phone,
                $perm_addr, $corr_addr,
                $job_role, $doj,
                $bank_name, $account_no, $ifsc_code
            );
        }

         if (empty($_SESSION['user_id'])) {
          die("USER NOT LOGGED IN");
            }
        $stmt->execute();
        $stmt->close();

        // ✅ SAME PATTERN AS payment.php
        echo "<script>
            alert('Profile saved successfully');
            window.location.href = '../userDash.php';
        </script>";
        exit;
        echo "PROFILE SAVED";
        exit;

    } catch (Throwable $e) {

        error_log("[Profile Save Error] UserID: " . ($_SESSION['user_id'] ?? 'NA') .
                  " | " . $e->getMessage());

        echo "<script>
            alert('Something went wrong. Please try again.');
            window.location.href = 'profile.php';
        </script>";
        exit;
    }
}
?>

<link rel="stylesheet" href="/TEST/css/pages.css">
<div id="main-content">
<h1><i class='bx bxs-plane'></i> Profile Details</h1>
<div class="profile-card">
    <form id="profileForm" method="POST" action="pages/profile.php">

        <div class="form-row">
            <div class="mb-3 col">
                <label for="title" class="form-label">Title</label>
                <select id="title" name="title" class="form-select" required>
                    <option value="">Select</option>
<option value="Mr"   <?= ($profile['title'] ?? '')=='Mr'?'selected':'' ?>>Mr.</option>
<option value="Mrs"  <?= ($profile['title'] ?? '')=='Mrs'?'selected':'' ?>>Mrs.</option>
<option value="Miss" <?= ($profile['title'] ?? '')=='Miss'?'selected':'' ?>>Miss</option>

                </select>
            </div>

            <div class="mb-3 col">
                <label for="fullName" class="form-label">Full Name</label>
                <input type="text" id="fullName" name="full_name"
class="form-control"
value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>"
required>

            </div>
        </div>

        <div class="form-row">
            <div class="mb-3 col">
                <label for="guardianName" class="form-label">Name of Guardian / Spouse</label>
                <input type="text" id="guardianName" name="guardian_name"
class="form-control"
value="<?= htmlspecialchars($profile['guardian_name'] ?? '') ?>">

            </div>

            <div class="mb-3 col">
                <label for="aadhaar" class="form-label">Aadhaar Card Number</label>
               <input type="text" id="aadhaar" name="aadhaar"
                    maxlength="12"
                    class="form-control"
                    value="<?= htmlspecialchars($profile['aadhaar'] ?? '') ?>"
                    pattern="\d{12}">

            </div>
        </div>

        <div class="form-row">
            <div class="mb-3 col">
                <label for="pan" class="form-label">PAN Card Number</label>
                <input type="text" id="pan" name="pan"
                maxlength="10"
                class="form-control"
                value="<?= htmlspecialchars($profile['pan'] ?? '') ?>"
                pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}">

            </div>

            <div class="mb-3 col">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email"
class="form-control"
value="<?= htmlspecialchars($profile['email'] ?? '') ?>"
required>

            </div>
        </div>

        <div class="form-row">
            <div class="mb-3 col">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone"
maxlength="10"
class="form-control"
value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"
pattern="\d{10}">

            </div>

            <div class="mb-3 col">
                <label class="form-label">Permanent Address</label>
                <input type="text" name="permanent_address"
class="form-control"
value="<?= htmlspecialchars($profile['permanent_address'] ?? '') ?>">

            </div>
        </div>

        <div class="form-row">
            <div class="mb-3 col">
                <label class="form-label">Job Role</label>
                <select name="job_role" class="form-select" required>
                    <option value="">Select</option>
<option value="Security" <?= ($profile['job_role'] ?? '')=='Security'?'selected':'' ?>>Security</option>
<option value="HouseKeeping" <?= ($profile['job_role'] ?? '')=='HouseKeeping'?'selected':'' ?>>HouseKeeping</option>
<option value="Driver" <?= ($profile['job_role'] ?? '')=='Driver'?'selected':'' ?>>Driver</option>
                </select>
            </div>

            <div class="mb-3 col">
                <label for="joining" class="form-label">Date of Joining</label>
                <input type="date" id="joining" name="date_of_joining"
class="form-control"
value="<?= $profile['date_of_joining'] ?? '' ?>"
required>

            </div>
        </div>

        <div class="form-row">
            <div class="mb-3 col">
                <label for="bankName" class="form-label">Bank Name</label>
                <input type="text" id="bankName" name="bank_name"
class="form-control"
value="<?= htmlspecialchars($profile['bank_name'] ?? '') ?>"
required>

            </div>

            <div class="mb-3 col">
                <label for="ifscCode" class="form-label">IFSC Code</label>
                <input type="text" id="ifscCode" name="ifsc_code"
class="form-control"
value="<?= htmlspecialchars($profile['ifsc_code'] ?? '') ?>"
required>

            </div>
        </div>

        <div class="form-row">
            <div class="mb-3 col">
                <label for="account" class="form-label">Acount Number</label>
                <input type="number" id="account" name="account_number"
class="form-control"
value="<?= htmlspecialchars($profile['account_number'] ?? '') ?>"
required>

            </div>

            <div class="mb-3 col">
                <label for="corrAddress" class="form-label">Correspondence Address</label>
                <input type="text" id="corrAddress" name="correspondence_address"
class="form-control"
value="<?= htmlspecialchars($profile['correspondence_address'] ?? '') ?>">

            </div>
        </div>

        <div class="btn-position mt-4">
            <button type="button" class="btn btn-cancel" id="cancelBtn">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>

    </form>
</div>
</div>

<!-- <script src="/TEST/js/sidebar-loader.js"></script> -->





