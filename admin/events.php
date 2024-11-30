<?php
require '../classes/account.class.php';
require '../classes/database.class.php';
require '../tools/functions.php';
session_start();

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');

global $pdo;

// Initialize variables
$title = $description = $event_date = "";
$title_err = $description_err = $event_date_err = "";

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (empty(trim($_POST['title']))) {
        $title_err = "Please enter the event title.";
    } else {
        $title = trim($_POST['title']);
    }

    if (empty(trim($_POST['description']))) {
        $description_err = "Please enter the event description.";
    } else {
        $description = trim($_POST['description']);
    }

    if (empty(trim($_POST['event_date']))) {
        $event_date_err = "Please enter the event date.";
    } else {
        $event_date = trim($_POST['event_date']);
    }

    // Check input errors before inserting or updating in the database
    if (empty($title_err) && empty($description_err) && empty($event_date_err)) {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            // Prepare an update statement
            $sql = "UPDATE events SET title = :title, description = :description, event_date = :event_date WHERE id = :id";

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement
                $stmt->bindParam(":title", $title);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":event_date", $event_date);
                $stmt->bindParam(":id", $_POST['update_id']);

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    header("Location: events.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        } else {
            // Prepare an insert statement
            $sql = "INSERT INTO events (title, description, event_date) VALUES (:title, :description, :event_date)";

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement
                $stmt->bindParam(":title", $title);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":event_date", $event_date);

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    header("Location: events.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $sql = "DELETE FROM events WHERE id = :id";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $_GET['delete_id']);

        if ($stmt->execute()) {
            header("Location: events.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}

// Fetch all events
$sql = "SELECT * FROM events ORDER BY event_date ASC";
$events = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<body>
<?php include '../includes/_topnav.php'; ?>
<script src="../js/bootstrap.bundle.min.js"></script>
<div class="container mt-4">
    <h1>Manage Events</h1>

    <!-- Form for Create and Update -->
    <form method="POST" class="mb-4">
        <input type="hidden" id="update_id" name="update_id">
        <div class="form-group">
            <label for="title">Event Title</label>
            <input type="text" class="form-control <?= !empty($title_err) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= htmlspecialchars($title) ?>">
            <span class="invalid-feedback"><?= $title_err ?></span>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control <?= !empty($description_err) ? 'is-invalid' : '' ?>" id="description" name="description" rows="3"><?= htmlspecialchars($description) ?></textarea>
            <span class="invalid-feedback"><?= $description_err ?></span>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date</label>
            <input type="date" class="form-control <?= !empty($event_date_err) ? 'is-invalid' : '' ?>" id="event_date" name="event_date" value="<?= htmlspecialchars($event_date) ?>">
            <span class="invalid-feedback"><?= $event_date_err ?></span>
        </div>
        <button type="submit" class="btn btn-primary" id="form-submit-btn">Add Event</button>
    </form>

    <h2>Event List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= htmlspecialchars($event['description']) ?></td>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" 
                                data-id="<?= $event['id'] ?>" 
                                data-title="<?= htmlspecialchars($event['title']) ?>" 
                                data-description="<?= htmlspecialchars($event['description']) ?>" 
                                data-event_date="<?= htmlspecialchars($event['event_date']) ?>">
                            Edit
                        </button>
                        <a href="events.php?delete_id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/_footer.php'; ?>

<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
<script>
    // Populate the form for editing
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('update_id').value = this.dataset.id;
            document.getElementById('title').value = this.dataset.title;
            document.getElementById('description').value = this.dataset.description;
            document.getElementById('event_date').value = this.dataset.event_date;
            document.getElementById('form-submit-btn').textContent = 'Update Event';
        });
    });
</script>
</body>
</html>
