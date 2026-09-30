
<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function tableExists($pdo, $table)
{
    try {

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
            AND table_name = ?
        ");

        $stmt->execute([$table]);

        return (bool)$stmt->fetchColumn();

    } catch (PDOException $e) {

        return false;
    }
}


function getTableCount($pdo, $table)
{
    if (!tableExists($pdo, $table)) {
        return 0;
    }

    try {

        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");

        return (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        return 0;
    }
}


/*
|--------------------------------------------------------------------------
| OVERVIEW
|--------------------------------------------------------------------------
*/

$totalUsers = getTableCount($pdo, "users");
$totalMenuItems = getTableCount($pdo, "menu_items");
$totalReservations = getTableCount($pdo, "reservations");
$totalMessages = getTableCount($pdo, "contact_messages");
$totalOrders = getTableCount($pdo, "orders");


/*
|--------------------------------------------------------------------------
| USER REPORT
|--------------------------------------------------------------------------
*/

$adminUsers = 0;
$normalUsers = 0;

if (tableExists($pdo, "users")) {

    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM users
            WHERE LOWER(role) = 'admin'
        ");

        $adminUsers = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $adminUsers = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM users
            WHERE LOWER(role) != 'admin'
            OR role IS NULL
        ");

        $normalUsers = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $normalUsers = 0;
    }
}


/*
|--------------------------------------------------------------------------
| RESERVATION REPORT
|--------------------------------------------------------------------------
*/

$pendingReservations = 0;
$confirmedReservations = 0;
$cancelledReservations = 0;
$todayReservations = 0;
$thisMonthReservations = 0;

if (tableExists($pdo, "reservations")) {

    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM reservations
            WHERE LOWER(status) = 'pending'
        ");

        $pendingReservations = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $pendingReservations = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM reservations
            WHERE LOWER(status) = 'confirmed'
        ");

        $confirmedReservations = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $confirmedReservations = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM reservations
            WHERE LOWER(status) = 'cancelled'
        ");

        $cancelledReservations = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $cancelledReservations = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM reservations
            WHERE reservation_date = CURDATE()
        ");

        $todayReservations = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $todayReservations = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM reservations
            WHERE MONTH(reservation_date) = MONTH(CURDATE())
            AND YEAR(reservation_date) = YEAR(CURDATE())
        ");

        $thisMonthReservations = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $thisMonthReservations = 0;
    }
}


/*
|--------------------------------------------------------------------------
| MENU REPORT
|--------------------------------------------------------------------------
*/

$starters = 0;
$mainCourse = 0;
$desserts = 0;
$drinks = 0;

if (tableExists($pdo, "menu_items")) {

    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM menu_items
            WHERE LOWER(category) = 'starters'
        ");

        $starters = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $starters = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM menu_items
            WHERE LOWER(category) = 'main course'
        ");

        $mainCourse = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $mainCourse = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM menu_items
            WHERE LOWER(category) = 'desserts'
        ");

        $desserts = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $desserts = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM menu_items
            WHERE LOWER(category) = 'drinks'
        ");

        $drinks = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $drinks = 0;
    }
}


/*
|--------------------------------------------------------------------------
| ORDER REPORT
|--------------------------------------------------------------------------
*/

$pendingOrders = 0;
$completedOrders = 0;
$cancelledOrders = 0;
$totalRevenue = 0;

if (tableExists($pdo, "orders")) {

    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM orders
            WHERE LOWER(status) = 'pending'
        ");

        $pendingOrders = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $pendingOrders = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM orders
            WHERE LOWER(status) = 'completed'
        ");

        $completedOrders = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $completedOrders = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM orders
            WHERE LOWER(status) = 'cancelled'
        ");

        $cancelledOrders = (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $cancelledOrders = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE LOWER(status) = 'completed'
        ");

        $totalRevenue = (float)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $totalRevenue = 0;
    }
}


/*
|--------------------------------------------------------------------------
| MONTHLY RESERVATIONS
|--------------------------------------------------------------------------
*/

$monthlyReservations = [];

if (tableExists($pdo, "reservations")) {

    try {

        $stmt = $pdo->query("
            SELECT
                DATE_FORMAT(reservation_date, '%Y-%m') AS month,
                COUNT(*) AS total
            FROM reservations
            GROUP BY YEAR(reservation_date), MONTH(reservation_date)
            ORDER BY YEAR(reservation_date) DESC,
                     MONTH(reservation_date) DESC
            LIMIT 12
        ");

        $monthlyReservations = $stmt->fetchAll();

        /*
        | Reverse so the oldest month appears first
        | in the visual report.
        */

        $monthlyReservations = array_reverse($monthlyReservations);

    } catch (PDOException $e) {

        $monthlyReservations = [];
    }
}


/*
|--------------------------------------------------------------------------
| MONTH NAME FORMAT
|--------------------------------------------------------------------------
*/

function formatReportMonth($month)
{
    $date = DateTime::createFromFormat('Y-m', $month);

    if (!$date) {
        return htmlspecialchars($month);
    }

    return $date->format('M Y');
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Bite & Bliss | Reports</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">


    <!-- Admin Theme -->

    <link
        rel="stylesheet"
        href="css/admin.css">

        <link rel="stylesheet" href="css/reports.css">


    <!-- Theme -->

    <script src="js/theme.js"></script>


   

</head>


<body>

<div class="admin-layout">


    <!-- SIDEBAR OVERLAY -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay">
    </div>


    <!-- SIDEBAR -->

    <?php require_once "includes/sidebar.php"; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <!-- =================================================
             REPORT BODY
        ================================================== -->

        <section class="reports-body">


            <!-- PAGE HEADING -->

            <div class="reports-heading">

                <div>

                    <p class="report-label">
                        BITE & BLISS
                    </p>

                    <h1>
                        Reports & Analytics
                    </h1>

                    <p>
                        Monitor users, menu items, reservations,
                        orders and restaurant revenue.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 OVERVIEW CARDS
            ================================================== -->

            <div class="row g-4 mb-4">


                <!-- USERS -->

                <div class="col-xl-3 col-md-6">

                    <div class="report-stat">

                        <div class="report-stat-top">

                            <div class="report-icon">
                                <i class="bi bi-people"></i>
                            </div>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                        <div class="report-stat-label">
                            Total Users
                        </div>

                        <h2>
                            <?= number_format($totalUsers) ?>
                        </h2>

                        <div class="report-stat-footer">
                            Registered users
                        </div>

                    </div>

                </div>


                <!-- MENU -->

                <div class="col-xl-3 col-md-6">

                    <div class="report-stat">

                        <div class="report-stat-top">

                            <div class="report-icon">
                                <i class="bi bi-journal-text"></i>
                            </div>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                        <div class="report-stat-label">
                            Menu Items
                        </div>

                        <h2>
                            <?= number_format($totalMenuItems) ?>
                        </h2>

                        <div class="report-stat-footer">
                            Available menu items
                        </div>

                    </div>

                </div>


                <!-- RESERVATIONS -->

                <div class="col-xl-3 col-md-6">

                    <div class="report-stat">

                        <div class="report-stat-top">

                            <div class="report-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                        <div class="report-stat-label">
                            Reservations
                        </div>

                        <h2>
                            <?= number_format($totalReservations) ?>
                        </h2>

                        <div class="report-stat-footer">
                            All reservations
                        </div>

                    </div>

                </div>


                <!-- ORDERS -->

                <div class="col-xl-3 col-md-6">

                    <div class="report-stat">

                        <div class="report-stat-top">

                            <div class="report-icon">
                                <i class="bi bi-receipt"></i>
                            </div>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                        <div class="report-stat-label">
                            Total Orders
                        </div>

                        <h2>
                            <?= number_format($totalOrders) ?>
                        </h2>

                        <div class="report-stat-footer">
                            All customer orders
                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 REVENUE
            ================================================== -->

            <div class="row g-4 mb-4">

                <div class="col-lg-8">

                    <div class="report-card">

                        <div class="report-card-header">

                            <div>

                                <h5>
                                    Restaurant Revenue
                                </h5>

                                <p>
                                    Revenue from completed orders
                                </p>

                            </div>

                            <i class="bi bi-currency-dollar"></i>

                        </div>


                        <div class="p-4">

                            <div class="revenue-box">

                                <small>
                                    TOTAL COMPLETED-ORDER REVENUE
                                </small>

                                <strong>
                                    <?= number_format($totalRevenue, 2) ?>
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- MESSAGES -->

                <div class="col-lg-4">

                    <div class="report-card">

                        <div class="report-card-header">

                            <div>

                                <h5>
                                    Contact Messages
                                </h5>

                                <p>
                                    Messages received
                                </p>

                            </div>

                            <i class="bi bi-envelope"></i>

                        </div>

                        <div class="p-4 text-center">

                            <div class="report-icon mx-auto mb-3">

                                <i class="bi bi-envelope-open"></i>

                            </div>

                            <h2 class="mb-1">

                                <?= number_format($totalMessages) ?>

                            </h2>

                            <p class="small mb-0">

                                Total messages

                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 USER + RESERVATION
            ================================================== -->

            <div class="row g-4 mb-4">


                <!-- USER REPORT -->

                <div class="col-lg-6">

                    <div class="report-card">

                        <div class="report-card-header">

                            <div>

                                <h5>
                                    User Report
                                </h5>

                                <p>
                                    User account distribution
                                </p>

                            </div>

                            <i class="bi bi-people"></i>

                        </div>


                        <div class="report-table-wrapper">

                            <table class="report-table">

                                <thead>

                                    <tr>

                                        <th>
                                            User Type
                                        </th>

                                        <th class="text-end">
                                            Total
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr>

                                        <td>
                                            All Users
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $totalUsers ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Admin Users
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $adminUsers ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Normal Users
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $normalUsers ?>
                                            </span>

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>


                <!-- RESERVATION REPORT -->

                <div class="col-lg-6">

                    <div class="report-card">

                        <div class="report-card-header">

                            <div>

                                <h5>
                                    Reservation Report
                                </h5>

                                <p>
                                    Current reservation status
                                </p>

                            </div>

                            <i class="bi bi-calendar-check"></i>

                        </div>


                        <div class="report-table-wrapper">

                            <table class="report-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Reservation
                                        </th>

                                        <th class="text-end">
                                            Total
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr>

                                        <td>
                                            All Reservations
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $totalReservations ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            <span class="status-badge pending">
                                                <i class="bi bi-clock"></i>
                                                Pending
                                            </span>
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $pendingReservations ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            <span class="status-badge confirmed">
                                                <i class="bi bi-check-circle"></i>
                                                Confirmed
                                            </span>
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $confirmedReservations ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            <span class="status-badge cancelled">
                                                <i class="bi bi-x-circle"></i>
                                                Cancelled
                                            </span>
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $cancelledReservations ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Today's Reservations
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $todayReservations ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            This Month
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $thisMonthReservations ?>
                                            </span>

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 MENU + ORDERS
            ================================================== -->

            <div class="row g-4 mb-4">


                <!-- MENU REPORT -->

                <div class="col-lg-6">

                    <div class="report-card">

                        <div class="report-card-header">

                            <div>

                                <h5>
                                    Menu Report
                                </h5>

                                <p>
                                    Menu items by category
                                </p>

                            </div>

                            <i class="bi bi-journal-text"></i>

                        </div>


                        <div class="report-table-wrapper">

                            <table class="report-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Category
                                        </th>

                                        <th class="text-end">
                                            Items
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr>

                                        <td>
                                            Starters
                                        </td>

                                        <td class="text-end">
                                            <span class="number-badge">
                                                <?= $starters ?>
                                            </span>
                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Main Course
                                        </td>

                                        <td class="text-end">
                                            <span class="number-badge">
                                                <?= $mainCourse ?>
                                            </span>
                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Desserts
                                        </td>

                                        <td class="text-end">
                                            <span class="number-badge">
                                                <?= $desserts ?>
                                            </span>
                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Drinks
                                        </td>

                                        <td class="text-end">
                                            <span class="number-badge">
                                                <?= $drinks ?>
                                            </span>
                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            <strong>
                                                Total Menu Items
                                            </strong>
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">

                                                <strong>
                                                    <?= $totalMenuItems ?>
                                                </strong>

                                            </span>

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>


                <!-- ORDER REPORT -->

                <div class="col-lg-6">

                    <div class="report-card">

                        <div class="report-card-header">

                            <div>

                                <h5>
                                    Order Report
                                </h5>

                                <p>
                                    Current order status
                                </p>

                            </div>

                            <i class="bi bi-receipt"></i>

                        </div>


                        <div class="report-table-wrapper">

                            <table class="report-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Order
                                        </th>

                                        <th class="text-end">
                                            Total
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr>

                                        <td>
                                            All Orders
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $totalOrders ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>

                                            <span class="status-badge pending">
                                                <i class="bi bi-clock"></i>
                                                Pending
                                            </span>

                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $pendingOrders ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>

                                            <span class="status-badge completed">
                                                <i class="bi bi-check-circle"></i>
                                                Completed
                                            </span>

                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $completedOrders ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>

                                            <span class="status-badge cancelled">
                                                <i class="bi bi-x-circle"></i>
                                                Cancelled
                                            </span>

                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= $cancelledOrders ?>
                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Completed Revenue
                                        </td>

                                        <td class="text-end">

                                            <span class="number-badge">
                                                <?= number_format($totalRevenue, 2) ?>
                                            </span>

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 MONTHLY RESERVATIONS
            ================================================== -->

            <div class="row g-4">


                <div class="col-12">

                    <div class="report-card">

                        <div class="report-card-header">

                            <div>

                                <h5>
                                    Monthly Reservation Report
                                </h5>

                                <p>
                                    Reservation activity for the latest months
                                </p>

                            </div>

                            <i class="bi bi-bar-chart-line"></i>

                        </div>


                        <?php if (!empty($monthlyReservations)): ?>

                            <?php

                            $maxReservations = max(
                                array_column(
                                    $monthlyReservations,
                                    'total'
                                )
                            );

                            $maxReservations = max(
                                1,
                                (int)$maxReservations
                            );

                            ?>


                            <div>

                                <?php foreach ($monthlyReservations as $row): ?>

                                    <?php

                                    $reservationTotal =
                                        (int)$row['total'];

                                    $percentage =
                                        ($reservationTotal / $maxReservations) * 100;

                                    ?>


                                    <div class="month-row">

                                        <div class="month-name">

                                            <?= formatReportMonth($row['month']) ?>

                                        </div>


                                        <div class="month-progress">

                                            <div
                                                class="month-progress-bar"
                                                style="width: <?= $percentage ?>%;">
                                            </div>

                                        </div>


                                        <div class="month-total">

                                            <?= $reservationTotal ?>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>


                        <?php else: ?>


                            <div class="empty-report">

                                <i class="bi bi-bar-chart"></i>

                                No reservation data found.

                            </div>


                        <?php endif; ?>

                    </div>

                </div>

            </div>


        </section>

    </main>

</div>


<!-- ADMIN JAVASCRIPT -->

<script src="js/admin.js"></script>

</body>

</html>