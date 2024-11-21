<?php
session_start();
include "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];

    $check_query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_query->bind_param("s", $email);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_number, role) VALUES (?, ?, ?, ?, 'client')");
        $stmt->bind_param("ssss", $name, $email, $password, $phone_number);
        $stmt->execute();

        $user_id = $stmt->insert_id;

        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = 'client';

        header("Location: userdashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="register-container">
        <h1>Create an Account</h1>
        
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>
            <br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>
            <br>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
