<?php
require_once '../includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $position   = trim($_POST['position']);
    $department = trim($_POST['department']);

    if(empty($first_name)) {
        $errors[] = "First name is required.";
    }
    if(empty($last_name)) {
        $errors[] = "Last name is required.";
    }
    if(empty($email)) {
        $errors[] = "Email is required.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if(empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, email, phone, position, department) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $position, $department);

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

<h2>Add New Employee</h2>
<a href="index.php" class="btn">Back to Employees</a>

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
    <label for="first_name">First Name<span class="required">*</span>:</label>
    <input type="text" name="first_name" id="first_name" required value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>">

    <label for="last_name">Last Name<span class="required">*</span>:</label>
    <input type="text" name="last_name" id="last_name" required value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>">

    <label for="email">Email<span class="required">*</span>:</label>
    <input type="email" name="email" id="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">

    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">

    <label for="position">Position:</label>
    <input type="text" name="position" id="position" value="<?php echo isset($position) ? htmlspecialchars($position) : ''; ?>">

    <label for="department">Department:</label>
    <input type="text" name="department" id="department" value="<?php echo isset($department) ? htmlspecialchars($department) : ''; ?>">

    <button type="submit">Add Employee</button>
</form>

<?php include '../includes/footer.php'; ?>
