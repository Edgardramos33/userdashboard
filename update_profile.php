<?php
session_start();
include "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $name, $email, $phone_number, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update profile.";
    }

    header("Location: userdashboard.php");
    exit();
}
?>
