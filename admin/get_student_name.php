<?php
require '../classes/database.class.php';

$student_number = isset($_GET['student_number']) ? $_GET['student_number'] : '';

if ($student_number) {
    $stmt = $pdo->prepare("SELECT name FROM students WHERE student_number = :student_number");
    $stmt->bindParam(':student_number', $student_number);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        echo json_encode(['success' => true, 'name' => $student['name']]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
