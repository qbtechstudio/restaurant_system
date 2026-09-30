<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["error"] = "Invalid user.";
    header("Location: users.php");
    exit;
}

// Don't allow admin to delete their own account
if ($id === (int)$_SESSION["user_id"]) {
    $_SESSION["error"] = "You cannot delete your own account.";
    header("Location: users.php");
    exit;
}

// Check whether user exists
$stmt = $pdo->prepare(
    "SELECT id FROM users WHERE id = :id LIMIT 1"
);

$stmt->execute([
    ":id" => $id
]);

$user = $stmt->fetch();

if (!$user) {
    $_SESSION["error"] = "User not found.";
    header("Location: users.php");
    exit;
}

// Delete user
$stmt = $pdo->prepare(
    "DELETE FROM users WHERE id = :id"
);

$stmt->execute([
    ":id" => $id
]);

$_SESSION["success"] = "User deleted successfully.";
header("Location: users.php");
exit;