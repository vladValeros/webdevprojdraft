<?php
require '../classes/account.class.php';
session_start();
Account::redirect_if_not_logged_in('admin');
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<body>
<?php include '../includes/_topnav.php'; ?>
<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['username']); ?>!</p>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="../admin/events.php">Event Manager</a>
    </nav>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../admin/attendance.php">Attendance</a>
    </nav>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../admin/students.php">Students</a>
    </nav>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../admin/users.php">Users</a>
    </nav>

    <a href="../account/logout.php" class="btn btn-danger">Logout</a>
</div>
<?php include '../includes/_footer.php'; ?>
</body>
</html>
