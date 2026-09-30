<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../register.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");

$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

if (
    empty($name) ||
    empty($username) ||
    empty($email) ||
    empty($password) ||
    empty($confirm_password)
) {
    $_SESSION["error"] = "Please fill all fields.";
    header("Location: ../register.php");
    exit;
}

if (!preg_match('/^[a-zA-Z ]{2,50}$/', $name)) {
    $_SESSION["error"] = "Name must be 2-50 characters and contain only letters and spaces.";
    header("Location: ../register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Please enter a valid email.";
    header("Location: ../register.php");
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
    $_SESSION["error"] = "Username must be 3-30 characters and contain only letters, numbers, and underscores.";
    header("Location: ../register.php");
    exit;
}

if (strlen($password) < 6) {
    $_SESSION["error"] = "Password must be at least 6 characters.";
    header("Location: ../register.php");
    exit;
}

if ($password !== $confirm_password) {

    $_SESSION["error"] = "Passwords do not match.";

    header("Location: ../register.php");
    exit;
}

try {

    $checkQuery = "SELECT id FROM users WHERE username = :username OR email = :email";
    $checkStmt = $pdo->prepare($checkQuery);

    $checkStmt->execute([
        ":username" => $username,
        ":email" => $email
    ]);

    if ($checkStmt->fetch()) {
        $_SESSION["error"] = "Username or email already exists.";
        header("Location: ../register.php");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $insertQuery = "INSERT INTO users(name, username, email, password, role, status)
        VALUES(:name, :username, :email, :password, 'user', 1)";

    $insertStmt = $pdo->prepare($insertQuery);

    $insertStmt->execute([
        ":name" => $name,
        ":username" => $username,
        ":email" => $email,
        ":password" => $hashedPassword
    ]);

    $_SESSION["success"] = "Registration successful. Please login.";

    header("Location: ../login.php");
    exit;
} catch (PDOException $e) {

    $_SESSION["error"] = "Registration failed. Try again.";
    header("Location: ../register.php");
    exit;
}