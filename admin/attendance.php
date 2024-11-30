<?php
require '../classes/account.class.php';
require '../classes/database.class.php';
require '../tools/functions.php';
session_start();

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');

global $pdo;

// Initialize variables
$event_id = $student_number = $student_name = $time_in = $time_out = "";
$event_id_err = $student_number_err = $time_in_err = $time_out_err = "";

// Fetch all events for the dropdown
$events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (empty(trim($_POST['event_id']))) {
        $event_id_err = "Please select an event.";
    } else {
        $event_id = trim($_POST['event_id']);
    }

    if (empty(trim($_POST['student_number']))) {
        $student_number_err = "Please enter the student number.";
    } else {
        $student_number = trim($_POST['student_number']);
        // Fetch student name
        $stmt = $pdo->prepare("SELECT name FROM students WHERE student_number = :student_number");
        $stmt->bindParam(':student_number', $student_number);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $student_name = $student['name'];
        } else {
            $student_number_err = "Student not found.";
        }
    }

    if (empty(trim($_POST['time_in']))) {
        $time_in_err = "Please enter the time in.";
    } else {
        $time_in = trim($_POST['time_in']);
    }

    if (empty(trim($_POST['time_out']))) {
        $time_out_err = "Please enter the time out.";
    } else {
        $time_out = trim($_POST['time_out']);
    }

    // Check input errors before inserting or updating in the database
    if (empty($event_id_err) && empty($student_number_err) && empty($time_in_err) && empty($time_out_err)) {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            $sql = "UPDATE attendance SET event_id = :event_id, student_number = :student_number, student_name = :student_name, time_in = :time_in, time_out = :time_out WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":event_id", $event_id);
            $stmt->bindParam(":student_number", $student_number);
            $stmt->bindParam(":student_name", $student_name);
            $stmt->bindParam(":time_in", $time_in);
            $stmt->bindParam(":time_out", $time_out);
            $stmt->bindParam(":id", $_POST['update_id']);
            $stmt->execute();
        } else {
            $sql = "INSERT INTO attendance (event_id, student_number, student_name, time_in, time_out) VALUES (:event_id, :student_number, :student_name, :time_in, :time_out)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":event_id", $event_id);
            $stmt->bindParam(":student_number", $student_number);
            $stmt->bindParam(":student_name", $student_name);
            $stmt->bindParam(":time_in", $time_in);
            $stmt->bindParam(":time_out", $time_out);
            $stmt->execute();
        }
        header("Location: attendance.php?event_id=$event_id");
        exit();
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $sql = "DELETE FROM attendance WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $_GET['delete_id']);
    $stmt->execute();
    header("Location: attendance.php?event_id=" . clean_input($_GET['event_id']));
    exit();
}

// Fetch attendance data for the selected event
$attendance_data = [];
if (isset($_GET['event_id'])) {
    $event_id = clean_input($_GET['event_id']);
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE event_id = :event_id ORDER BY time_in ASC");
    $stmt->bindParam(':event_id', $event_id);
    $stmt->execute();
    $attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<body>
<?php include '../includes/_topnav.php'; ?>

<div class="container mt-4">
    <h1>Manage Attendance</h1>

    <!-- Event Selection -->
    <form method="GET" class="mb-4">
        <div class="form-group">
            <label for="event_id">Select Event</label>
            <select class="form-control" id="event_id" name="event_id" onchange="this.form.submit()">
                <option value="">Select...</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= $event_id == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (!empty($event_id)): ?>
        <!-- Form for Create and Update -->
        <form method="POST" class="mb-4">
            <input type="hidden" id="update_id" name="update_id">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <div class="form-group">
                <label for="student_number">Student Number</label>
                <input type="text" class="form-control <?= !empty($student_number_err) ? 'is-invalid' : '' ?>" id="student_number" name="student_number" value="<?= htmlspecialchars($student_number) ?>">
                <span class="invalid-feedback"><?= $student_number_err ?></span>
            </div>
            <div class="form-group">
                    <label for="student_name">Student Name</label>
                    <input type="text" class="form-control" id="student_name" name="student_name" readonly>
            </div>
            <div class="form-group">
                <label for="time_in">Time In</label>
                <input type="time" class="form-control <?= !empty($time_in_err) ? 'is-invalid' : '' ?>" id="time_in" name="time_in" value="<?= htmlspecialchars($time_in) ?>">
                <span class="invalid-feedback"><?= $time_in_err ?></span>
            </div>
            <div class="form-group">
                <label for="time_out">Time Out</label>
                <input type="time" class="form-control <?= !empty($time_out_err) ? 'is-invalid' : '' ?>" id="time_out" name="time_out" value="<?= htmlspecialchars($time_out) ?>">
                <span class="invalid-feedback"><?= $time_out_err ?></span>
            </div>
            <button type="submit" class="btn btn-primary" id="form-submit-btn">Add Attendance</button>
        </form>

        <!-- Attendance List -->
        <h2>Attendance List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Student Name</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_data as $entry): ?>
                    <tr>
                        <td><?= htmlspecialchars($entry['student_number']) ?></td>
                        <td><?= htmlspecialchars($entry['student_name']) ?></td>
                        <td><?= htmlspecialchars($entry['time_in']) ?></td>
                        <td><?= htmlspecialchars($entry['time_out']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $entry['id'] ?>" data-student_number="<?= $entry['student_number'] ?>" data-time_in="<?= $entry['time_in'] ?>" data-time_out="<?= $entry['time_out'] ?>">
                                Edit
                            </button>
                            <a href="attendance.php?delete_id=<?= $entry['id'] ?>&event_id=<?= $event_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    // Populate the form for editing
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('update_id').value = this.dataset.id;
            document.getElementById('student_number').value = this.dataset.student_number;
            document.getElementById('time_in').value = this.dataset.time_in;
            document.getElementById('time_out').value = this.dataset.time_out;
            document.getElementById('form-submit-btn').textContent = 'Update Attendance';
        });
    });

    // Fetch student details on student number input
    document.getElementById('student_number').addEventListener('input', function() {
        const studentNumber = this.value;
        if (studentNumber.length > 0) {
            fetch(`get_student_name.php?student_number=${studentNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('student_name').value = data.name;
                    } else {
                        document.getElementById('student_name').value = '';
                    }
                });
        } else {
            document.getElementById('student_name').value = '';
        }
    });
</script>
</body>
</html>
