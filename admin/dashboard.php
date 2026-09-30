<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| HELPER
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


/*
|--------------------------------------------------------------------------
| CURRENT USER
|--------------------------------------------------------------------------
*/

$currentUserName =
    $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Administrator";

$currentUserName =
    htmlspecialchars(
        $currentUserName,
        ENT_QUOTES,
        "UTF-8"
    );


/*
|--------------------------------------------------------------------------
| TOTAL USERS
|--------------------------------------------------------------------------
*/

$totalUsers = 0;

if (tableExists($pdo, "users")) {

    try {

        $totalUsers = (int)$pdo
            ->query("SELECT COUNT(*) FROM users")
            ->fetchColumn();

    } catch (PDOException $e) {

        $totalUsers = 0;
    }
}


/*
|--------------------------------------------------------------------------
| TOTAL ORDERS
|--------------------------------------------------------------------------
*/

$totalOrders = 0;

if (tableExists($pdo, "orders")) {

    try {

        $totalOrders = (int)$pdo
            ->query("SELECT COUNT(*) FROM orders")
            ->fetchColumn();

    } catch (PDOException $e) {

        $totalOrders = 0;
    }
}


/*
|--------------------------------------------------------------------------
| TOTAL REVENUE
|--------------------------------------------------------------------------
|
| Cancelled orders are excluded.
|
*/

$totalRevenue = 0;

if (tableExists($pdo, "orders")) {

    try {

        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE LOWER(status) != 'cancelled'
        ");

        $totalRevenue =
            (float)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $totalRevenue = 0;
    }
}


/*
|--------------------------------------------------------------------------
| TOTAL RESERVATIONS
|--------------------------------------------------------------------------
*/

$totalReservations = 0;

if (tableExists($pdo, "reservations")) {

    try {

        $totalReservations = (int)$pdo
            ->query("SELECT COUNT(*) FROM reservations")
            ->fetchColumn();

    } catch (PDOException $e) {

        $totalReservations = 0;
    }
}


/*
|--------------------------------------------------------------------------
| ORDER STATUS COUNTS
|--------------------------------------------------------------------------
*/

$completedOrders = 0;
$pendingOrders = 0;
$cancelledOrders = 0;
$confirmedOrders = 0;
$preparingOrders = 0;
$readyOrders = 0;


if (tableExists($pdo, "orders")) {

    try {

        $stmt = $pdo->query("
            SELECT
                LOWER(status) AS status,
                COUNT(*) AS total
            FROM orders
            GROUP BY LOWER(status)
        ");

        $orderStatusRows =
            $stmt->fetchAll();

        foreach ($orderStatusRows as $row) {

            $status =
                strtolower(
                    trim($row["status"] ?? "")
                );

            $count =
                (int)$row["total"];

            switch ($status) {

                case "completed":
                    $completedOrders = $count;
                    break;

                case "pending":
                    $pendingOrders = $count;
                    break;

                case "cancelled":
                    $cancelledOrders = $count;
                    break;

                case "confirmed":
                    $confirmedOrders = $count;
                    break;

                case "preparing":
                    $preparingOrders = $count;
                    break;

                case "ready":
                    $readyOrders = $count;
                    break;
            }
        }

    } catch (PDOException $e) {

        $completedOrders = 0;
        $pendingOrders = 0;
        $cancelledOrders = 0;
        $confirmedOrders = 0;
        $preparingOrders = 0;
        $readyOrders = 0;
    }
}


/*
|--------------------------------------------------------------------------
| TODAY'S ORDERS
|--------------------------------------------------------------------------
*/

$todayOrders = 0;

if (tableExists($pdo, "orders")) {

    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM orders
            WHERE DATE(created_at) = CURDATE()
        ");

        $todayOrders =
            (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $todayOrders = 0;
    }
}


/*
|--------------------------------------------------------------------------
| TODAY'S REVENUE
|--------------------------------------------------------------------------
*/

$todayRevenue = 0;

if (tableExists($pdo, "orders")) {

    try {

        $stmt = $pdo->query("
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE DATE(created_at) = CURDATE()
            AND LOWER(status) != 'cancelled'
        ");

        $todayRevenue =
            (float)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $todayRevenue = 0;
    }
}


/*
|--------------------------------------------------------------------------
| RESERVATION COUNTS
|--------------------------------------------------------------------------
*/

$pendingReservations = 0;
$confirmedReservations = 0;
$cancelledReservations = 0;
$todayReservations = 0;


if (tableExists($pdo, "reservations")) {

    try {

        $stmt = $pdo->query("
            SELECT
                LOWER(status) AS status,
                COUNT(*) AS total
            FROM reservations
            GROUP BY LOWER(status)
        ");

        $reservationStatuses =
            $stmt->fetchAll();

        foreach ($reservationStatuses as $row) {

            $status =
                strtolower(
                    trim($row["status"] ?? "")
                );

            $count =
                (int)$row["total"];

            if ($status === "pending") {
                $pendingReservations = $count;
            }

            elseif ($status === "confirmed") {
                $confirmedReservations = $count;
            }

            elseif ($status === "cancelled") {
                $cancelledReservations = $count;
            }
        }

    } catch (PDOException $e) {

        $pendingReservations = 0;
        $confirmedReservations = 0;
        $cancelledReservations = 0;
    }


    try {

        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM reservations
            WHERE reservation_date = CURDATE()
        ");

        $todayReservations =
            (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        $todayReservations = 0;
    }
}


/*
|--------------------------------------------------------------------------
| MENU ITEMS
|--------------------------------------------------------------------------
*/

$totalMenuItems = 0;

if (tableExists($pdo, "menu_items")) {

    try {

        $totalMenuItems =
            (int)$pdo
                ->query("SELECT COUNT(*) FROM menu_items")
                ->fetchColumn();

    } catch (PDOException $e) {

        $totalMenuItems = 0;
    }
}


/*
|--------------------------------------------------------------------------
| CONTACT MESSAGES
|--------------------------------------------------------------------------
*/

$totalMessages = 0;

if (tableExists($pdo, "contact_messages")) {

    try {

        $totalMessages =
            (int)$pdo
                ->query("SELECT COUNT(*) FROM contact_messages")
                ->fetchColumn();

    } catch (PDOException $e) {

        $totalMessages = 0;
    }
}


/*
|--------------------------------------------------------------------------
| RECENT ORDERS
|--------------------------------------------------------------------------
*/

$recentOrders = [];

if (tableExists($pdo, "orders")) {

    try {

        $stmt = $pdo->query("
            SELECT
                id,
                full_name,
                email,
                total_amount,
                status,
                created_at
            FROM orders
            ORDER BY id DESC
            LIMIT 6
        ");

        $recentOrders =
            $stmt->fetchAll();

    } catch (PDOException $e) {

        $recentOrders = [];
    }
}


/*
|--------------------------------------------------------------------------
| ORDER ITEMS COUNT
|--------------------------------------------------------------------------
*/

$orderItemCounts = [];

if (
    tableExists($pdo, "order_items") &&
    !empty($recentOrders)
) {

    try {

        $orderIds =
            array_column(
                $recentOrders,
                "id"
            );

        $placeholders =
            implode(
                ",",
                array_fill(
                    0,
                    count($orderIds),
                    "?"
                )
            );

        $stmt = $pdo->prepare("
            SELECT
                order_id,
                SUM(quantity) AS total_items
            FROM order_items
            WHERE order_id IN ($placeholders)
            GROUP BY order_id
        ");

        $stmt->execute($orderIds);

        foreach (
            $stmt->fetchAll()
            as $row
        ) {

            $orderItemCounts[
                (int)$row["order_id"]
            ] =
                (int)$row["total_items"];
        }

    } catch (PDOException $e) {

        $orderItemCounts = [];
    }
}


/*
|--------------------------------------------------------------------------
| MONTHLY REVENUE
|--------------------------------------------------------------------------
*/

$monthlyRevenue = [];

if (tableExists($pdo, "orders")) {

    try {

        $stmt = $pdo->query("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                COALESCE(SUM(total_amount), 0) AS revenue
            FROM orders
            WHERE LOWER(status) != 'cancelled'
            AND created_at >= DATE_SUB(
                CURDATE(),
                INTERVAL 11 MONTH
            )
            GROUP BY
                YEAR(created_at),
                MONTH(created_at)
            ORDER BY
                YEAR(created_at),
                MONTH(created_at)
        ");

        $monthlyRevenue =
            $stmt->fetchAll();

    } catch (PDOException $e) {

        $monthlyRevenue = [];
    }
}


/*
|--------------------------------------------------------------------------
| CREATE LAST 12 MONTHS
|--------------------------------------------------------------------------
|
| This ensures the chart always has 12 months,
| even when some months have no orders.
|
*/

$chartLabels = [];
$chartValues = [];

$revenueMap = [];

foreach ($monthlyRevenue as $row) {

    $revenueMap[
        $row["month"]
    ] =
        (float)$row["revenue"];
}


for ($i = 11; $i >= 0; $i--) {

    $date =
        new DateTime(
            "first day of -" . $i . " months"
        );

    $monthKey =
        $date->format("Y-m");

    $chartLabels[] =
        $date->format("M");

    $chartValues[] =
        $revenueMap[$monthKey] ?? 0;
}


/*
|--------------------------------------------------------------------------
| INITIALS
|--------------------------------------------------------------------------
*/

$initials = "AD";

$nameParts =
    preg_split(
        "/\s+/",
        trim(
            strip_tags($currentUserName)
        )
    );

if (count($nameParts) >= 2) {

    $initials =
        strtoupper(
            substr($nameParts[0], 0, 1) .
            substr(
                $nameParts[
                    count($nameParts) - 1
                ],
                0,
                1
            )
        );

} elseif (!empty($nameParts[0])) {

    $initials =
        strtoupper(
            substr(
                $nameParts[0],
                0,
                2
            )
        );
}


/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

function dashboardStatusClass($status)
{
    $status =
        strtolower(
            trim($status)
        );

    switch ($status) {

        case "completed":
            return "completed";

        case "pending":
            return "pending";

        case "cancelled":
            return "cancelled";

        default:
            return "pending";
    }
}


/*
|--------------------------------------------------------------------------
| STATUS LABEL
|--------------------------------------------------------------------------
*/

function dashboardStatusLabel($status)
{
    return htmlspecialchars(
        $status ?: "Unknown",
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Bite & Bliss | Admin Dashboard</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">


    <!-- Admin CSS -->

    <link
        rel="stylesheet"
        href="css/admin.css">


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


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="topbar-left">

                <button
                    class="menu-toggle"
                    id="menuToggle">

                    <i class="bi bi-list"></i>

                </button>


                <div>

                    <h5>
                        Dashboard
                    </h5>

                    <p>
                        Welcome back,
                        <?= $currentUserName ?>
                    </p>

                </div>

            </div>


            <div class="topbar-right">

                <div class="profile">

                    <div class="profile-image">

                        <?= htmlspecialchars(
                            $initials,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </div>


                    <div class="profile-text">

                        <strong>
                            <?= $currentUserName ?>
                        </strong>

                        <small>
                            Administrator
                        </small>

                    </div>

                </div>

            </div>

        </header>


        <!-- =====================================================
             DASHBOARD BODY
        ====================================================== -->

        <section class="dashboard-body">


            <!-- PAGE HEADING -->

            <div class="page-heading">

                <div>

                    <p class="gold-label">
                        BITE & BLISS
                    </p>

                    <h1>
                        Dashboard Overview
                    </h1>

                    <p>
                        Manage your restaurant and track
                        performance.
                    </p>

                </div>


                <a
                    href="orders.php"
                    class="gold-btn text-decoration-none">

                    <i class="bi bi-receipt"></i>

                    View Orders

                </a>

            </div>


            <!-- =================================================
                 STAT CARDS
            ================================================== -->

            <div class="row g-4 mb-4">


                <!-- TOTAL ORDERS -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="stat-card-top">

                            <div class="stat-icon">

                                <i class="bi bi-receipt"></i>

                            </div>


                            <span class="stat-change">

                                <i class="bi bi-clock"></i>

                                <?= $pendingOrders ?>

                            </span>

                        </div>


                        <p>
                            Total Orders
                        </p>


                        <h2>
                            <?= number_format(
                                $totalOrders
                            ) ?>
                        </h2>


                        <div class="stat-bottom">

                            <span>
                                <?= $todayOrders ?>
                                today
                            </span>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                    </div>

                </div>


                <!-- TOTAL REVENUE -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="stat-card-top">

                            <div class="stat-icon">

                                <i class="bi bi-currency-dollar"></i>

                            </div>


                            <span class="stat-change">

                                Today:
                                <?= number_format(
                                    $todayRevenue,
                                    2
                                ) ?>

                            </span>

                        </div>


                        <p>
                            Total Revenue
                        </p>


                        <h2>

                            <?= number_format(
                                $totalRevenue,
                                2
                            ) ?>

                        </h2>


                        <div class="stat-bottom">

                            <span>
                                Excluding cancelled orders
                            </span>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                    </div>

                </div>


                <!-- USERS -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="stat-card-top">

                            <div class="stat-icon">

                                <i class="bi bi-people"></i>

                            </div>


                            <span class="stat-change">

                                <i class="bi bi-person"></i>

                                Users

                            </span>

                        </div>


                        <p>
                            Total Customers
                        </p>


                        <h2>
                            <?= number_format(
                                $totalUsers
                            ) ?>
                        </h2>


                        <div class="stat-bottom">

                            <span>
                                Registered accounts
                            </span>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                    </div>

                </div>


                <!-- RESERVATIONS -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="stat-card-top">

                            <div class="stat-icon">

                                <i class="bi bi-calendar-check"></i>

                            </div>


                            <span class="stat-change">

                                Today:
                                <?= $todayReservations ?>

                            </span>

                        </div>


                        <p>
                            Reservations
                        </p>


                        <h2>
                            <?= number_format(
                                $totalReservations
                            ) ?>
                        </h2>


                        <div class="stat-bottom">

                            <span>
                                <?= $pendingReservations ?>
                                pending
                            </span>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 EXTRA QUICK STATS
            ================================================== -->

            <div class="row g-4 mb-4">


                <div class="col-xl-4 col-md-6">

                    <div class="stat-card">

                        <div class="stat-card-top">

                            <div class="stat-icon">

                                <i class="bi bi-journal-text"></i>

                            </div>

                        </div>

                        <p>
                            Menu Items
                        </p>

                        <h2>
                            <?= number_format(
                                $totalMenuItems
                            ) ?>
                        </h2>

                        <div class="stat-bottom">

                            <span>
                                Available menu items
                            </span>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                    </div>

                </div>


                <div class="col-xl-4 col-md-6">

                    <div class="stat-card">

                        <div class="stat-card-top">

                            <div class="stat-icon">

                                <i class="bi bi-envelope"></i>

                            </div>

                        </div>

                        <p>
                            Contact Messages
                        </p>

                        <h2>
                            <?= number_format(
                                $totalMessages
                            ) ?>
                        </h2>

                        <div class="stat-bottom">

                            <span>
                                Customer messages
                            </span>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                    </div>

                </div>


                <div class="col-xl-4 col-md-12">

                    <div class="stat-card">

                        <div class="stat-card-top">

                            <div class="stat-icon">

                                <i class="bi bi-check-circle"></i>

                            </div>

                        </div>

                        <p>
                            Completed Orders
                        </p>

                        <h2>
                            <?= number_format(
                                $completedOrders
                            ) ?>
                        </h2>

                        <div class="stat-bottom">

                            <span>
                                <?= $cancelledOrders ?>
                                cancelled
                            </span>

                            <i class="bi bi-arrow-up-right"></i>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 CHARTS
            ================================================== -->

            <div class="row g-4 mb-4">


                <!-- REVENUE CHART -->

                <div class="col-xl-8">

                    <div class="dashboard-card">

                        <div class="card-header-custom">

                            <div>

                                <h5>
                                    Revenue Overview
                                </h5>

                                <p>
                                    Revenue from orders
                                </p>

                            </div>


                            <span
                                class="small">

                                Last 12 Months

                            </span>

                        </div>


                        <div class="chart-area">

                            <canvas
                                id="revenueChart">
                            </canvas>

                        </div>

                    </div>

                </div>


                <!-- ORDER SUMMARY -->

                <div class="col-xl-4">

                    <div class="dashboard-card">

                        <div class="card-header-custom">

                            <div>

                                <h5>
                                    Order Summary
                                </h5>

                                <p>
                                    All order statuses
                                </p>

                            </div>

                        </div>


                        <div class="donut-container">

                            <canvas
                                id="orderChart">
                            </canvas>


                            <div class="donut-text">

                                <strong>
                                    <?= number_format(
                                        $totalOrders
                                    ) ?>
                                </strong>

                                <span>
                                    Total Orders
                                </span>

                            </div>

                        </div>


                        <div class="order-summary">


                            <div>

                                <span class="dot gold"></span>

                                Completed

                                <b>
                                    <?= $completedOrders ?>
                                </b>

                            </div>


                            <div>

                                <span class="dot yellow"></span>

                                Pending

                                <b>
                                    <?= $pendingOrders ?>
                                </b>

                            </div>


                            <div>

                                <span class="dot red"></span>

                                Cancelled

                                <b>
                                    <?= $cancelledOrders ?>
                                </b>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 RESERVATION SUMMARY
            ================================================== -->

            <div class="row g-4 mb-4">


                <div class="col-md-4">

                    <div class="dashboard-card p-4">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="fw-bold">
                                    Pending Reservations
                                </small>

                                <h3 class="mt-2 mb-0">
                                    <?= $pendingReservations ?>
                                </h3>

                            </div>

                            <div class="stat-icon">

                                <i class="bi bi-clock"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="dashboard-card p-4">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="fw-bold">
                                    Confirmed Reservations
                                </small>

                                <h3 class="mt-2 mb-0">
                                    <?= $confirmedReservations ?>
                                </h3>

                            </div>

                            <div class="stat-icon">

                                <i class="bi bi-check-circle"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="dashboard-card p-4">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="fw-bold">
                                    Today's Reservations
                                </small>

                                <h3 class="mt-2 mb-0">
                                    <?= $todayReservations ?>
                                </h3>

                            </div>

                            <div class="stat-icon">

                                <i class="bi bi-calendar-day"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 RECENT ORDERS
            ================================================== -->

            <div class="dashboard-card">


                <div class="card-header-custom">

                    <div>

                        <h5>
                            Recent Orders
                        </h5>

                        <p>
                            Latest customer orders
                        </p>

                    </div>


                    <a
                        href="orders.php"
                        class="outline-gold-btn text-decoration-none">

                        View All

                    </a>

                </div>


                <div class="table-responsive">

                    <table class="table restaurant-table">

                        <thead>

                            <tr>

                                <th>
                                    ORDER ID
                                </th>

                                <th>
                                    CUSTOMER
                                </th>

                                <th>
                                    ITEMS
                                </th>

                                <th>
                                    AMOUNT
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    DATE
                                </th>

                                <th>
                                    ACTION
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php if (!empty($recentOrders)): ?>


                                <?php foreach (
                                    $recentOrders
                                    as $order
                                ): ?>


                                    <?php

                                    $orderId =
                                        (int)$order["id"];

                                    $customerName =
                                        trim(
                                            $order["full_name"]
                                            ?? "Customer"
                                        );

                                    $customerNameEscaped =
                                        htmlspecialchars(
                                            $customerName,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                    $customerInitials =
                                        "CU";

                                    $parts =
                                        preg_split(
                                            "/\s+/",
                                            $customerName
                                        );

                                    if (
                                        count($parts) >= 2
                                    ) {

                                        $customerInitials =
                                            strtoupper(
                                                substr(
                                                    $parts[0],
                                                    0,
                                                    1
                                                ) .
                                                substr(
                                                    $parts[
                                                        count($parts) - 1
                                                    ],
                                                    0,
                                                    1
                                                )
                                            );

                                    } elseif (
                                        !empty($parts[0])
                                    ) {

                                        $customerInitials =
                                            strtoupper(
                                                substr(
                                                    $parts[0],
                                                    0,
                                                    2
                                                )
                                            );
                                    }


                                    $itemsCount =
                                        $orderItemCounts[
                                            $orderId
                                        ] ?? 0;


                                    $statusClass =
                                        dashboardStatusClass(
                                            $order["status"]
                                        );


                                    $amount =
                                        (float)$order[
                                            "total_amount"
                                        ];


                                    $createdAt =
                                        !empty(
                                            $order["created_at"]
                                        )
                                            ? date(
                                                "d M Y",
                                                strtotime(
                                                    $order["created_at"]
                                                )
                                            )
                                            : "-";

                                    ?>


                                    <tr>


                                        <td>

                                            <strong>
                                                #ORD-<?= $orderId ?>
                                            </strong>

                                        </td>


                                        <td>

                                            <div class="customer">

                                                <span>
                                                    <?= htmlspecialchars(
                                                        $customerInitials,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>
                                                </span>

                                                <?= $customerNameEscaped ?>

                                            </div>

                                        </td>


                                        <td>

                                            <?= $itemsCount ?>

                                            <?= $itemsCount === 1
                                                ? "Item"
                                                : "Items" ?>

                                        </td>


                                        <td>

                                            <?= number_format(
                                                $amount,
                                                2
                                            ) ?>

                                        </td>


                                        <td>

                                            <span
                                                class="status <?= $statusClass ?>">

                                                <?= dashboardStatusLabel(
                                                    $order["status"]
                                                ) ?>

                                            </span>

                                        </td>


                                        <td>

                                            <?= $createdAt ?>

                                        </td>


                                        <td>

                                            <a
                                                href="order_view.php?id=<?= $orderId ?>"
                                                class="table-btn text-decoration-none">

                                                <i class="bi bi-eye"></i>

                                            </a>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-5">

                                        <i
                                            class="bi bi-receipt"
                                            style="
                                                font-size:32px;
                                                color:var(--gold);
                                            ">
                                        </i>

                                        <div class="mt-2">

                                            No orders found.

                                        </div>

                                    </td>

                                </tr>


                            <?php endif; ?>


                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <footer class="footer">

                <p>
                    © 2026 Bite & Bliss.
                    All rights reserved.
                </p>

                <p>

                    Developed by

                    <strong>
                        Qamar Idrees &
                        Muhammad Bilal Waris
                    </strong>

                </p>

            </footer>


        </section>

    </main>

</div>


<!-- =====================================================
     CHART.JS
====================================================== -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>


<script>

/*
|--------------------------------------------------------------------------
| REVENUE CHART DATA
|--------------------------------------------------------------------------
*/

const revenueLabels =
    <?= json_encode($chartLabels) ?>;

const revenueValues =
    <?= json_encode($chartValues) ?>;


/*
|--------------------------------------------------------------------------
| ORDER DATA
|--------------------------------------------------------------------------
*/

const completedOrders =
    <?= (int)$completedOrders ?>;

const pendingOrders =
    <?= (int)$pendingOrders ?>;

const cancelledOrders =
    <?= (int)$cancelledOrders ?>;


/*
|--------------------------------------------------------------------------
| GET THEME COLORS
|--------------------------------------------------------------------------
*/

function getCSSVariable(variable)
{
    return getComputedStyle(
        document.documentElement
    )
        .getPropertyValue(variable)
        .trim();
}


const gold =
    getCSSVariable("--gold") ||
    "#d4af37";

const white =
    getCSSVariable("--white") ||
    "#ffffff";

const muted =
    getCSSVariable("--muted") ||
    "#999999";


/*
|--------------------------------------------------------------------------
| REVENUE CHART
|--------------------------------------------------------------------------
*/

const revenueCanvas =
    document.getElementById(
        "revenueChart"
    );


if (revenueCanvas) {

    new Chart(
        revenueCanvas,
        {

            type: "line",

            data: {

                labels: revenueLabels,

                datasets: [

                    {

                        label:
                            "Revenue",

                        data:
                            revenueValues,

                        borderColor:
                            gold,

                        backgroundColor:
                            "rgba(212,175,55,0.10)",

                        borderWidth: 2,

                        fill: true,

                        tension: 0.35,

                        pointRadius: 3,

                        pointHoverRadius: 5

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        callbacks: {

                            label:
                                function(context)
                                {

                                    return " Revenue: " +
                                        Number(
                                            context.raw
                                        ).toLocaleString(
                                            undefined,
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }
                                        );

                                }

                        }

                    }

                },


                scales: {

                    x: {

                        grid: {
                            display: false
                        },

                        ticks: {
                            color: muted
                        }

                    },


                    y: {

                        beginAtZero: true,

                        grid: {

                            color:
                                "rgba(255,255,255,0.06)"

                        },

                        ticks: {

                            color: muted,

                            callback:
                                function(value)
                                {

                                    return value
                                        .toLocaleString();

                                }

                        }

                    }

                }

            }

        }
    );
}


/*
|--------------------------------------------------------------------------
| ORDER DONUT
|--------------------------------------------------------------------------
*/

const orderCanvas =
    document.getElementById(
        "orderChart"
    );


if (orderCanvas) {

    new Chart(
        orderCanvas,
        {

            type: "doughnut",

            data: {

                labels: [

                    "Completed",
                    "Pending",
                    "Cancelled"

                ],

                datasets: [

                    {

                        data: [

                            completedOrders,
                            pendingOrders,
                            cancelledOrders

                        ],

                        backgroundColor: [

                            gold,
                            "#e8c75a",
                            "#d9534f"

                        ],

                        borderWidth: 0,

                        hoverOffset: 5

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: "72%",

                plugins: {

                    legend: {
                        display: false
                    }

                }

            }

        }
    );
}

</script>


<!-- ADMIN JAVASCRIPT -->

<script src="js/admin.js"></script>


</body>

</html>