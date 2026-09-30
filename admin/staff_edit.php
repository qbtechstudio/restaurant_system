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
| Fetch Staff Member
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT id, name, position, phone, email, address, status
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

} catch (PDOException $e) {

    die("Unable to fetch staff member: " . $e->getMessage());
}


/*
|--------------------------------------------------------------------------
| Variables
|--------------------------------------------------------------------------
*/

$name = $staff['name'];
$position = $staff['position'];
$phone = $staff['phone'];
$email = $staff['email'] ?? "";
$address = $staff['address'] ?? "";
$status = $staff['status'];
$staffPositions = ["Manager", "Chef", "Waiter", "Cashier", "Delivery Rider"];

$errorMessage = "";


/*
|--------------------------------------------------------------------------
| Handle Update
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $position = trim($_POST["position"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $status = trim($_POST["status"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($name === "") {

        $errorMessage = "Staff name is required.";

    } elseif (!in_array($position, $staffPositions, true)) {

        $errorMessage = "Please select a valid staff position.";

    } elseif ($phone === "") {

        $errorMessage = "Phone number is required.";

    } elseif (!in_array($status, ["Active", "On Leave", "Inactive"], true)) {

        $errorMessage = "Invalid staff status.";

    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errorMessage = "Please enter a valid email address.";

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | Update Staff
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE staff
                SET
                    name = :name,
                    position = :position,
                    phone = :phone,
                    email = :email,
                    address = :address,
                    status = :status
                WHERE id = :id
            ");

            $stmt->execute([
                ":name" => $name,
                ":position" => $position,
                ":phone" => $phone,
                ":email" => $email !== "" ? $email : null,
                ":address" => $address !== "" ? $address : null,
                ":status" => $status,
                ":id" => $id
            ]);


            /*
            |--------------------------------------------------------------------------
            | Redirect After Successful Update
            |--------------------------------------------------------------------------
            */

            header("Location: staff.php?success=updated");
            exit;

        } catch (PDOException $e) {

            $errorMessage = "Unable to update staff member: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Staff</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/staff.css">
    <script src="js/theme.js"></script>

</head>

<body class="staff-form-page">

<div class="admin-layout">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php require_once "includes/sidebar.php"; ?>
    <main class="main-content">
    <div class="container-fluid p-4">

    <div class="page-heading users-page-heading">
        <div>
            <p class="gold-label">BITE &amp; BLISS</p>
            <h1>Edit Staff</h1>
            <p>Update this team member's information and status.</p>
        </div>
        <a href="staff.php" class="gold-btn"><i class="bi bi-arrow-left"></i> Back to Staff</a>
    </div>


    <!-- Back to Staff -->

    <!-- Error Message -->

    <?php if ($errorMessage !== ""): ?>

        <div class="alert alert-danger staff-form-alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>

    <?php endif; ?>


    <!-- Edit Staff Form -->

    <div class="dashboard-card user-form-card staff-form-card">
    <div class="card-header-custom">
        <div>
            <h5><i class="bi bi-person-badge-fill"></i> Staff Information</h5>
            <p>Update the details for this team member.</p>
        </div>
    </div>
    <form method="POST" action="">

        <!-- Name -->

        <div>

            <label for="name" class="form-label">
                Name
            </label>

            <br>

            <input class="form-control"
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($name); ?>"
                required
            >

        </div>

        <br>


        <!-- Position -->

        <div>

            <label for="position" class="form-label">
                Position
            </label>

            <br>

            <select id="position" name="position" class="form-select" required>
                <option value="" disabled <?php echo !in_array($position, $staffPositions, true) ? "selected" : ""; ?>>Select a position</option>
                <?php foreach ($staffPositions as $staffPosition): ?>
                    <option value="<?php echo htmlspecialchars($staffPosition); ?>" <?php echo ($position === $staffPosition) ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($staffPosition); ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </div>

        <br>


        <!-- Phone -->

        <div>

            <label for="phone" class="form-label">
                Phone
            </label>

            <br>

            <input class="form-control"
                type="text"
                id="phone"
                name="phone"
                value="<?php echo htmlspecialchars($phone); ?>"
                required
            >

        </div>

        <br>


        <!-- Email -->

        <div>

            <label for="email" class="form-label">
                Email
            </label>

            <br>

            <input class="form-control"
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email); ?>"
            >

        </div>

        <br>


        <!-- Address -->

        <div>

            <label for="address" class="form-label">
                Address
            </label>

            <br>

            <textarea class="form-control"
                id="address"
                name="address"
                rows="4"
                cols="40"
            ><?php echo htmlspecialchars($address); ?></textarea>

        </div>

        <br>


        <!-- Status -->

        <div>

            <label for="status" class="form-label">
                Status
            </label>

            <br>

            <select id="status" name="status" class="form-select">

                <option
                    value="Active"
                    <?php echo ($status === "Active") ? "selected" : ""; ?>
                >
                    Active
                </option>

                <option
                    value="On Leave"
                    <?php echo ($status === "On Leave") ? "selected" : ""; ?>
                >
                    On Leave
                </option>

                <option
                    value="Inactive"
                    <?php echo ($status === "Inactive") ? "selected" : ""; ?>
                >
                    Inactive
                </option>

            </select>

        </div>

        <br>


        <!-- Buttons -->

        <button type="submit" class="btn-submit">
            <i class="bi bi-check2-circle"></i>
            Update Staff
        </button>

        <a href="staff.php" class="btn-cancel">
            Cancel
        </a>

    </form>
    </div>

    </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/admin.js"></script>

</body>

</html>
