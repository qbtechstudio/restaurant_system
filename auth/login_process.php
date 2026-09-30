<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php");
    exit;
}

$login = trim($_POST["login"] ?? "");
$password = $_POST["password"] ?? "";

// VALIDATION
if ($login === "") {
    $_SESSION["error"] = "Please enter your username or email.";
    header("Location: ../login.php");
    exit;
}

if ($password === "") {
    $_SESSION["error"] = "Please enter your password.";
    header("Location: ../login.php");
    exit;
}

// FIND USER
$query = "SELECT * FROM users WHERE username = :login OR email = :login LIMIT 1 ";

$stmt = $pdo->prepare($query);

$stmt->execute([
    ":login" => $login
]);

$user = $stmt->fetch();

// CHECK USER
if (!$user) {
    $_SESSION["error"] = "Invalid username/email or password.";
    header("Location: ../login.php");
    exit;
}

// CHECK PASSWORD
if (!password_verify($password, $user["password"])) {
    $_SESSION["error"] = "Invalid username/email or password.";
    header("Location: ../login.php");
    exit;
}

// CHECK ACCOUNT STATUS
if ((int) $user["status"] !== 1) {
    $_SESSION["error"] = "Your account is inactive.";
    header("Location: ../login.php");
    exit;
}

// CREATE SESSION
session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["name"] = $user["name"];
$_SESSION["username"] = $user["username"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];


// ADMIN LOGIN
if ($user["role"] === "admin") {
    header("Location: ../admin/dashboard.php");
    exit;
}

// USER LOGIN
header("Location: ../index.php");
exit;