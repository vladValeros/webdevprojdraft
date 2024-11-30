<?php
require '../classes/database.class.php';
require '../classes/authenticate.class.php';
require '../tools/functions.php';

// Instantiate Authenticator
$auth = new Authenticator($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    // Authenticate user
    if ($auth->login($username, $password)) {
        header("Location: ../admin/dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<title>OCESION - Login</title>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<body>
<div class="container mt-5">
    <h2>Log In</h2>
    <form method="POST">
        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
