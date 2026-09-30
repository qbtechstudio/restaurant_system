<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

// ONLY ALLOW POST REQUEST

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact_messages.php");
    exit;
}

// GET MESSAGE ID

$id = (int) ($_POST["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["error"] = "Invalid message.";
    header("Location: contact_messages.php");
    exit;
}

// CHECK MESSAGE EXISTS

$stmt = $pdo->prepare(
    "SELECT id FROM contact_messages WHERE id = :id LIMIT 1"
);

$stmt->execute([
    ":id" => $id
]);

$message = $stmt->fetch();

if (!$message) {
    $_SESSION["error"] = "Contact message not found.";
    header("Location: contact_messages.php");
    exit;
}

// DELETE MESSAGE

$stmt = $pdo->prepare(
    "DELETE FROM contact_messages WHERE id = :id"
);

$stmt->execute([
    ":id" => $id
]);

// SUCCESS

$_SESSION["success"] = "Contact message deleted successfully.";
header("Location: contact_messages.php");
exit;