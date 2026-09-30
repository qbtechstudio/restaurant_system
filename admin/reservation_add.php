<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

$error = "";

$allowedTimes = [
    "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00",
    "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00"
];

$allowedStatuses = ["Pending", "Confirmed", "Completed", "Cancelled"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $reservation_date = trim($_POST["reservation_date"] ?? "");
    $reservation_time = trim($_POST["reservation_time"] ?? "");
    $guests = trim($_POST["guests"] ?? "");
    $special_request = trim($_POST["special_request"] ?? "");
    $status = $_POST["status"] ?? "Pending";


    // Full name validation
    if ($full_name === "") {

        $error = "Please enter the guest's full name.";

    }

    // Email validation
    elseif ($email === "") {

        $error = "Please enter an email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    // Phone validation
    elseif ($phone === "") {

        $error = "Please enter a phone number.";

    }

    // Date validation
    elseif ($reservation_date === "") {

        $error = "Please select a reservation date.";

    } elseif ($reservation_date < date("Y-m-d")) {

        $error = "Please select today or a future reservation date.";

    }

    // Time validation
    elseif (!in_array($reservation_time, $allowedTimes, true)) {

        $error = "Please select a valid reservation time.";

    }

    // Guests validation
    elseif (
        $guests === "" ||
        (int) $guests <= 0 ||
        (int) $guests > 9
    ) {

        $error = "Please select the number of guests.";

    }

    // Status validation
    elseif (!in_array($status, $allowedStatuses, true)) {

        $error = "Invalid status selected.";

    }


    if ($error === "") {

        $stmt = $pdo->prepare("
            INSERT INTO reservations
            (full_name, email, phone, reservation_date, reservation_time, guests, special_request, status)
            VALUES
            (:full_name, :email, :phone, :reservation_date, :reservation_time, :guests, :special_request, :status)
        ");

        $stmt->execute([
            ":full_name" => $full_name,
            ":email" => $email,
            ":phone" => $phone,
            ":reservation_date" => $reservation_date,
            ":reservation_time" => $reservation_time,
            ":guests" => (int) $guests,
            ":special_request" => $special_request !== "" ? $special_request : null,
            ":status" => $status
        ]);

        $_SESSION["success"] = "Reservation added successfully.";

        header("Location: reservations.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Reservation</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/admin.css">

    <script src="js/theme.js"></script>
</head>

<body>

<div class="admin-layout">

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <?php require_once "includes/sidebar.php"; ?>

    <main class="main-content">

        <div class="container-fluid p-4">

            <!-- PAGE HEADING -->
            <div class="page-heading users-page-heading">

                <div>

                    <p class="gold-label">
                        BITE & BLISS
                    </p>

                    <h1>
                        Add Reservation
                    </h1>

                    <p>
                        Create a new table reservation on behalf of a guest.
                    </p>

                </div>

                <a href="reservations.php" class="gold-btn">

                    <i class="bi bi-arrow-left"></i>

                    Back to Reservations

                </a>

            </div>


            <!-- ERROR MESSAGE -->
            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!-- RESERVATION FORM CARD -->
            <div class="dashboard-card user-form-card">

                <div class="card-header-custom">

                    <div>

                        <h5>
                            Reservation Information
                        </h5>

                        <p>
                            Enter the details for the new reservation.
                        </p>

                    </div>

                </div>


                <form method="POST">

                    <div class="row g-4">

                        <!-- FULL NAME -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="full_name"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST["full_name"] ?? "") ?>"
                            >

                        </div>


                        <!-- EMAIL -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="text"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                            >

                        </div>


                        <!-- PHONE -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>"
                            >

                        </div>


                        <!-- GUESTS -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Number of Guests
                            </label>

                            <select name="guests" class="form-select">

                                <option value="">Select guests</option>

                                <?php for ($i = 1; $i <= 9; $i++): ?>

                                    <option
                                        value="<?= $i ?>"
                                        <?= (($_POST["guests"] ?? "") === (string) $i) ? "selected" : "" ?>
                                    >
                                        <?= $i ?><?= $i === 9 ? "+" : "" ?> <?= $i === 1 ? "Person" : "People" ?>
                                    </option>

                                <?php endfor; ?>

                            </select>

                        </div>


                        <!-- DATE -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Reservation Date
                            </label>

                            <input
                                type="date"
                                name="reservation_date"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST["reservation_date"] ?? "") ?>"
                            >

                        </div>


                        <!-- TIME -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Preferred Time
                            </label>

                            <select name="reservation_time" class="form-select">

                                <option value="">Select time</option>

                                <?php

                                $timeLabels = [
                                    "11:00:00" => "11:00 AM",
                                    "12:00:00" => "12:00 PM",
                                    "13:00:00" => "01:00 PM",
                                    "14:00:00" => "02:00 PM",
                                    "15:00:00" => "03:00 PM",
                                    "17:00:00" => "05:00 PM",
                                    "18:00:00" => "06:00 PM",
                                    "19:00:00" => "07:00 PM",
                                    "20:00:00" => "08:00 PM",
                                    "21:00:00" => "09:00 PM",
                                    "22:00:00" => "10:00 PM"
                                ];

                                foreach ($timeLabels as $value => $label):
                                ?>

                                    <option
                                        value="<?= $value ?>"
                                        <?= (($_POST["reservation_time"] ?? "") === $value) ? "selected" : "" ?>
                                    >
                                        <?= $label ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- STATUS -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status" class="form-select">

                                <?php foreach ($allowedStatuses as $statusOption): ?>

                                    <option
                                        value="<?= $statusOption ?>"
                                        <?= (($_POST["status"] ?? "Pending") === $statusOption) ? "selected" : "" ?>
                                    >
                                        <?= $statusOption ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- SPECIAL REQUEST -->
                        <div class="col-md-12">

                            <label class="form-label">
                                Special Requests
                                <span class="optional-tag">(Optional)</span>
                            </label>

                            <textarea
                                name="special_request"
                                class="form-control"
                                rows="3"
                            ><?= htmlspecialchars($_POST["special_request"] ?? "") ?></textarea>

                        </div>

                    </div>


                    <!-- FORM BUTTONS -->
                    <div class="d-flex gap-2 mt-4 pt-3">

                        <button
                            type="submit"
                            class="btn-submit"
                        >

                            <i class="bi bi-calendar-plus"></i>

                            Add Reservation

                        </button>


                        <a
                            href="reservations.php"
                            class="btn-cancel"
                        >

                            Cancel

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>


<script src="js/admin.js"></script>

</body>

</html> 