<?php
require_once '../includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $employee_id = $_GET['id'];
} else {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows != 1) {
    header("Location: index.php");
    exit();
}

$employee = $result->fetch_assoc();
$stmt->close();

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
        $stmt = $conn->prepare("UPDATE employees SET first_name = ?, last_name = ?, email = ?, phone = ?, position = ?, department = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $position, $department, $employee_id);

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

<h2>Edit Employee</h2>
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

<form action="edit.php?id=<?php echo $employee_id; ?>" method="POST">
    <label for="first_name">First Name<span class="required">*</span>:</label>
    <input type="text" name="first_name" id="first_name" required value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : htmlspecialchars($employee['first_name']); ?>">

    <label for="last_name">Last Name<span class="required">*</span>:</label>
    <input type="text" name="last_name" id="last_name" required value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : htmlspecialchars($employee['last_name']); ?>">

    <label for="email">Email<span class="required">*</span>:</label>
    <input type="email" name="email" id="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : htmlspecialchars($employee['email']); ?>">

    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : htmlspecialchars($employee['phone']); ?>">

    <label for="position">Position:</label>
    <input type="text" name="position" id="position" value="<?php echo isset($position) ? htmlspecialchars($position) : htmlspecialchars($employee['position']); ?>">

    <label for="department">Department:</label>
    <input type="text" name="department" id="department" value="<?php echo isset($department) ? htmlspecialchars($department) : htmlspecialchars($employee['department']); ?>">

    <button type="submit">Update Employee</button>
</form>

<?php include '../includes/footer.php'; ?>
