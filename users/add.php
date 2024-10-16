<?php
require_once '../includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if(empty($username)) {
        $errors[] = "Username is required.";
    }
    if(empty($password)) {
        $errors[] = "Password is required.";
    }
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0) {
        $errors[] = "Username already exists.";
    }
    $stmt->close();

    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<?php include '../includes/header.php'; ?>

<h2>Add New User</h2>
<a href="index.php" class="btn">Back to Users</a>

<?php if(!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="add.php" method="POST">
    <label for="username">Username<span class="required">*</span>:</label>
    <input type="text" name="username" id="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">

    <label for="password">Password<span class="required">*</span>:</label>
    <input type="password" name="password" id="password" required>

    <label for="confirm_password">Confirm Password<span class="required">*</span>:</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <button type="submit">Add User</button>
</form>

<?php include '../includes/footer.php'; ?>
