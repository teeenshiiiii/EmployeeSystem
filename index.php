<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<main class="dashboard-container">
    <h2>Dashboard</h2>

    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

    <div class="dashboard">
        <a href="employees/index.php" class="btn">Manage Employees</a>
        <a href="users/index.php" class="btn">Manage Users</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
