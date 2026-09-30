<?php
session_start();

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

$host = "localhost";
$dbName = "restaurant_db";
$dbUser = "root";
$dbPass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


/*
|--------------------------------------------------------------------------
| Get Staff ID
|--------------------------------------------------------------------------
*/

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("Invalid staff ID.");
}


/*
|--------------------------------------------------------------------------
| Check If Staff Member Exists
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT id
        FROM staff
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $staff = $stmt->fetch();

    if (!$staff) {
        die("Staff member not found.");
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Staff Member
    |--------------------------------------------------------------------------
    */

    $deleteStmt = $pdo->prepare("
        DELETE FROM staff
        WHERE id = :id
    ");

    $deleteStmt->execute([
        ":id" => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Redirect After Successful Delete
    |--------------------------------------------------------------------------
    */

    header("Location: staff.php?success=deleted");
    exit;

} catch (PDOException $e) {

    die("Unable to delete staff member: " . $e->getMessage());
}
?>