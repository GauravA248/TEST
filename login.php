<?php
session_start();
require_once "config/db.php";

$error = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === "" || $password === "") {
        $error = "All fields are required";
    } else {

        // Fetch user by username OR email
        $sql = "
            SELECT id, username, email, password, role, status, emp_id, first_login
            FROM users
            WHERE username = ? OR email = ?
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check status
            if ($user['status'] !== 'active') {
                $error = "Account is inactive";
            }
            // Verify password
            elseif (!password_verify($password, $user['password'])) {
                $error = "Invalid username or password";
            }
            // SUCCESS
            else {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['emp_id']   = $user['emp_id'];
                $_SESSION['first_login'] = $user['first_login'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: userDash.php");
                }
                exit;
            }

        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>JEEVCHI | Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="video-container">
    <video autoplay muted loop playsinline>
        <source src="images/loginBG.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>
</div>

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-lg-4 col-md-6 col-sm-10">
        <div class="login-card text-center">
            <img src="images/Man avatar.gif" class="avatar">

            <form method="POST">
                <div class="mb-4">
                    <input type="text" name="username"
                           class="form-control"
                           placeholder="Username or Email" required>
                </div>

                <div class="mb-4">
                    <input type="password" name="password"
                           class="form-control"
                           placeholder="Password" required>
                </div>

                <div class="d-grid">
                    <button type="submit"
                            class="btn btn-login btn-primary fw-semibold rounded-3">
                        LOGIN
                    </button>
                </div>
            </form>

            <?php if ($error): ?>
                <script>
                    Swal.fire({
                        icon: "error",
                        title: "Login Failed",
                        text: "<?= $error ?>"
                    });
                </script>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
