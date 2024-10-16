<?php
require_once '../includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows != 1) {
    header("Location: index.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if(empty($username)) {
        $errors[] = "Username is required.";
    }

    if($username != $user['username']) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {
            $errors[] = "Username already exists.";
        }
        $stmt->close();
    }

    if(!empty($password) || !empty($confirm_password)) {
        if($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }

    if(empty($errors)) {
        if(!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssi", $username, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->bind_param("si", $username, $user_id);
        }

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

<h2>Edit User</h2>
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

<form action="edit.php?id=<?php echo $user_id; ?>" method="POST">
    <label for="username">Username<span class="required">*</span>:</label>
    <input type="text" name="username" id="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : htmlspecialchars($user['username']); ?>">

    <label for="password">New Password:</label>
    <input type="password" name="password" id="password">

    <label for="confirm_password">Confirm New Password:</label>
    <input type="password" name="confirm_password" id="confirm_password">

    <button type="submit">Update User</button>
</form>

<?php include '../includes/footer.php'; ?>
