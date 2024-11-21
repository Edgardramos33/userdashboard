<?php
session_start();
include "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'client') {
                header("Location: userdashboard.php");
            } elseif ($user['role'] === 'therapist') {
                header("Location: therapist_dashboard.php");
            } elseif ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="form-container">
        <?php if (isset($error)): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <h2>Login</h2>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>