<?php
require_once '../includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    if($user_id == $_SESSION['user_id']) {
        echo "You cannot delete your own account.";
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: index.php");
    exit();
}
?>
