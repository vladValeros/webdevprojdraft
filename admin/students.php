<?php
require '../classes/account.class.php';
require '../classes/database.class.php';
require '../tools/functions.php';
session_start();

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');

global $pdo;

// Initialize variables
$student_number = $name = $course = $year_level = "";
$student_number_err = $name_err = $course_err = $year_level_err = "";

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (empty(trim($_POST['student_number']))) {
        $student_number_err = "Please enter the student number.";
    } else {
        $student_number = trim($_POST['student_number']);
    }

    if (empty(trim($_POST['name']))) {
        $name_err = "Please enter the student's name.";
    } else {
        $name = trim($_POST['name']);
    }

    if (empty(trim($_POST['course']))) {
        $course_err = "Please enter the course.";
    } else {
        $course = trim($_POST['course']);
    }

    if (empty(trim($_POST['year_level']))) {
        $year_level_err = "Please enter the year level.";
    } else {
        $year_level = trim($_POST['year_level']);
    }

    // Check input errors before inserting or updating in the database
    if (empty($student_number_err) && empty($name_err) && empty($course_err) && empty($year_level_err)) {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            // Prepare an update statement
            $sql = "UPDATE students SET student_number = :student_number, name = :name, course = :course, year_level = :year_level WHERE id = :id";

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":student_number", $student_number);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":course", $course);
                $stmt->bindParam(":year_level", $year_level);
                $stmt->bindParam(":id", $_POST['update_id']);

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Record updated successfully. Redirect to landing page
                    header("Location: students.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        } else {
            // Prepare an insert statement
            $sql = "INSERT INTO students (student_number, name, course, year_level) VALUES (:student_number, :name, :course, :year_level)";

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":student_number", $student_number);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":course", $course);
                $stmt->bindParam(":year_level", $year_level);

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Record created successfully. Redirect to landing page
                    header("Location: students.php");
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
    $sql = "DELETE FROM students WHERE id = :id";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $_GET['delete_id']);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Record deleted successfully. Redirect to landing page
            header("Location: students.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}

// Fetch all students
$sql = "SELECT * FROM students ORDER BY name ASC";
$students = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<body>
<?php include '../includes/_topnav.php'; ?>
<script src="../js/bootstrap.bundle.min.js"></script>
<div class="container mt-4">
    <h1>Manage Students</h1>

    <!-- Form for Create and Update -->
    <form method="POST" class="mb-4">
        <input type="hidden" id="update_id" name="update_id">
        <div class="form-group">
            <label for="student_number">Student Number</label>
            <input type="text" class="form-control <?= !empty($student_number_err) ? 'is-invalid' : '' ?>" id="student_number" name="student_number" value="<?= htmlspecialchars($student_number) ?>">
            <span class="invalid-feedback"><?= $student_number_err ?></span>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control <?= !empty($name_err) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($name) ?>">
            <span class="invalid-feedback"><?= $name_err ?></span>
        </div>
        <div class="form-group">
            <label for="course">Course</label>
            <input type="text" class="form-control <?= !empty($course_err) ? 'is-invalid' : '' ?>" id="course" name="course" value="<?= htmlspecialchars($course) ?>">
            <span class="invalid-feedback"><?= $course_err ?></span>
        </div>
        <div class="form-group">
            <label for="year_level">Year Level</label>
            <input type="number" class="form-control <?= !empty($year_level_err) ? 'is-invalid' : '' ?>" id="year_level" name="year_level" value="<?= htmlspecialchars($year_level) ?>">
            <span class="invalid-feedback"><?= $year_level_err ?></span>
        </div>
        <button type="submit" class="btn btn-primary" id="form-submit-btn">Add Student</button>
    </form>

    <h2>Student List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Number</th>
                <th>Name</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['student_number']) ?></td>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td><?= htmlspecialchars($student['course']) ?></td>
                    <td><?= htmlspecialchars($student['year_level']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" 
                                data-id="<?= $student['id'] ?>" 
                                data-student_number="<?= htmlspecialchars($student['student_number']) ?>" 
                                data-name="<?= htmlspecialchars($student['name']) ?>" 
                                data-course="<?= htmlspecialchars($student['course']) ?>" 
                                data-year_level="<?= htmlspecialchars($student['year_level']) ?>">
                            Edit
                        </button>
                        <a href="students.php?delete_id=<?= $student['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
            document.getElementById('student_number').value = this.dataset.student_number;
            document.getElementById('name').value = this.dataset.name;
            document.getElementById('course').value = this.dataset.course;
            document.getElementById('year_level').value = this.dataset.year_level;
            document.getElementById('form-submit-btn').textContent = 'Update Student';
        });
    });
</script>
</body>
</html>
