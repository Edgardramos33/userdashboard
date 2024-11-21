<?php
session_start();
include "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "New passwords do not match.";
        header("Location: userdashboard.php");
        exit();
    }


    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (password_verify($current_password, $result['password'])) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Password updated successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to update password.";
        }
    } else {
        $_SESSION['error_message'] = "Current password is incorrect.";
    }

    header("Location: userdashboard.php");
    exit();
}
?>
