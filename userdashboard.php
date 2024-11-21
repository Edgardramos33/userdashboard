<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_query = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

$upcoming_query = $conn->prepare(
    "SELECT appointments.*, services.service_name
     FROM appointments
     JOIN services ON appointments.service_id = services.service_id
     WHERE appointments.user_id = ? AND appointments.appointment_date >= NOW()
     ORDER BY appointments.appointment_date ASC"
);
$upcoming_query->bind_param("i", $user_id);
$upcoming_query->execute();
$upcoming_appointments = $upcoming_query->get_result();

$past_query = $conn->prepare(
    "SELECT appointments.*, services.service_name
     FROM appointments
     JOIN services ON appointments.service_id = services.service_id
     WHERE appointments.user_id = ? AND appointments.appointment_date < NOW()
     ORDER BY appointments.appointment_date DESC"
);
$past_query->bind_param("i", $user_id);
$past_query->execute();
$past_appointments = $past_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
        </header>

        <section>
            <h2>Upcoming Appointments</h2>
            <?php if ($upcoming_appointments->num_rows > 0): ?>
                <ul>
                    <?php while ($appointment = $upcoming_appointments->fetch_assoc()): ?>
                        <li>
                            <strong>Service:</strong> <?= htmlspecialchars($appointment['service_name']) ?><br>
                            <strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?><br>
                            <strong>Status:</strong> <?= htmlspecialchars($appointment['status']) ?><br>
                            <a href="cancel_appointment.php?id=<?= $appointment['appointment_id'] ?>">Cancel</a> |
                            <a href="reschedule.php?id=<?= $appointment['appointment_id'] ?>">Reschedule</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No upcoming appointments.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Past Appointments</h2>
            <?php if ($past_appointments->num_rows > 0): ?>
<section>
    <h2>Upcoming Appointments</h2>
    <?php if ($upcoming_appointments->num_rows > 0): ?>
        <ul>
            <?php while ($appointment = $upcoming_appointments->fetch_assoc()): ?>
                <li>
                    <div class="appointment-details">
                        <span><strong>Service:</strong> <?= htmlspecialchars($appointment['service_name']) ?></span>
                        <span><strong>Status:</strong> <?= htmlspecialchars($appointment['status']) ?></span>
                        <span><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></span>
                    </div>
                    <div class="appointment-actions">
                        <a href="cancel_appointment.php?id=<?= $appointment['appointment_id'] ?>">Cancel</a> |
                        <a href="reschedule.php?id=<?= $appointment['appointment_id'] ?>">Reschedule</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No upcoming appointments.</p>
    <?php endif; ?>
</section>
            <?php else: ?>
                <p>No past appointments.</p>
            <?php endif; ?>
            <section>
    <h2>Account Settings</h2>

    <form action="update_profile.php" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <br>
        
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
        <br>
        
        <button type="submit">Update Profile</button>
    </form>

    <form action="change_password.php" method="POST">
        <h3>Change Password</h3>

        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
        <br>
        
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <br>
        
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br>
        
        <button type="submit">Change Password</button>
    </form>
</section>

        <section>
            <h2>Promotions and Rewards</h2>
            <p>Check out our latest offers and rewards!</p>
            <ul>
                <li>Get 10% off your next booking!</li>
                <li>Earn loyalty points for every appointment.</li>
            </ul>
        </section>

        <footer>
            <a href="logout.php">Logout</a> | 
            <a href="index.php">Back to Homepage</a>
        </footer>
    </div>
</body>
</html>

<?php
$conn->close();
?>
