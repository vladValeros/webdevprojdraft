<?php
require './classes/account.class.php';
require './classes/database.class.php';
session_start();

global $pdo;
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include './includes/_head.php'; ?>
<body>
<?php include './includes/_topnav.php'; ?>
<div class="container mt-4">
    <h1>Event Calendar</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Event Title</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= htmlspecialchars($event['description']) ?></td>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include './includes/_footer.php'; ?>
</body>
</html>
